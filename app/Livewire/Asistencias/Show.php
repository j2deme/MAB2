<?php

namespace App\Livewire\Asistencias;

use App\Livewire\Forms\AsistenciaForm;
use App\Models\Asistencia;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public AsistenciaForm $form;

    public function mount(Asistencia $asistencia)
    {
        $this->form->setAsistenciaModel($asistencia);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.asistencia.show', ['asistencia' => $this->form->asistenciaModel]);
    }
}
