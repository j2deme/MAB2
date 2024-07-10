<?php

namespace App\Livewire\Carreras;

use App\Livewire\Forms\CarreraForm;
use App\Models\Carrera;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public CarreraForm $form;

    public function mount(Carrera $carrera)
    {
        $this->form->setCarreraModel($carrera);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Carrera actualizada correctamente.');

        return $this->redirectRoute('carreras.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.carrera.edit');
    }
}
