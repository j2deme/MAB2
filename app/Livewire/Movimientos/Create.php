<?php

namespace App\Livewire\Movimientos;

use App\Enums\MovesStatus;
use App\Enums\MovesType;
use App\Livewire\Forms\MovimientoForm;
use App\Models\Movimiento;
use App\Models\Semestre;
use Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Http\Request;

class Create extends Component
{
    use WireUiActions;
    public MovimientoForm $form;

    public function mount($tipo = null, Movimiento $movimiento)
    {
        $semestre                = Semestre::where('activo', true)->first();
        $movimiento->user_id     = Auth::user()->id;
        $movimiento->semestre_id = $semestre->id;
        $movimiento->estatus     = MovesStatus::REGISTRADO;
        $movimiento->is_paralelo = false;

        if (!is_null($tipo) and in_array($tipo, ['alta', 'baja'])) {
            $movimiento->tipo = match ($tipo) {
                'alta', 'Alta' => MovesType::ALTA,
                'baja', 'Baja' => MovesType::BAJA,
            };
        } else {
            $movimiento->tipo = null;
        }

        $this->form->setMovimientoModel($movimiento, $tipo);
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Movimiento agregado correctamente.');

        return $this->redirectRoute('movimientos.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.create');
    }
}
