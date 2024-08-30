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

class ListaMaterias extends Component
{
    use WireUiActions;

    public Semestre $semestre;

    public $semestres;

    public function mount()
    {
        $this->semestre = Semestre::whereActivo(true)->first();

        $movimientos = $this->semestre->movimientos()->get();

        $movimientos->each(function ($move) {
            $materia = $move->grupo->materia;
            $pos     = $materia->semestre;
            $clave   = $materia->clave;

            if (Auth::user()->es('Coordinador')) {
                // Si la carrera de la materia no esta entre las que coordina el usuario, se omite
                if (!Auth::user()->carreras->contains($materia->carrera)) {
                    return;
                }
            }

            if (isset($this->semestres[$pos][$clave])) {
                $this->semestres[$pos][$clave]->push($move);
            } else {
                $this->semestres[$pos][$clave] = new Collection([$move]);
            }
        });

        $this->semestres = collect($this->semestres)->sortKeys();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.movimiento.lista-materias');
    }
}
