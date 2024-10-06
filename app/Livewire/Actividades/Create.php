<?php

namespace App\Livewire\Actividades;

use App\Livewire\Forms\ActividadForm;
use App\Models\Actividad;
use App\Models\Evento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;
    public ActividadForm $form;

    public $eventos = [];

    public function mount(Actividad $actividad)
    {
        $this->eventos = Evento::all()->where('is_activo', true);
        $this->form->setActividadModel($actividad);
        $this->form->is_activo    = true;
        $this->form->is_magistral = false;
        $this->form->descripcion  = 'N/A';
        $this->form->lugar        = 'N/A';
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Actividad agregada correctamente.');

        return $this->redirectRoute('actividades.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.actividad.create');
    }
}
