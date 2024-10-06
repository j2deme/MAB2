<?php

namespace App\Livewire\Forms;

use App\Models\Actividad;
use Livewire\Form;

class ActividadForm extends Form
{
    public ?Actividad $actividadModel;

    public $clave = '';
    public $nombre = '';
    public $descripcion = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $is_activo = '';
    public $tipo = '';
    public $lugar = '';
    public $modalidad = '';
    public $is_magistral = '';
    public $duracion = '';
    public $evento_id = '';

    public function rules(): array
    {
        return [
            'clave' => 'required|string',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'is_activo' => 'required|boolean',
            'tipo' => 'required|string',
            'lugar' => 'nullable|string',
            'modalidad' => 'required|string',
            'is_magistral' => 'required|boolean',
            'duracion' => 'required',
            'evento_id' => 'required|exists:eventos,id',
        ];
    }

    public function setActividadModel(Actividad $actividadModel): void
    {
        $this->actividadModel = $actividadModel;

        $this->clave        = $this->actividadModel->clave;
        $this->nombre       = $this->actividadModel->nombre;
        $this->descripcion  = $this->actividadModel->descripcion;
        $this->fecha_inicio = $this->actividadModel->fecha_inicio;
        $this->fecha_fin    = $this->actividadModel->fecha_fin;
        $this->is_activo    = $this->actividadModel->is_activo;
        $this->tipo         = $this->actividadModel->tipo;
        $this->lugar        = $this->actividadModel->lugar;
        $this->modalidad    = $this->actividadModel->modalidad;
        $this->is_magistral = $this->actividadModel->is_magistral;
        $this->duracion     = $this->actividadModel->duracion;
        $this->evento_id    = $this->actividadModel->evento_id;
    }

    public function store(): void
    {
        $this->actividadModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->actividadModel->update($this->validate());

        $this->reset();
    }
}
