<?php

namespace App\Livewire\Forms;

use App\Models\Carrera;
use Livewire\Form;

class CarreraForm extends Form
{
    public ?Carrera $carreraModel;

    public $siglas = '';
    public $clave_interna = '';
    public $nombre = '';

    public function rules(): array
    {
        return [
            'siglas' => 'required|string|unique:carreras,siglas,' . optional($this->carreraModel)->id,
            'clave_interna' => 'required|string|unique:carreras,clave_interna,' . optional($this->carreraModel)->id,
            'nombre' => 'required|string',
        ];
    }

    public function setCarreraModel(Carrera $carreraModel): void
    {
        $this->carreraModel = $carreraModel;

        $this->siglas        = $this->carreraModel->siglas;
        $this->clave_interna = $this->carreraModel->clave_interna;
        $this->nombre        = $this->carreraModel->nombre;
    }

    public function store(): void
    {
        $this->carreraModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->carreraModel->update($this->validate());

        $this->reset();
    }
}
