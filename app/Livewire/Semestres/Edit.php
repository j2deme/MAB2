<?php

namespace App\Livewire\Semestres;

use App\Livewire\Forms\SemestreForm;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public SemestreForm $form;

    public function mount(Semestre $semestre)
    {
        $this->form->setSemestreModel($semestre);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Semestre actualizado correctamente.');

        return $this->redirectRoute('semestres.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.semestre.edit');
    }
}
