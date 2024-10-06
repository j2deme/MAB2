<?php

namespace App\Livewire\Eventos;

use App\Models\Evento;
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
        $eventos = Evento::paginate();

        return view('livewire.evento.index', compact('eventos'))
            ->with('i', $this->getPage() * $eventos->perPage());
    }

    public function delete(Evento $evento)
    {
        $evento->delete();

        $this->notification()->error('Registro eliminado', 'Evento eliminado correctamente');

        return $this->redirectRoute('eventos.index', navigate: true);
    }
}
