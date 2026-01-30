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

        // Consulta SQL directa para obtener materias, grupos y movimientos del semestre activo
        $results = \DB::select("
        SELECT m.id as materia_id, m.clave, m.nombre, m.nombre_completo, m.semestre as materia_semestre, m.carrera_id,
        g.id as grupo_id, g.semestre_id,
        c.id as carrera_id, c.color as carrera_color, c.nombre as carrera_nombre, c.siglas as carrera_siglas,
        mo.id as movimiento_id, mo.estatus, mo.is_paralelo
        FROM materias m
        INNER JOIN grupos g ON g.materia_id = m.id AND g.semestre_id = ?
        INNER JOIN carreras c ON c.id = m.carrera_id
        INNER JOIN movimientos mo ON mo.grupo_id = g.id AND mo.semestre_id = ? AND (mo.deleted_at IS NULL)
        ", [$this->semestre->id, $this->semestre->id]);

        $semestres = [];
        foreach ($results as $row) {
            $pos   = $row->materia_semestre;
            $clave = $row->clave;

            // Filtrar por coordinador
            if (auth()->user()->es('Coordinador')) {
                $materiaCarreraId = $row->carrera_id;
                $carrerasIds      = auth()->user()->carreras->pluck('id')->toArray();
                if (!in_array($materiaCarreraId, $carrerasIds)) {
                    continue;
                }
            }

            if (!isset($semestres[$pos][$clave])) {
                $semestres[$pos][$clave] = collect();
            }

            // Crear objeto en lugar de array
            $semestres[$pos][$clave]->push((object) [
                'grupo_id' => $row->grupo_id,
                'materia_id' => $row->materia_id,
                'clave' => $row->clave,
                'nombre' => $row->nombre,
                'nombre_completo' => $row->nombre_completo,
                'semestre' => $row->materia_semestre,
                'estatus' => $row->estatus ?? null,
                'is_paralelo' => $row->is_paralelo ?? null,
                'total' => $row->movimiento_id ? 1 : 0,
                'carrera_color' => $row->carrera_color,
                'carrera_nombre' => $row->carrera_nombre,
                'carrera_siglas' => $row->carrera_siglas,
            ]);
        }

        // Convertir arrays internos a colecciones
        foreach ($semestres as $semestreKey => $materias) {
            foreach ($materias as $claveMateria => $grupos) {
                // Asegurar que sea una colección
                if (!$grupos instanceof \Illuminate\Support\Collection) {
                    $semestres[$semestreKey][$claveMateria] = collect($grupos);
                }
            }
            $semestres[$semestreKey] = collect($materias);
        }

        $this->semestres = collect($semestres)->sortKeys();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-materias');
    }
}
