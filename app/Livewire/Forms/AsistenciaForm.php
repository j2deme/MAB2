<?php

namespace App\Livewire\Forms;

use App\Models\Asistencia;
use Livewire\Form;

class AsistenciaForm extends Form
{
    public ?Asistencia $asistenciaModel;

    public $actividad_id = '';
    public $user_id = '';

    public function rules(): array
    {
        return [
            'actividad_id' => 'required',
            'user_id' => 'required',
        ];
    }

    public function setAsistenciaModel(Asistencia $asistenciaModel): void
    {
        $this->asistenciaModel = $asistenciaModel;

        $this->actividad_id = $this->asistenciaModel->actividad_id;
        $this->user_id      = $this->asistenciaModel->user_id;
    }

    public function store(): void
    {
        $this->asistenciaModel->updateOrCreate(['user_id' => $this->user_id], $this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->asistenciaModel->update($this->validate());

        $this->reset();
    }
}
