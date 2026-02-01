<?php

namespace App\Livewire\Semestres;

use App\Livewire\Forms\SemestreForm;
use App\Models\Semestre;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

        // Stats generales (queries simples, escalables)
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

        // Top 5 carreras por movimientos - CACHEADO (5 minutos, periodicidad: moderada)
        $topCarreras = Cache::remember(
            "semestre:{$semestre->id}:top-carreras",
            300, // 5 minutos
            function () use ($semestre) {
                $topCarrerasRaw = $semestre->movimientos()
                    ->selectRaw('carrera_id, COUNT(*) as count, SUM(CASE WHEN tipo = ? THEN 1 ELSE 0 END) as altas, SUM(CASE WHEN tipo = ? THEN 1 ELSE 0 END) as bajas', ['ALTA', 'BAJA'])
                    ->where('deleted_at', null)
                    ->groupBy('carrera_id')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->with('carrera')
                    ->get();

                return $topCarrerasRaw->map(function ($movimiento) {
                    return [
                        'carrera' => $movimiento->carrera,
                        'count' => $movimiento->count,
                        'altas' => $movimiento->altas,
                        'bajas' => $movimiento->bajas,
                    ];
                });
            }
        );

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
