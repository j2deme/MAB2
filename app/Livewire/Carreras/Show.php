<?php

namespace App\Livewire\Carreras;

use App\Livewire\Forms\CarreraForm;
use App\Models\Carrera;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public CarreraForm $form;

    public function mount(Carrera $carrera)
    {
        $this->form->setCarreraModel($carrera);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.carrera.show', ['carrera' => $this->form->carreraModel]);
    }
}
