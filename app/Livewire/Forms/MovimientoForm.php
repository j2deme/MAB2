<?php

namespace App\Livewire\Forms;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Grupo;
use Livewire\Form;
use App\Enums\MovesStatus;
use App\Enums\MovesType;
use App\Enums\Ups;
use App\Enums\Downs;
use App\Enums\MovesAnswers;
use Auth;

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

    public $max_altas = 3;
    public $altas = [];

    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'semestre_id' => 'required',
            'carrera_id' => '',
            'grupo_id' => 'required',
            'tipo' => 'required',
            'estatus' => 'required',
            'motivo' => 'bail|required|string',
            'motivo_adicional' => 'nullable|string',
            'respuesta' => 'nullable|string',
            'respuesta_adicional' => 'nullable|string',
            'is_paralelo' => 'bail|required|boolean',
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

        if ($tipo == 'alta') {
            $semestre        = Semestre::where('activo', true)->first();
            $this->max_altas = $semestre->max_altas;

            $this->altas = Movimiento::where('user_id', Auth::user()->id)
                ->where('semestre_id', $semestre->id)
                ->where('deleted_at', null)
                ->where('tipo', MovesType::ALTA)
                ->get();
        }
    }

    public function store(): void
    {
        $materia = Grupo::find($this->grupo_id)->materia;

        // if (Auth::user()->carrera_id !== $materia->carrera_id) {
        //     $this->addError('grupo_id', 'El grupo seleccionado no pertenece a tu carrera');
        //     return;
        // }


        $this->movimientoModel->create($this->validate());

        if ($this->asociado_id != -1) {
            $asociado = Movimiento::find($this->asociado_id);
            $this->movimientoModel->associate($asociado);
            $asociado->associate($this->movimientoModel);
        }

        $this->reset();
    }

    public function update(): void
    {
        if ($this->asociado_id != -1) {
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

        $this->movimientoModel->update($this->validate());

        $this->reset();
    }

    private function cargaDesplegables($tipo = '')
    {
        $semestre = Semestre::where('activo', true)->first();

        $this->tipos      = MovesType::cases();
        $this->estatuses  = MovesStatus::cases();
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
    }
}
