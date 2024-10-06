<?php

namespace App\Livewire\Asistencias;

use App\Models\Asistencia;
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
        $asistencias = Asistencia::paginate();

        return view('livewire.asistencia.index', compact('asistencias'))
            ->with('i', $this->getPage() * $asistencias->perPage());
    }

    public function delete(Asistencia $asistencia)
    {
        $asistencia->delete();

        $this->notification()->error('Registro eliminado', 'Asistencia eliminado correctamente');

        return $this->redirectRoute('asistencias.index', navigate: true);
    }
}
