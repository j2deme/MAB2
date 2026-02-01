<?php

namespace App\Livewire\Grupos;

use App\Livewire\Forms\GrupoForm;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Traits\UsesSemestreActivo;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;
    use UsesSemestreActivo;
    public GrupoForm $form;
    public Semestre $semestre;

    public function mount(Grupo $grupo)
    {
        $semestre                = $this->getSemestreActivo();
        $grupo->semestre_id      = $semestre->id;
        $grupo->is_disponible    = true;
        $grupo->is_paralelizable = false;
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
