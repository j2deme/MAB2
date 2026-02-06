<?php

namespace App\Http\Livewire\Movimiento;

use Livewire\Component;
use App\Models\Movimiento;

class ModalShow extends Component
{
  public ?Movimiento $movimiento = null;
  public bool $open = false;

  protected $listeners = [
    'openMovimientoModal' => 'open',
  ];

  public function open($id)
  {
    $this->movimiento = Movimiento::with(['grupo.materia.carrera', 'user.carreras', 'semestre'])->find($id);
    if ($this->movimiento) {
      $this->open = true;
    }
  }

  public function close()
  {
    $this->open       = false;
    $this->movimiento = null;
  }

  public function render()
  {
    return view('livewire.movimiento.modal-show');
  }
}
