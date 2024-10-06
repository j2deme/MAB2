<?php

namespace App\Livewire\Actividades;

use App\Livewire\Forms\ActividadForm;
use App\Models\Actividad;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public ActividadForm $form;

    public function mount(Actividad $actividad)
    {
        $this->form->setActividadModel($actividad);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.actividade.show', ['actividad' => $this->form->actividadModel]);
    }
}
