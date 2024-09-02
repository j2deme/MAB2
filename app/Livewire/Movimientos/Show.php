<?php

namespace App\Livewire\Movimientos;

use App\Livewire\Forms\MovimientoForm;
use App\Models\Movimiento;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public MovimientoForm $form;

    public Semestre $semestre;

    public function mount(Movimiento $movimiento)
    {
        $this->form->setMovimientoModel($movimiento);
        $this->semestre = $this->form->movimientoModel->semestre;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.show', ['movimiento' => $this->form->movimientoModel]);
    }
}
