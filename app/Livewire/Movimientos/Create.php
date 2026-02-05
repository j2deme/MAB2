<?php

namespace App\Livewire\Movimientos;

use App\Enums\MovesStatus;
use App\Enums\MovesType;
use App\Livewire\Forms\MovimientoForm;
use App\Models\Movimiento;
use App\Models\Semestre;
use App\Traits\UsesSemestreActivo;
use Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserRoles;

class Create extends Component
{
    use WireUiActions;
    use UsesSemestreActivo;
    public MovimientoForm $form;
    public $estudiantes = [];

    public function mount($tipo = null, Movimiento $movimiento)
    {
        $semestre                = $this->getSemestreActivo();
        $movimiento->user_id     = Auth::user()->id;
        $movimiento->semestre_id = $semestre->id;
        $movimiento->estatus     = MovesStatus::REGISTRADO;
        $movimiento->is_paralelo = false;

        if (!is_null($tipo) and in_array($tipo, ['alta', 'baja'])) {
            $movimiento->tipo = match ($tipo) {
                'alta', 'Alta' => MovesType::ALTA,
                'baja', 'Baja' => MovesType::BAJA,
            };
        }

        $this->form->setMovimientoModel($movimiento, $tipo);

        // Si el usuario activo es estudiante, NO carga la lista de estudiantes
        if (Auth::user()->es('Estudiante')) {
            $this->estudiantes = [];
        }
        // Si el usuario es Administrador o Jefe, carga la lista de los estudiantes activos
        if (Auth::user()->es(['Administrador', 'Jefe'])) {
            $this->estudiantes = User::where('rol', UserRoles::ESTUDIANTE)
                ->select('id', 'name', 'username')
                ->orderBy('username')
                ->get();
        }
        // Si el usuario es Coordinador, carga la lista de los estudiantes activos, en la carreras asociadas al coordinador
        if (Auth::user()->es('Coordinador')) {
            $carrerasIds       = Auth::user()->carreras()->pluck('id');
            $this->estudiantes = User::where('rol', UserRoles::ESTUDIANTE)
                ->whereHas(
                    'carreras',
                    fn($query) =>
                    $query->whereIn('id', $carrerasIds)
                )
                ->select('id', 'name', 'username')
                ->orderBy('username')
                ->get();
        }
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
