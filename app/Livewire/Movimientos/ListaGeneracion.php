<?php

namespace App\Livewire\Movimientos;
ini_set('max_execution_time', 1000);

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\Semestre;
use Illuminate\Database\Eloquent\Collection;
use Auth;
use App\Models\User;
use App\Models\Movimiento;
use App\Enums\MovesStatus;
use Illuminate\Support\Facades\Cache;

class ListaGeneracion extends Component
{
    use WireUiActions;

    public Semestre $semestre;

    public $generaciones;
    // Opciones (sin filtros)
    public $carrerasOptions = null;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();
        $semestreId     = $this->semestre?->id;

        // Inicializar options
        if (Auth::user()->es('Coordinador')) {
            $this->carrerasOptions = Auth::user()->carreras;
        } else {
            $this->carrerasOptions = \App\Models\Carrera::query()->orderBy('siglas')->get();
        }
        if (!$semestreId) {
            $this->generaciones = collect();
            return;
        }

        $this->loadStructure();
    }

    private function loadStructure(): void
    {
        $semestreId = $this->semestre?->id;

        // Generar clave de cache única por usuario (coordinador tiene filtro diferente)
        $userId         = Auth::id();
        $esCoordinador  = Auth::user()->es('Coordinador');
        $carrerasFilter = $esCoordinador ? implode(',', Auth::user()->carreras->pluck('id')->toArray()) : 'todos';
        $cacheKey       = "lista_generacion_sem_{$semestreId}_user_{$userId}_carr_{$carrerasFilter}";
        $useCache       = true;

        $build = function () use ($semestreId, $esCoordinador) {
            // Obtener user_ids únicos con movimientos en el semestre
            $userIds = Movimiento::query()
                ->where('semestre_id', $semestreId)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            if (empty($userIds)) {
                return collect();
            }

            // Minimizar columnas en User select
            $statuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION];

            $usersQuery = User::query()
                ->select('id', 'username', 'name')
                ->whereIn('id', $userIds)
                ->with('carreras:id,nombre,siglas,color');

            // No per-request search or carrera filters in this cached build

            if ($esCoordinador) {
                $carrerasIds = Auth::user()->carreras->pluck('id')->toArray();
                if (!empty($carrerasIds)) {
                    $usersQuery->whereHas('carreras', fn($q) => $q->whereIn('carreras.id', $carrerasIds));
                    $usersQuery->withCount(['movimientos as total' => fn($q) => $q->where('semestre_id', $semestreId)->whereIn('estatus', $statuses)->where('is_paralelo', false)]);
                }
            } else {
                $usersQuery->withCount(['movimientos as total' => fn($q) => $q->where('semestre_id', $semestreId)->whereIn('estatus', $statuses)]);
            }

            $users = $usersQuery->get();

            $generaciones = [];

            foreach ($users as $estudiante) {
                $pos = is_numeric(substr($estudiante->username, 0, 2)) ? substr($estudiante->username, 0, 2) : substr($estudiante->username, 1, 2);

                if ($esCoordinador) {
                    $firstCarr = $estudiante->carreras->first();
                    if (!$firstCarr || !Auth::user()->carreras->contains($firstCarr->id)) {
                        continue;
                    }
                }

                $estudiante->total                         = $estudiante->total ?? 0;
                $generaciones[$pos][$estudiante->username] = $estudiante;
            }

            return collect($generaciones)->sortKeys();
        };

        if ($useCache) {
            $this->generaciones = Cache::remember($cacheKey, 24 * 3600, $build);
        } else {
            $this->generaciones = $build();
        }

        // No aplicar filtros: mantenemos la estructura cacheada completa

        // Cache conteos por 5 min (cambian más frecuentemente)
        $this->injectCounts($semestreId);
    }

    private function injectCounts($semestreId): void
    {
        $userId = Auth::id();
        $esCoordinador = Auth::user()->es('Coordinador');
        $carrerasFilter = $esCoordinador ? implode(',', Auth::user()->carreras->pluck('id')->toArray()) : 'todos';
        $cacheKeyCountsBase = "lista_generacion_counts_sem_{$semestreId}_user_{$userId}_carr_{$carrerasFilter}";
        $counts             = Cache::remember($cacheKeyCountsBase, 5 * 60, function () use ($semestreId, $esCoordinador) {
            $statuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION];
            return Movimiento::query()
                ->select('user_id')
                ->where('semestre_id', $semestreId)
                ->whereNotNull('user_id')
                ->whereIn('estatus', $statuses)
                ->when($esCoordinador, fn($q) => $q->where('is_paralelo', false))
                ->groupBy('user_id')
                ->selectRaw('count(*) as total')
                ->pluck('total', 'user_id');
        });

        // Inyectar conteos actualizados
        foreach ($this->generaciones as $pos => $usuarios) {
            foreach ($usuarios as $username => $usuario) {
                $usuario->total = $counts->get($usuario->id, 0);
            }
        }
    }

    public static function invalidateCache($semestreId): void
    {
        Cache::forget("lista_generacion_counts_sem_{$semestreId}");
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-generacion');
    }
}
