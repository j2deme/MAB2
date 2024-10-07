<?php

namespace App\Livewire\Actividades;

use App\Livewire\Forms\ActividadForm;
use App\Models\Actividad;
use App\Models\Evento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public ActividadForm $form;
    public $eventos = [];

    public function mount(Actividad $actividad)
    {
        $this->eventos = Evento::all()->where('is_activo', true);
        $this->form->setActividadModel($actividad);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Actividade actualizado correctamente.');

        return $this->redirectRoute('actividades.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.actividad.edit');
    }
}
