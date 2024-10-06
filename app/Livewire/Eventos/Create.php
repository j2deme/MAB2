<?php

namespace App\Livewire\Eventos;

use App\Livewire\Forms\EventoForm;
use App\Models\Evento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Create extends Component
{
    use WireUiActions;
    public EventoForm $form;

    public function mount(Evento $evento)
    {
        $this->form->setEventoModel($evento);
    }

    public function save()
    {
        $this->form->store();

        $this->notification()->session()->success('Registro agregado', 'Evento agregado correctamente.');

        return $this->redirectRoute('eventos.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.evento.create');
    }
}
