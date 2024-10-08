<?php

namespace App\Livewire\Forms;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Grupo;
use App\Models\User;
use Livewire\Form;
use App\Enums\MovesStatus;
use App\Enums\MovesType;
use App\Enums\Ups;
use App\Enums\Downs;
use App\Enums\MovesAnswers;
use App\Enums\UserRoles;
use Auth;
use Illuminate\Support\Str;

class MovimientoForm extends Form
{
    public ?Movimiento $movimientoModel;

    public $user_id = '';
    public $semestre_id = '';
    public $carrera_id = '';
    public $grupo_id = '';
    public $tipo = '';
    public $estatus = '';
    public $motivo = '';
    public $motivo_adicional = '';
    public $respuesta = '';
    public $respuesta_adicional = '';
    public $asociado_id = '';
    public $is_paralelo = '';

    // Desplegables
    public $tipos = [];
    public $estatuses = [];
    public $movimientos = [];
    public $grupos = [];
    public $motivos = [];
    public $respuestas = [];

    public $mode = 'create';
    public $outOfRange = false;

    public Semestre $semestre;

    public $max_altas = 3;
    public $altas = [];

    public $backRoute = 'movimientos.index';

    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'semestre_id' => 'required',
            'carrera_id' => 'nullable',
            'grupo_id' => 'required',
            'tipo' => 'required',
            'estatus' => 'required',
            'motivo' => 'required|string',
            'motivo_adicional' => 'nullable|string',
            'respuesta' => 'nullable|string',
            'respuesta_adicional' => 'nullable|string',
            'is_paralelo' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'grupo_id.required' => 'El campo grupo es obligatorio.',
            'motivo.required' => 'El campo motivo es obligatorio.',
        ];
    }

    public function setMovimientoModel(Movimiento $movimientoModel, $tipo = null): void
    {
        $this->movimientoModel = $movimientoModel;

        $this->user_id             = $this->movimientoModel->user_id;
        $this->semestre_id         = $this->movimientoModel->semestre_id;
        $this->carrera_id          = $this->movimientoModel->carrera_id;
        $this->grupo_id            = $this->movimientoModel->grupo_id;
        $this->tipo                = $this->movimientoModel->tipo;
        $this->estatus             = $this->movimientoModel->estatus;
        $this->motivo              = $this->movimientoModel->motivo;
        $this->motivo_adicional    = $this->movimientoModel->motivo_adicional;
        $this->respuesta           = $this->movimientoModel->respuesta;
        $this->respuesta_adicional = $this->movimientoModel->respuesta_adicional;
        $this->asociado_id         = $this->movimientoModel->asociado_id;
        $this->is_paralelo         = $this->movimientoModel->is_paralelo;

        if (is_null($tipo)) {
            $tipo = $this->movimientoModel->tipo->value;
        }

        if ($this->movimientoModel->asociado()->first() !== null) {
            $this->asociado_id = $this->movimientoModel->asociado()->first();
        }

        $this->cargaDesplegables($tipo);

        $semestre       = Semestre::where('activo', true)->first();
        $this->semestre = $semestre;

        if ($tipo == 'alta') {
            $this->max_altas = $semestre->max_altas;

            $this->altas = Movimiento::where('user_id', Auth::user()->id)
                ->where('semestre_id', $semestre->id)
                ->where('deleted_at', null)
                ->where('tipo', MovesType::ALTA)
                ->get();
        }

        if (Auth::user()->es('Estudiante')) {
            match ($tipo) {
                'alta', 'Alta' => $this->outOfRange = !now()->between($semestre->inicio_altas, $semestre->fin_altas),
                'baja', 'Baja' => $this->outOfRange = !now()->between($semestre->inicio_bajas, $semestre->fin_bajas),
            };
        }

        $this->setBackRoute(request()->headers->get('referer'));
    }

    public function store(): void
    {
        $movimiento = $this->movimientoModel->create($this->validate());
        if (!is_null($movimiento)) {
            $this->revisaParalelo($movimiento);
            // $this->asociaMovimientoo();
            //$this->reset();
        }
    }

    public function update(): void
    {
        $this->movimientoModel->update($this->validate());
        $this->revisaParalelo($this->movimientoModel);
        // $this->asociaMovimiento();
        //$this->reset();
    }

    private function asociaMovimiento()
    {
        if ($this->asociado_id != -1 and $this->asociado_id != null) {
            // Revisa si hay un movimiento asociado previamente y lo desasocia
            if ($this->movimientoModel->asociado_id != $this->asociado_id and $this->movimientoModel->asociado_id != null) {
                $asociado              = Movimiento::find($this->movimientoModel->asociado_id);
                $asociado->asociado_id = null;
                $asociado->save();
            }

            $asociado                           = Movimiento::find($this->asociado_id);
            $this->movimientoModel->asociado_id = $asociado->id;
            $asociado->asociado_id              = $this->movimientoModel->id;
            $asociado->save();
            $this->movimientoModel->save();
        } else {
            // Revisa si hay un movimiento asociado previamente y lo desasocia
            if ($this->movimientoModel->asociado_id != null) {
                $asociado              = Movimiento::find($this->movimientoModel->asociado_id);
                $asociado->asociado_id = null;
                $asociado->save();
            }
            $this->movimientoModel->asociado_id = null;
            $this->movimientoModel->save();
        }
    }

    private function revisaParalelo(Movimiento $move)
    {
        // Si el usuario no es el dueño del movimiento, no se hace nada
        if (Auth::user()->id !== $move->user_id) {
            return;
        }

        // Si no se ha seleccionado un grupo, no se hace nada
        if ($move->grupo_id == null) {
            return;
        }

        $grupo   = Grupo::find($move->grupo_id);
        $materia = $grupo->materia;

        $owner = User::find($move->user_id);

        // Si la carrera del dueño del movimiento es diferente a la carrera de la materia, se marca como paralelo
        $move->is_paralelo = $owner->carreras()->first()->id !== $materia->carrera_id;
        // La carrera del movimiento se iguala a la carrera de la materia
        $move->carrera_id = $materia->carrera_id;

        $move->save();
    }

    private function cargaDesplegables($tipo = '')
    {
        $semestre = Semestre::where('activo', true)->first();

        $this->tipos      = MovesType::cases();
        $this->respuestas = MovesAnswers::cases();

        $this->grupos = Grupo::with('materia')
            ->where('semestre_id', $semestre->id)
            ->where('is_disponible', true)
            ->get();

        $this->movimientos = Movimiento::where('user_id', Auth::user()->id)
            ->where('semestre_id', $semestre->id)
            ->where('estatus', MovesStatus::REGISTRADO)
            ->where('id', '!=', $this->movimientoModel->id)
            ->get();

        if ($tipo === 'alta') {
            $this->motivos = Ups::cases();
        } else {
            $this->motivos = Downs::cases();
        }

        match (Auth::user()->rol) {
            UserRoles::JEFE => $this->estatuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION, MovesStatus::RECHAZADO_JEFE, MovesStatus::AUTORIZADO_JEFE],
            UserRoles::COORDINADOR => $this->estatuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION, MovesStatus::RECHAZADO, MovesStatus::AUTORIZADO],
            default => $this->estatuses = MovesStatus::cases()
        };
    }

    private function setBackRoute($previous)
    {
        if (Str::contains($previous, 'solicitudes/materias')) {
            $this->backRoute = 'movimientos.materias';
        }

        if (Str::contains($previous, 'solicitudes/generacion')) {
            $this->backRoute = 'movimientos.generacion';
        }

        if (Str::contains($previous, 'solicitudes/pendientes')) {
            $this->backRoute = 'movimientos.pending';
        }

        if (Str::contains($previous, 'solicitudes/atendidas')) {
            $this->backRoute = 'movimientos.attended';
        }
    }
}
