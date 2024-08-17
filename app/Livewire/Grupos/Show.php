<?php

namespace App\Livewire\Grupos;

use App\Livewire\Forms\GrupoForm;
use App\Models\Grupo;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public GrupoForm $form;

    public function mount(Grupo $grupo)
    {
        $this->form->setGrupoModel($grupo);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.grupo.show', ['grupo' => $this->form->grupoModel]);
    }
}
