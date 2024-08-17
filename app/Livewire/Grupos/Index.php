<?php

namespace App\Livewire\Grupos;

use App\Models\Grupo;
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
        $grupos = Grupo::paginate();

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
