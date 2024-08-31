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

class ListaGeneracion extends Component
{
    use WireUiActions;

    public Semestre $semestre;

    public $generaciones;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();

        $movimientos = $this->semestre->movimientos()->get();

        $movimientos->each(function ($move) {
            $paralelos  = true;
            $estudiante = $move->user;
            // La posición son los primero 2 caracteres del número de control si son números o los primeros 3 si inicia con una letra
            $pos = is_numeric(substr($estudiante->username, 0, 2)) ? substr($estudiante->username, 0, 2) : substr($estudiante->username, 1, 2);

            if (Auth::user()->es('Coordinador')) {
                // Si la carrera de la materia no esta entre las que coordina el usuario, se omite
                if (!Auth::user()->carreras->contains($estudiante->carreras->first()->id)) {
                    return;
                }
                $paralelos = false;
            }

            $estudiante->total = $estudiante
                ->movimientos()
                ->where('semestre_id', $this->semestre->id)
                ->when(!$paralelos, function ($query) use ($move) {
                    return $query->where('is_paralelo', false);
                })
                ->whereIn('estatus', [\App\Enums\MovesStatus::REGISTRADO, \App\Enums\MovesStatus::REVISION])
                ->count();

            if (Auth::user()->es('Coordinador') and $move->is_paralelo) {
                return;
            } else {
                $this->generaciones[$pos][$estudiante->username] = $estudiante;
            }
        });


        $this->generaciones = collect($this->generaciones)->sortKeys();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-generacion');
    }
}
