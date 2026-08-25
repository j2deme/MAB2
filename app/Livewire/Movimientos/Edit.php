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

    public function updatedFormCarreraId($value): void
    {
        $this->form->refreshOptionsForCareer($value);
    }

    public function updatedFormMateriaId($value): void
    {
        $this->form->refreshOptionsForMateria($value);
    }

    public function mount(Movimiento $movimiento)
    {
        // Reload movimiento with the relations needed by the form to avoid N+1 on render
        $mov = Movimiento::with(['grupo.materia.carrera', 'user.carreras', 'asociado'])
            ->find($movimiento->id);

        $this->form->setMovimientoModel($mov ?? $movimiento);
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
            case 'movimientos.missing':
                return $this->redirectRoute('movimientos.missing', navigate: true);
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
