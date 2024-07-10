<?php

namespace App\Livewire\Materias;

use App\Livewire\Forms\MateriaForm;
use App\Models\Materia;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public MateriaForm $form;

    public function mount(Materia $materia)
    {
        $this->form->setMateriaModel($materia);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.materia.show', ['materia' => $this->form->materiaModel]);
    }
}
