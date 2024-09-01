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

        $this->dynamicRedirect();
    }

    private function dynamicRedirect()
    {
        switch ($this->form->backRoute) {
            case 'movimientos.index':
                return $this->redirectRoute('movimientos.index', navigate: true);
            case 'movimientos.pending':
                return $this->redirectRoute('movimientos.pending', navigate: true);
            case 'movimientos.attended':
                return $this->redirectRoute('movimientos.attended', navigate: true);
            case 'movimientos.materias':
                $materia = $this->form->movimientoModel->grupo->materia;
                return $this->redirectRoute('movimientos.materias.clave', $materia->clave, navigate: true);
            case 'movimientos.generacion':
                return $this->redirectRoute('movimientos.generacion.estudiante', $this->form->movimientoModel->user->username, navigate: true);
            default:
                return $this->redirectRoute('movimientos.index', navigate: true);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.edit');
    }
}
