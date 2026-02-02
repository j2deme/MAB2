<?php

namespace App\Livewire\Movimientos;
ini_set('max_execution_time', 1000);

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\Semestre;
use Illuminate\Database\Eloquent\Collection;
use Auth;
use App\Models\User;
use App\Models\Movimiento;
use App\Enums\MovesStatus;

class ListaGeneracion extends Component
{
    use WireUiActions;

    public Semestre $semestre;

    public $generaciones;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();

        $semestreId = $this->semestre?->id;

        if (!$semestreId) {
            $this->generaciones = collect();
            return;
        }

        // Obtener user_ids únicos con movimientos en el semestre (evita iterar movimientos completos)
        $userIds = Movimiento::query()
            ->where('semestre_id', $semestreId)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        if (empty($userIds)) {
            $this->generaciones = collect();
            return;
        }

        // Preparar consulta de usuarios con relaciones y conteos agregados para evitar N+1
        $statuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION];

        $usersQuery = User::query()
            ->whereIn('id', $userIds)
            ->with('carreras')
            ->withCount(['movimientos as total' => fn($q) => $q->where('semestre_id', $semestreId)->whereIn('estatus', $statuses)]);

        // Si es coordinador, limitar usuarios a los de sus carreras y excluir paralelos
        if (Auth::user()->es('Coordinador')) {
            $carrerasIds = Auth::user()->carreras->pluck('id')->toArray();
            if (!empty($carrerasIds)) {
                $usersQuery->whereHas('carreras', fn($q) => $q->whereIn('carreras.id', $carrerasIds));
            }

            // Ajustar conteo para excluir paralelos
            $usersQuery = User::query()
                ->whereIn('id', $userIds)
                ->with('carreras')
                ->whereHas('carreras', fn($q) => $q->whereIn('carreras.id', $carrerasIds))
                ->withCount(['movimientos as total' => fn($q) => $q->where('semestre_id', $semestreId)->whereIn('estatus', $statuses)->where('is_paralelo', false)]);
        }

        $users = $usersQuery->get();

        $generaciones = [];

        foreach ($users as $estudiante) {
            $pos = is_numeric(substr($estudiante->username, 0, 2)) ? substr($estudiante->username, 0, 2) : substr($estudiante->username, 1, 2);

            // Si el coordinador y el estudiante no tiene carreras en el conjunto (seguridad)
            if (Auth::user()->es('Coordinador')) {
                $firstCarr = $estudiante->carreras->first();
                if (!$firstCarr || !Auth::user()->carreras->contains($firstCarr->id)) {
                    continue;
                }
            }

            // Añadir total calculado por withCount
            $estudiante->total = $estudiante->total ?? 0;

            $generaciones[$pos][$estudiante->username] = $estudiante;
        }

        $this->generaciones = collect($generaciones)->sortKeys();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-generacion');
    }
}
