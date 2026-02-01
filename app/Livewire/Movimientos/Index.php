<?php

namespace App\Livewire\Movimientos;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Traits\UsesSemestreActivo;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WithPagination;
    use WireUiActions;
    use UsesSemestreActivo;

    #[Layout('layouts.app')]
    public function render(): View
    {
        $semestre    = $this->getSemestreActivo();
        $movimientos = Movimiento::paginate();

        return view('livewire.movimiento.index', compact('movimientos', 'semestre'))
            ->with('i', $this->getPage() * $movimientos->perPage());
    }

    // NOTE: Método no funcional, se activa el método delete del PowerGrid
    public function delete(Movimiento $movimiento)
    {
        $movimiento->delete();

        $this->notification()->error('Registro eliminado', 'Movimiento eliminado correctamente');

        return $this->redirectRoute('movimientos.index', navigate: true);
    }
}
