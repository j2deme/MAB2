<?php

namespace App\Livewire\Users;

use App\Enums\UserRoles;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public UserForm $form;
    public $movimientosRecientes = [];
    public $carrerasAsignadas = [];
    public $movimientosEstadisticas = [];

    public function mount(User $user)
    {
        $this->form->setUserModel($user);
        $this->cargarDatos($user);
    }

    private function cargarDatos(User $user)
    {
        // Cargar movimientos recientes
        $this->movimientosRecientes = $user->movimientos()
            ->with(['grupo.materia.carrera', 'user'])
            ->where('deleted_at', null)
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Cargar carreras asignadas (para coordinadores y jefes)
        if ($user->es(['Coordinador', 'Jefe', 'Administrador'])) {
            $this->carrerasAsignadas = $user->carreras()
                ->withCount('materias')
                ->get();
        }

        // Cargar estadísticas de movimientos
        $this->movimientosEstadisticas = [
            'total' => $user->movimientos()->count(),
            'altas' => $user->movimientos()->whereIn('tipo', ['Alta', 'ALTA'])->count(),
            'bajas' => $user->movimientos()->whereIn('tipo', ['Baja', 'BAJA'])->count(),
            'cambios' => $user->movimientos()->whereIn('tipo', ['Cambio', 'CAMBIO'])->count(),
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.user.show', [
            'user' => $this->form->userModel,
            'movimientosRecientes' => $this->movimientosRecientes,
            'carrerasAsignadas' => $this->carrerasAsignadas,
            'movimientosEstadisticas' => $this->movimientosEstadisticas,
        ]);
    }
}
