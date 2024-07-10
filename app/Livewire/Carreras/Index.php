<?php

namespace App\Livewire\Carreras;

use App\Models\Carrera;
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
        $carreras = Carrera::paginate();

        return view('livewire.carrera.index', compact('carreras'))
            ->with('i', $this->getPage() * $carreras->perPage());
    }

    public function delete(Carrera $carrera)
    {
        $carrera->delete();

        $this->notification()->session()->error('Registro eliminado', 'Carrera eliminada correctamente.');

        return $this->redirectRoute('carreras.index', navigate: true);
    }
}
