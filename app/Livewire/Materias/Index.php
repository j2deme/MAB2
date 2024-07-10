<?php

namespace App\Livewire\Materias;

use App\Models\Materia;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WithPagination;
    use WireUiActions;

    #[Layout('layouts.app')]
    public function render(): View
    {
        $materias = Materia::paginate();

        return view('livewire.materia.index', compact('materias'))
            ->with('i', $this->getPage() * $materias->perPage());
    }

    public function delete(Materia $materia)
    {
        $materia->delete();

        $this->notification()->session()->error('Registro eliminado', 'Materia eliminada correctamente.');

        return $this->redirectRoute('materias.index', navigate: true);
    }
}
