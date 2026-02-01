<?php

namespace App\Livewire\Grupos;

use App\Models\Grupo;
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
        $semestreActivoId = $this->getSemestreActivoId();

        $grupos = Grupo::where('semestre_id', $semestreActivoId)
            ->with(['materia.carrera', 'semestre'])
            ->paginate();

        return view('livewire.grupo.index', compact('grupos'))
            ->with('i', $this->getPage() * $grupos->perPage());
    }

    public function delete(Grupo $grupo)
    {
        $grupo->delete();

        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente');

        return $this->redirectRoute('grupos.index', navigate: true);
    }
}
