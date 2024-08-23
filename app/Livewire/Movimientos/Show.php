<?php

namespace App\Livewire\Movimientos;

use App\Livewire\Forms\MovimientoForm;
use App\Models\Movimiento;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public MovimientoForm $form;

    public function mount(Movimiento $movimiento)
    {
        $this->form->setMovimientoModel($movimiento);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.show', ['movimiento' => $this->form->movimientoModel]);
    }
}
