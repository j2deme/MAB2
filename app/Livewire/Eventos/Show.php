<?php

namespace App\Livewire\Eventos;

use App\Livewire\Forms\EventoForm;
use App\Models\Evento;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public EventoForm $form;

    public function mount(Evento $evento)
    {
        $this->form->setEventoModel($evento);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.evento.show', ['evento' => $this->form->eventoModel]);
    }
}
