<?php

namespace App\Livewire\Asistencias;

use App\Livewire\Forms\AsistenciaForm;
use App\Models\Asistencia;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public AsistenciaForm $form;

    public function mount(Asistencia $asistencia)
    {
        $this->form->setAsistenciaModel($asistencia);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Asistencia actualizado correctamente.');

        return $this->redirectRoute('asistencias.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.asistencia.edit');
    }
}
