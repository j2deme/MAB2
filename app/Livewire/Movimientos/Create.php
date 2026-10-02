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

class Create extends Component
{
    use WireUiActions;
    use UsesSemestreActivo;
    public MovimientoForm $form;

    public function updatedFormCarreraId($value): void
    {
        $this->form->refreshOptionsForCareer($value);
    }

    public function updatedFormMateriaId($value): void
    {
        $this->form->refreshOptionsForMateria($value);
    }

    public function mount(Movimiento $movimiento, $tipo = null)
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

        // La lista de estudiantes no se precarga: el select usa
        // api.estudiantes.index, que con middleware web acota la consulta al rol
        // del usuario (el Coordinador sólo ve estudiantes de sus carreras).
    }

    public function updatedFormTipo($value): void
    {
        // Refresh motivos and related dropdowns when tipo changes
        $this->form->cargaDesplegables($value);
    }

    public function updatedFormUserId($value): void
    {
        // When admin/jefe selects a student, preload the student's carrera and movimientos
        $this->form->setStudentContext((int) $value);

        // Ensure career/materia/group options are reset appropriately
        if ($this->form->carrera_id) {
            $this->form->refreshOptionsForCareer($this->form->carrera_id);
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
