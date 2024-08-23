<?php

namespace App\Livewire\Movimientos;

use App\Livewire\Forms\MovimientoForm;
use App\Models\Movimiento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Edit extends Component
{
    use WireUiActions;
    public MovimientoForm $form;

    public function mount(Movimiento $movimiento)
    {
        $this->form->setMovimientoModel($movimiento);
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Movimiento actualizado correctamente.');

        return $this->redirectRoute('movimientos.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.edit');
    }
}
