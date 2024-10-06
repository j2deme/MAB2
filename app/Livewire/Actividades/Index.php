<?php

namespace App\Livewire\Actividades;

use App\Models\Actividad;
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
        $actividades = Actividad::paginate();

        return view('livewire.actividad.index', compact('actividades'))
            ->with('i', $this->getPage() * $actividades->perPage());
    }

    public function delete(Actividad $actividad)
    {
        $actividad->delete();

        $this->notification()->error('Registro eliminado', 'Actividad eliminada correctamente');

        return $this->redirectRoute('actividades.index', navigate: true);
    }
}
