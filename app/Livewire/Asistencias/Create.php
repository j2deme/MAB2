<?php

namespace App\Livewire\Asistencias;

use App\Livewire\Forms\AsistenciaForm;
use App\Models\Asistencia;
use App\Models\Evento;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;
    public AsistenciaForm $form;

    public $actividades = [];
    public $users = [];

    public function mount(Asistencia $asistencia)
    {
        $this->form->setAsistenciaModel($asistencia);

        $evento_activo     = Evento::where('is_activo', true)->first();
        $this->actividades = $evento_activo->actividades()->get();

        $this->users = User::where('rol', '=', \App\Enums\UserRoles::ESTUDIANTE)->get();
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Asistencia agregado correctamente.');

        return $this->redirectRoute('asistencias.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.asistencia.create');
    }
}
