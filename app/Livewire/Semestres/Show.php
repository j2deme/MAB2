<?php

namespace App\Livewire\Semestres;

use App\Livewire\Forms\SemestreForm;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public SemestreForm $form;

    public function mount(Semestre $semestre)
    {
        $this->form->setSemestreModel($semestre);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.semestre.show', ['semestre' => $this->form->semestreModel]);
    }
}
