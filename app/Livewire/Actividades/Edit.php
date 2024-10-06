<?php

namespace App\Livewire\Actividades;

use App\Livewire\Forms\ActividadForm;
use App\Models\Actividad;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public ActividadForm $form;

    public function mount(Actividad $actividad)
    {
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
        return view('livewire.actividade.edit');
    }
}
