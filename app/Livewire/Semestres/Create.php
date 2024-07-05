<?php

namespace App\Livewire\Semestres;

use App\Livewire\Forms\SemestreForm;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public SemestreForm $form;

    public function mount(Semestre $semestre)
    {
        $this->form->setSemestreModel($semestre);
    }

    public function save()
    {
        $this->form->store();

        return $this->redirectRoute('semestres.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.semestre.create');
    }
}
