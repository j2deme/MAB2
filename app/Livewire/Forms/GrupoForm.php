<?php

namespace App\Livewire\Forms;

use App\Models\Grupo;
use Livewire\Form;

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
			'siglas' => 'required|string',
			'semestre_id' => 'required',
			'materia_id' => 'required',
			'is_disponible' => 'required',
			'is_paralelizable' => 'required',
        ];
    }

    public function setGrupoModel(Grupo $grupoModel): void
    {
        $this->grupoModel = $grupoModel;
        
        $this->siglas = $this->grupoModel->siglas;
        $this->semestre_id = $this->grupoModel->semestre_id;
        $this->materia_id = $this->grupoModel->materia_id;
        $this->is_disponible = $this->grupoModel->is_disponible;
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
