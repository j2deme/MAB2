<?php

namespace App\Livewire\Materias;

use App\Livewire\Forms\MateriaForm;
use App\Models\Materia;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public MateriaForm $form;

    public function mount(Materia $materia)
    {
        $this->form->setMateriaModel($materia);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Materia actualizada correctamente.');

        return $this->redirectRoute('materias.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.materia.edit');
    }
}
