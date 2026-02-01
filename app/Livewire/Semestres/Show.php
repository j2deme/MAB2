<?php

namespace App\Livewire\Semestres;

use App\Livewire\Forms\SemestreForm;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;

class Show extends Component
{
    public SemestreForm $form;

    public function mount(Semestre $semestre)
    {
        $this->form->setSemestreModel($semestre);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $semestre = $this->form->semestreModel;
        $now      = Carbon::now();

        // Stats generales
        $gruposActivos      = $semestre->grupos()->count();
        $movimientosTotales = $semestre->movimientos()->where('deleted_at', null)->count();
        $altasTotales       = $semestre->movimientos()->where('tipo', 'ALTA')->where('deleted_at', null)->count();
        $bajasTotales       = $semestre->movimientos()->where('tipo', 'BAJA')->where('deleted_at', null)->count();
        $pendientesTotales  = $semestre->movimientos()
            ->whereIn('estatus', ['REGISTRADO', 'REVISION'])
            ->where('deleted_at', null)
            ->count();
        $autorizadosTotales = $semestre->movimientos()
            ->whereIn('estatus', ['AUTORIZADO', 'AUTORIZADO_JEFE'])
            ->where('deleted_at', null)
            ->count();

        // Estado del semestre (pasado, activo, futuro)
        $estado = 'futuro';
        if ($semestre->activo) {
            $estado = 'activo';
        } elseif ($now->isAfter($semestre->fin_bajas)) {
            $estado = 'pasado';
        }

        // Timeline de períodos
        $periodos = [
            'inicio_altas' => $semestre->inicio_altas,
            'fin_altas' => $semestre->fin_altas,
            'inicio_bajas' => $semestre->inicio_bajas,
            'fin_bajas' => $semestre->fin_bajas,
        ];

        // Top 5 carreras por movimientos
        $topCarreras = $semestre->movimientos()
            ->with('carrera')
            ->where('deleted_at', null)
            ->get()
            ->groupBy('carrera_id')
            ->map(function ($movimientos, $carreraId) {
                $carrera = $movimientos->first()->carrera;
                return [
                    'carrera' => $carrera,
                    'count' => $movimientos->count(),
                    'altas' => $movimientos->filter(fn($m) => $m->tipo->value === 'Alta')->count(),
                    'bajas' => $movimientos->filter(fn($m) => $m->tipo->value === 'Baja')->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(5);

        return view('livewire.semestre.show', [
            'semestre' => $semestre,
            'gruposActivos' => $gruposActivos,
            'movimientosTotales' => $movimientosTotales,
            'altasTotales' => $altasTotales,
            'bajasTotales' => $bajasTotales,
            'pendientesTotales' => $pendientesTotales,
            'autorizadosTotales' => $autorizadosTotales,
            'estado' => $estado,
            'periodos' => $periodos,
            'topCarreras' => $topCarreras,
            'now' => $now,
        ]);
    }
}
