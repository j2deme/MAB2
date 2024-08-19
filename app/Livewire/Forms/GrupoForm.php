<?php

namespace App\Livewire\Forms;

use App\Models\Grupo;
use Livewire\Form;
use Illuminate\Validation\Rule;

class GrupoForm extends Form
{
    public ?Grupo $grupoModel;

    public $siglas = '';
    public $semestre_id = '';
    public $materia_id = '';
    public $is_disponible = '';
    public $is_paralelizable = '';

    public function rules(): array
    {
        return [
            'siglas' => [
                'required',
                'string',
                'max:5',
                Rule::unique('grupos')->where(function ($query) {
                    return $query->where('materia_id', $this->materia_id)
                        ->where('semestre_id', $this->semestre_id);
                })->ignore($this->grupoModel->id),
            ],
            'semestre_id' => 'required',
            'materia_id' => 'required',
            'is_disponible' => 'required|boolean',
            'is_paralelizable' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'siglas.unique' => 'Ya existe un grupo con estas siglas para la materia seleccionada.',
            'semestre_id.required' => 'El campo semestre es obligatorio.',
            'materia_id.required' => 'El campo materia es obligatorio.'
        ];
    }

    public function setGrupoModel(Grupo $grupoModel): void
    {
        $this->grupoModel = $grupoModel;

        $this->siglas           = $this->grupoModel->siglas;
        $this->semestre_id      = $this->grupoModel->semestre_id;
        $this->materia_id       = $this->grupoModel->materia_id;
        $this->is_disponible    = $this->grupoModel->is_disponible;
        $this->is_paralelizable = $this->grupoModel->is_paralelizable;
    }

    public function store(): void
    {
        $this->grupoModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->grupoModel->update($this->validate());

        $this->reset();
    }
}
