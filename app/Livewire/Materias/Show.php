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

        // Grupos de esta materia en el semestre activo
        $grupos = $materia->grupos()
            ->where('semestre_id', $semestreActivo?->id)
            ->with(['movimientos'])
            ->get();

        // Contar estudiantes por grupo
        $gruposConEstudiantes = $grupos->map(function ($grupo) {
            $movimientos = $grupo->movimientos()
                ->whereNull('movimientos.deleted_at')
                ->get();

            return [
                'grupo' => $grupo,
                'estudiantes' => $movimientos->pluck('user_id')->unique()->count(),
                'altas' => $movimientos->filter(fn($m) => $m->tipo->value === 'Alta')->pluck('user_id')->unique()->count(),
                'bajas' => $movimientos->filter(fn($m) => $m->tipo->value === 'Baja')->pluck('user_id')->unique()->count(),
            ];
        });

        // Stats totales de la materia (solo del semestre activo)
        $movimientosTotal = $materia->movimientos()
            ->whereNull('movimientos.deleted_at')
            ->whereHas('grupo', fn($q) => $q->where('semestre_id', $semestreActivo?->id))
            ->get();

        $movimientosTotales = $movimientosTotal->count();

        $altasTotales = $movimientosTotal
            ->filter(fn($m) => $m->tipo->value === 'Alta')
            ->count();

        $bajasTotales = $movimientosTotal
            ->filter(fn($m) => $m->tipo->value === 'Baja')
            ->count();

        $estudiantesRegistrados = $movimientosTotal
            ->pluck('user_id')
            ->unique()
            ->count();

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
