<?php

namespace App\Livewire\Materias;

use App\Enums\MovesType;
use App\Livewire\Forms\MateriaForm;
use App\Models\Materia;
use App\Traits\UsesSemestreActivo;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    use UsesSemestreActivo;

    public MateriaForm $form;

    public function mount(Materia $materia)
    {
        $this->form->setMateriaModel($materia);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $materia        = $this->form->materiaModel;
        $semestreActivo = $this->getSemestreActivo();

        // Grupos de esta materia en el semestre activo con conteos agregados
        $grupos = $materia->grupos()
            ->where('semestre_id', $semestreActivo?->id)
            ->withCount(['movimientos as estudiantes_count' => fn($q) => $q->selectRaw('COUNT(DISTINCT user_id)')->whereNull('movimientos.deleted_at')])
            ->withCount(['movimientos as altas_count' => fn($q) => $q->where('tipo', 'ALTA')->whereNull('movimientos.deleted_at')])
            ->withCount(['movimientos as bajas_count' => fn($q) => $q->where('tipo', 'BAJA')->whereNull('movimientos.deleted_at')])
            ->get();

        // Mapear grupos con conteos ya calculados en BD
        $gruposConEstudiantes = $grupos->map(function ($grupo) {
            return [
                'grupo' => $grupo,
                'estudiantes' => $grupo->estudiantes_count,
                'altas' => $grupo->altas_count,
                'bajas' => $grupo->bajas_count,
            ];
        });

        // Stats totales de la materia (solo del semestre activo) - calculadas en BD
        $movimientosTotales = $materia->movimientos()
            ->whereNull('movimientos.deleted_at')
            ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo?->id))
            ->count();

        $altasTotales = $materia->movimientos()
            ->where('tipo', 'ALTA')
            ->whereNull('movimientos.deleted_at')
            ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo?->id))
            ->count();

        $bajasTotales = $materia->movimientos()
            ->where('tipo', 'BAJA')
            ->whereNull('movimientos.deleted_at')
            ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo?->id))
            ->count();

        $estudiantesRegistrados = $materia->movimientos()
            ->selectRaw('COUNT(DISTINCT user_id) as total')
            ->whereNull('movimientos.deleted_at')
            ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo?->id))
            ->value('total') ?? 0;

        return view('livewire.materia.show', [
            'materia' => $materia,
            'gruposConEstudiantes' => $gruposConEstudiantes,
            'semestreActivo' => $semestreActivo,
            'movimientosTotales' => $movimientosTotales,
            'altasTotales' => $altasTotales,
            'bajasTotales' => $bajasTotales,
            'estudiantesRegistrados' => $estudiantesRegistrados,
        ]);
    }
}
