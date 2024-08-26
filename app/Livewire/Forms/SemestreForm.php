<?php

namespace App\Livewire\Forms;

use App\Models\Semestre;
use Livewire\Form;

class SemestreForm extends Form
{
    public ?Semestre $semestreModel;

    public $clave = '';
    public $nombre = '';
    public $nombre_completo = '';
    public $inicio_altas = '';
    public $fin_altas = '';
    public $inicio_bajas = '';
    public $fin_bajas = '';
    public $max_altas = '';
    public $activo = '';

    public function rules(): array
    {
        return [
            'clave' => 'bail|required|string',
            'nombre' => 'bail|required|string',
            'nombre_completo' => 'bail|required|string',
            'activo' => 'nullable|boolean',
            'inicio_altas' => 'bail|required|date',
            'fin_altas' => 'bail|required|date|after:inicio_altas',
            'inicio_bajas' => 'bail|required|date',
            'fin_bajas' => 'bail|required|date|after:inicio_bajas',
            'max_altas' => 'bail|required|integer|min:1|max:10',
        ];
    }

    public function setSemestreModel(Semestre $semestreModel): void
    {
        $this->semestreModel = $semestreModel;

        $this->clave           = $this->semestreModel->clave;
        $this->nombre          = $this->semestreModel->nombre;
        $this->nombre_completo = $this->semestreModel->nombre_completo;
        $this->inicio_altas    = $this->semestreModel->inicio_altas;
        $this->fin_altas       = $this->semestreModel->fin_altas;
        $this->inicio_bajas    = $this->semestreModel->inicio_bajas;
        $this->fin_bajas       = $this->semestreModel->fin_bajas;
        $this->max_altas       = $this->semestreModel->max_altas;
        $this->activo          = $this->semestreModel->activo;
    }

    public function store(): void
    {
        Semestre::query()->update(['activo' => false]);
        $this->activo = true;
        $this->semestreModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        if ($this->activo) {
            Semestre::query()
                ->where('id', '!=', $this->semestreModel->id)
                ->update(['activo' => false]);
        }
        $this->semestreModel->update($this->validate());

        // Sino hay un semestre activo, se activa el actual
        if (!Semestre::where('activo', true)->exists()) {
            $this->semestreModel->update(['activo' => true]);
        }

        $this->reset();
    }
}
