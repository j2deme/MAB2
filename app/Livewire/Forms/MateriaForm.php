<?php

namespace App\Livewire\Forms;

use App\Models\Materia;
use Livewire\Form;
use Illuminate\Validation\Rule;

class MateriaForm extends Form
{
    public ?Materia $materiaModel;

    public $clave = '';
    public $nombre = '';
    public $nombre_completo = '';
    public $carrera_id = '';
    public $semestre = '';
    public $ht = '';
    public $hp = '';
    public $cr = '';
    public $activo = '';

    public function rules(): array
    {
        return [
            'clave' => [
                'bail',
                'required',
                'string',
                Rule::unique('materias')->ignore($this->materiaModel->id),
            ],
            'nombre' => 'bail|required|string|max:75',
            'nombre_completo' => 'bail|required|string',
            'carrera_id' => 'required',
            'semestre' => 'bail|required|min:1|max:9',
            'ht' => 'required',
            'hp' => 'required',
            'cr' => 'required',
            'activo' => 'nullable|boolean',
        ];

    }

    public function messages()
    {
        return [
            'carrera_id.required' => 'El campo carrera es obligatorio.',
            'ht.required' => 'El campo horas téoricas es obligatorio.',
            'hp.required' => 'El campo horas prácticas es obligatorio.',
            'cr.required' => 'El campo créditos es obligatorio.',
        ];
    }

    public function setMateriaModel(Materia $materiaModel): void
    {
        $this->materiaModel = $materiaModel;

        $this->clave           = $this->materiaModel->clave;
        $this->nombre          = $this->materiaModel->nombre;
        $this->nombre_completo = $this->materiaModel->nombre_completo;
        $this->carrera_id      = $this->materiaModel->carrera_id;
        $this->semestre        = $this->materiaModel->semestre;
        $this->ht              = $this->materiaModel->ht;
        $this->hp              = $this->materiaModel->hp;
        $this->cr              = $this->materiaModel->cr;
        $this->activo          = $this->materiaModel->activo;
    }

    public function store(): void
    {
        $this->activo = (is_null($this->activo)) ? false : $this->activo;
        $this->materiaModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->materiaModel->update($this->validate());

        $this->reset();
    }
}
