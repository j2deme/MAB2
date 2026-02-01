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

        // Movimientos del grupo (altas/bajas con estatus) - TODOS para stats
        $movimientosCompletos = $grupo->movimientos()
            ->with('user')
            ->where('deleted_at', null)
            ->latest('updated_at')
            ->get();

        // Movimientos a mostrar (todos o primeros 10)
        $movimientos = $this->mostrarTodos
            ? $movimientosCompletos
            : $movimientosCompletos->take(10);

        // Estudiantes registrados (distinct users con movimientos)
        $totalEstudiantes = $movimientosCompletos->pluck('user_id')->unique()->count();

        // Stats
        $totalMovimientos = $movimientosCompletos->count();
        $altasAprobadas   = $movimientosCompletos->filter(fn($m) => $m->tipo->value === 'Alta')->count();
        $bajasAprobadas   = $movimientosCompletos->filter(fn($m) => $m->tipo->value === 'Baja')->count();

        return view('livewire.grupo.show', [
            'grupo' => $grupo,
            'movimientos' => $movimientos,
            'movimientosCompletos' => $movimientosCompletos,
            'totalEstudiantes' => $totalEstudiantes,
            'totalMovimientos' => $totalMovimientos,
            'altasAprobadas' => $altasAprobadas,
            'bajasAprobadas' => $bajasAprobadas,
        ]);
    }
}
