<?php

namespace App\Livewire\Asistencias;

use App\Livewire\Forms\AsistenciaForm;
use App\Models\Asistencia;
use App\Models\Evento;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Magistral extends Component
{
    use WireUiActions;
    public AsistenciaForm $form;
    public $actividades = [];
    public $users = [];
    public $evento;
    public $magistral;

    public function mount(Asistencia $asistencia)
    {
        $this->form->setAsistenciaModel($asistencia);

        $evento    = Evento::where('is_activo', true)->first();
        $magistral = $evento->actividades()->where('is_magistral', true)->first();

        $this->actividades        = $evento->actividades()->get();
        $this->form->actividad_id = $magistral->id;

        $this->evento    = $evento;
        $this->magistral = $magistral;

        $this->users = User::where('rol', '=', \App\Enums\UserRoles::ESTUDIANTE)->get();
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Asistencia agregado correctamente.');

        return $this->redirectRoute('asistencias.magistral', navigate: true);
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.asistencia.magistral');
    }
}
