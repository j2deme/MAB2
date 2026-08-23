<?php

namespace App\Livewire\Forms;

use App\Models\Carrera;
use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Traits\UsesSemestreActivo;

class CarreraForm extends Form
{
    use UsesSemestreActivo;

    public ?Carrera $carreraModel;

    public $siglas = '';
    public $clave_interna = '';
    public $nombre = '';
    public $color = '';

    public function rules(): array
    {
        return [
            'siglas' => [
                'required',
                'string',
                Rule::unique('carreras')->ignore($this->carreraModel->id)
            ],
            'clave_interna' => [
                'required',
                'string',
                Rule::unique('carreras')->ignore($this->carreraModel->id)
            ],
            'nombre' => 'required|string',
            'color' => 'bail|nullable|string',
        ];
    }

    public function setCarreraModel(Carrera $carreraModel): void
    {
        $this->carreraModel = $carreraModel;

        $this->siglas        = $this->carreraModel->siglas;
        $this->clave_interna = $this->carreraModel->clave_interna;
        $this->nombre        = $this->carreraModel->nombre;
        $this->color         = $this->carreraModel->color;
    }

    public function store(): void
    {
        $carrera = $this->carreraModel->create($this->validate());

        $this->invalidarCacheCarrera((int) $carrera->id);
        $this->forgetCachePattern('movimientos.%');

        $this->reset();
    }

    public function update(): void
    {
        $this->carreraModel->update($this->validate());

        $this->invalidarCacheCarrera((int) $this->carreraModel->id);
        $this->forgetCachePattern('movimientos.%');

        $this->reset();
    }
}
