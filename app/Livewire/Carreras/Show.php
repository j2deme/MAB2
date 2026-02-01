<?php

namespace App\Livewire\Carreras;

use App\Livewire\Forms\CarreraForm;
use App\Models\Carrera;
use App\Traits\UsesSemestreActivo;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    use UsesSemestreActivo;

    public CarreraForm $form;

    public function mount(Carrera $carrera)
    {
        $this->form->setCarreraModel($carrera);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $carrera        = $this->form->carreraModel;
        $semestreActivo = $this->getSemestreActivo();

        // Coordinadores de esta carrera
        $coordinadores = $carrera->usuarios()
            ->where('rol', 'Coordinador')
            ->get();

        // Movimientos recientes de esta carrera (últimos 5)
        $movimientosRecientes = $carrera->movimientos()
            ->with('user', 'grupo.materia')
            ->where('deleted_at', null)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // Stats
        $totalMaterias      = $carrera->materias()->count();
        $totalGruposActivos = $semestreActivo
            ? $carrera->grupos()->where('semestre_id', $semestreActivo->id)->count()
            : 0;
        $totalCoordinadores = $coordinadores->count();
        $totalMovimientos   = $carrera->movimientos()->where('deleted_at', null)->count();

        return view('livewire.carrera.show', [
            'carrera' => $carrera,
            'carreraId' => $carrera->id,
            'coordinadores' => $coordinadores,
            'movimientosRecientes' => $movimientosRecientes,
            'semestreActivo' => $semestreActivo,
            'totalMaterias' => $totalMaterias,
            'totalGruposActivos' => $totalGruposActivos,
            'totalCoordinadores' => $totalCoordinadores,
            'totalMovimientos' => $totalMovimientos,
        ]);
    }
}
