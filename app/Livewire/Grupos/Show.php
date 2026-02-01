<?php

namespace App\Livewire\Grupos;

use App\Livewire\Forms\GrupoForm;
use App\Models\Grupo;
use App\Traits\UsesSemestreActivo;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    use UsesSemestreActivo;

    public GrupoForm $form;
    public bool $mostrarTodos = false;

    public function mount(Grupo $grupo)
    {
        $this->form->setGrupoModel($grupo);
    }

    public function mostrarTodos(): void
    {
        $this->mostrarTodos = true;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $grupo = $this->form->grupoModel;

        // Movimientos a mostrar (todos o primeros 10) con paginación
        $movimientos = $grupo->movimientos()
            ->with('user')
            ->where('deleted_at', null)
            ->latest('updated_at')
            ->when(!$this->mostrarTodos, fn($q) => $q->take(10))
            ->get();

        // Stats calculados en BD (optimizados para escalabilidad)
        $totalEstudiantes = $grupo->movimientos()
            ->selectRaw('COUNT(DISTINCT user_id) as total')
            ->where('deleted_at', null)
            ->value('total') ?? 0;

        $totalMovimientos = $grupo->movimientos()
            ->where('deleted_at', null)
            ->count();

        $altasAprobadas = $grupo->movimientos()
            ->where('tipo', 'ALTA')
            ->where('deleted_at', null)
            ->count();

        $bajasAprobadas = $grupo->movimientos()
            ->where('tipo', 'BAJA')
            ->where('deleted_at', null)
            ->count();

        return view('livewire.grupo.show', [
            'grupo' => $grupo,
            'movimientos' => $movimientos,
            'totalEstudiantes' => $totalEstudiantes,
            'totalMovimientos' => $totalMovimientos,
            'altasAprobadas' => $altasAprobadas,
            'bajasAprobadas' => $bajasAprobadas,
        ]);
    }
}
