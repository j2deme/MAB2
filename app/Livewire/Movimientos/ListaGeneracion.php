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

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();

        $semestreId = $this->semestre?->id;

        if (!$semestreId) {
            $this->generaciones = collect();
            return;
        }

        // Generar clave de cache única por usuario (coordinador tiene filtro diferente)
        $userId         = Auth::id();
        $esCoordinador  = Auth::user()->es('Coordinador');
        $carrerasFilter = $esCoordinador ? implode(',', Auth::user()->carreras->pluck('id')->toArray()) : 'todos';
        $cacheKey       = "lista_generacion_sem_{$semestreId}_user_{$userId}_carr_{$carrerasFilter}";

        // Cache estructura por 24h
        $this->generaciones = Cache::remember($cacheKey, 24 * 3600, function () use ($semestreId, $esCoordinador) {
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
        });

        // Cache conteos por 5 min (cambian más frecuentemente)
        $this->injectCounts($semestreId);
    }

    private function injectCounts($semestreId): void
    {
        $cacheKeyCountsBase = "lista_generacion_counts_sem_{$semestreId}";
        $counts             = Cache::remember($cacheKeyCountsBase, 5 * 60, function () use ($semestreId) {
            $statuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION];
            return Movimiento::query()
                ->select('user_id')
                ->where('semestre_id', $semestreId)
                ->whereNotNull('user_id')
                ->whereIn('estatus', $statuses)
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
