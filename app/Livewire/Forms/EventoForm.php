<?php

namespace App\Livewire\Forms;

use App\Models\Evento;
use Livewire\Form;

class EventoForm extends Form
{
    public ?Evento $eventoModel;
    
    public $nombre = '';
    public $descripcion = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $is_activo = '';

    public function rules(): array
    {
        return [
			'nombre' => 'required|string',
			'descripcion' => 'required|string',
			'fecha_inicio' => 'required',
			'fecha_fin' => 'required',
			'is_activo' => 'required',
        ];
    }

    public function setEventoModel(Evento $eventoModel): void
    {
        $this->eventoModel = $eventoModel;
        
        $this->nombre = $this->eventoModel->nombre;
        $this->descripcion = $this->eventoModel->descripcion;
        $this->fecha_inicio = $this->eventoModel->fecha_inicio;
        $this->fecha_fin = $this->eventoModel->fecha_fin;
        $this->is_activo = $this->eventoModel->is_activo;
    }

    public function store(): void
    {
        $this->eventoModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->eventoModel->update($this->validate());

        $this->reset();
    }
}
