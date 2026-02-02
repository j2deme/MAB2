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
use Illuminate\Support\Facades\Cache;

class ListaMaterias extends Component
{
    use WireUiActions;

    public ?Semestre $semestre = null;
    public $semestres;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();
        $semestreId     = $this->semestre?->id;

        if (!$semestreId) {
            $this->semestres = collect();
            return;
        }

        // Generar clave de cache única por usuario y filtro de carreras
        $userId         = Auth::id();
        $esCoordinador  = Auth::user()->es('Coordinador');
        $carrerasFilter = $esCoordinador ? implode(',', Auth::user()->carreras->pluck('id')->toArray()) : 'todos';
        $cacheKey       = "lista_materias_sem_{$semestreId}_user_{$userId}_carr_{$carrerasFilter}";

        // Cache estructura (materias, grupos) por 24h
        $this->semestres = Cache::remember($cacheKey, 24 * 3600, function () use ($semestreId, $esCoordinador) {
            // Minimizar columnas consultadas
            $query = \App\Models\Materia::query()
                ->select('id', 'clave', 'nombre', 'nombre_completo', 'carrera_id', 'semestre')
                ->whereHas('grupos', fn($q) => $q->where('semestre_id', $semestreId))
                ->with([
                    'carrera' => fn($q) => $q->select('id', 'color', 'nombre', 'siglas'),
                    'grupos' => fn($q) => $q->where('semestre_id', $semestreId)->select('id', 'materia_id', 'siglas', 'semestre_id')
                ]);

            if ($esCoordinador) {
                $carrerasIds = Auth::user()->carreras->pluck('id')->toArray();
                $query->whereIn('carrera_id', $carrerasIds);
            }

            $materias = $query->get();

            return $materias->groupBy('semestre')->map(function ($materiasDelSemestre) {
                return $materiasDelSemestre->keyBy('clave')->map(function ($materia) {
                    $primerGrupo = $materia->grupos->first();
                    return (object) [
                        'grupo_id' => $primerGrupo?->id,
                        'materia_id' => $materia->id,
                        'clave' => $materia->clave,
                        'nombre' => $materia->nombre,
                        'nombre_completo' => $materia->nombre_completo,
                        'semestre' => $materia->semestre,
                        'carrera_color' => $materia->carrera?->color,
                        'carrera_nombre' => $materia->carrera?->nombre,
                        'carrera_siglas' => $materia->carrera?->siglas,
                    ];
                })->map(fn($v) => collect([$v]));
            })->sortKeys();
        });

        // Cache conteos por 5 min (cambian más frecuentemente)
        $this->injectCounts($semestreId);
    }


    private function injectCounts($semestreId): void
    {
        $cacheKeyCountsBase = "lista_materias_counts_sem_{$semestreId}";
        $counts             = Cache::remember($cacheKeyCountsBase, 5 * 60, function () use ($semestreId) {
            return \App\Models\Materia::query()
                ->select('id')
                ->whereHas('grupos', fn($q) => $q->where('semestre_id', $semestreId))
                ->withCount([
                    'movimientos as total_movimientos_count' => fn($q) => $q->where('movimientos.semestre_id', $semestreId),
                    'movimientos as pendientes_count' => fn($q) => $q->where('movimientos.semestre_id', $semestreId)->whereIn('movimientos.estatus', ['Registrado', 'En revisión']),
                ])
                ->get()
                ->keyBy('id');
        });

        // Inyectar conteos en estructura cacheada
        foreach ($this->semestres as $numSem => $materias) {
            foreach ($materias as $clave => $coleccion) {
                foreach ($coleccion as $idx => $materia) {
                    $countData           = $counts->get($materia->materia_id);
                    $materia->total      = $countData?->total_movimientos_count ?? 0;
                    $materia->pendientes = $countData?->pendientes_count ?? 0;
                }
            }
        }
    }

    public static function invalidateCache($semestreId): void
    {
        Cache::forget("lista_materias_counts_sem_{$semestreId}");
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-materias');
    }
}
