<?php

namespace App\Livewire\Grupos;

use App\Livewire\Forms\GrupoForm;
use App\Models\Grupo;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;
    public GrupoForm $form;

    public function mount(Grupo $grupo)
    {
        $this->form->setGrupoModel($grupo);
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Grupo agregado correctamente.');

        return $this->redirectRoute('grupos.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.grupo.create');
    }
}
