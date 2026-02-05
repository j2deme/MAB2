<?php

namespace App\Livewire\Grupos;

use App\Livewire\Forms\GrupoForm;
use App\Models\Grupo;
use App\Traits\UsesSemestreActivo;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    use UsesSemestreActivo;

    public GrupoForm $form;
    public bool $mostrarTodos = false;

    public function mount($grupo)
    {
        // $grupo may be a model (route model binding) or an id — resolve including trashed
        if ($grupo instanceof Grupo) {
            $g = $grupo;
        } else {
            $g = Grupo::withTrashed()->findOrFail($grupo);
        }

        $this->form->setGrupoModel($g);
    }

    public function mostrarTodos(): void
    {
        $this->mostrarTodos = true;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $grupo    = $this->form->grupoModel;
        $cacheKey = $this->getCacheKeyForUser("grupo.{$grupo->id}");

        // Movimientos a mostrar (todos o primeros 10) con paginación
        $movimientos = $grupo->movimientos()
            ->with(['user.carreras'])
            ->withTrashed()
            ->latest('updated_at')
            ->when(!$this->mostrarTodos, fn($q) => $q->take(10))
            ->get();

        // Stats calculados en BD con caché granular por usuario
        $stats = Cache::remember($cacheKey, 1800, function () use ($grupo) {
            return [
                'totalEstudiantes' => $grupo->movimientos()
                    ->selectRaw('COUNT(DISTINCT user_id) as total')
                    ->where('deleted_at', null)
                    ->value('total') ?? 0,
                'totalMovimientos' => $grupo->movimientos()
                    ->where('deleted_at', null)
                    ->count(),
                'altasAprobadas' => $grupo->movimientos()
                    ->where('tipo', 'ALTA')
                    ->where('deleted_at', null)
                    ->count(),
                'bajasAprobadas' => $grupo->movimientos()
                    ->where('tipo', 'BAJA')
                    ->where('deleted_at', null)
                    ->count(),
            ];
        });

        return view('livewire.grupo.show', [
            'grupo' => $grupo,
            'movimientos' => $movimientos,
            'totalEstudiantes' => $stats['totalEstudiantes'],
            'totalMovimientos' => $stats['totalMovimientos'],
            'altasAprobadas' => $stats['altasAprobadas'],
            'bajasAprobadas' => $stats['bajasAprobadas'],
        ]);
    }
}
