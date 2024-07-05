<?php

namespace App\Livewire\Semestres;

use App\Models\Semestre;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public function render(): View
    {
        $semestres = Semestre::paginate();

        return view('livewire.semestre.index', compact('semestres'))
            ->with('i', $this->getPage() * $semestres->perPage());
    }

    public function delete(Semestre $semestre)
    {
        $semestre->delete();

        return $this->redirectRoute('semestres.index', navigate: true);
    }
}
