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

class ListaMaterias extends Component
{
    use WireUiActions;

    public Semestre $semestre;

    public $semestres;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();

        $semestreId = $this->semestre?->id;

        // Obtén materias que tienen grupos en el semestre y agrega conteos para movimientos
        $query = \App\Models\Materia::query()
            ->whereHas('grupos', fn($q) => $q->where('semestre_id', $semestreId))
            ->with(['carrera', 'grupos' => fn($q) => $q->where('semestre_id', $semestreId)->select('id', 'materia_id', 'siglas', 'semestre_id')])
            ->withCount([
                'movimientos as total_movimientos_count' => fn($q) => $q->where('movimientos.semestre_id', $semestreId),
                'movimientos as pendientes_count' => fn($q) => $q->where('movimientos.semestre_id', $semestreId)->whereIn('movimientos.estatus', ['Registrado', 'En revisión']),
            ]);

        // Si es coordinador, limitar por sus carreras
        if (Auth::check() && Auth::user()->es('Coordinador')) {
            $carrerasIds = Auth::user()->carreras->pluck('id')->toArray();
            $query->whereIn('carrera_id', $carrerasIds);
        }

        $materias = $query->get();

        // Agrupar por semestre (atributo de materia)
        $grouped = $materias->groupBy('semestre')->map(function ($materiasDelSemestre) {
            return $materiasDelSemestre->keyBy('clave')->map(function ($materia) {
                // Tomar el primer grupo disponible en ese semestre
                $primerGrupo = $materia->grupos->first();

                return (object) [
                    'grupo_id' => $primerGrupo?->id,
                    'materia_id' => $materia->id,
                    'clave' => $materia->clave,
                    'nombre' => $materia->nombre,
                    'nombre_completo' => $materia->nombre_completo,
                    'semestre' => $materia->semestre,
                    'estatus' => null,
                    'is_paralelo' => null,
                    'total' => $materia->total_movimientos_count ?? 0,
                    'pendientes' => $materia->pendientes_count ?? 0,
                    'carrera_color' => $materia->carrera?->color,
                    'carrera_nombre' => $materia->carrera?->nombre,
                    'carrera_siglas' => $materia->carrera?->siglas,
                ];
            })->map(function ($v) {
                // Mantener la compatibilidad con la vista original que espera una colección de "grupos"
                // por cada clave; devolvemos una colección con un único objeto.
                return collect([$v]);
            });
        });

        $this->semestres = collect($grouped)->sortKeys();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-materias');
    }
}
