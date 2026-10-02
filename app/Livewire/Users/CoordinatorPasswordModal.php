<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use WireUi\Traits\WireUiActions;

class CoordinatorPasswordModal extends Component
{
  use WireUiActions;

  public bool $show = false;
  public ?User $user = null;
  public string $newPassword = '';

  protected $listeners = [
    'openCoordinatorPasswordModal' => 'openModal',
  ];

  public function openModal($userId)
  {
    if (!Auth::check() || !Auth::user()->es('Coordinador')) {
      return; // silently ignore
    }

    $user = User::find($userId);
    if (!$user || !$user->es('Estudiante')) {
      $this->notification()->error('No autorizado', 'Solo se pueden cambiar contraseñas de estudiantes.');
      return;
    }

    $coordCareerIds = Auth::user()->carreras()->pluck('carreras.id')->toArray();
    $userCareerIds  = $user->carreras()->pluck('carreras.id')->toArray();
    if (empty(array_intersect($coordCareerIds, $userCareerIds))) {
      $this->notification()->error('No autorizado', 'El estudiante no pertenece a tus carreras.');
      return;
    }

    $this->user        = $user;
    $this->newPassword = '';
    $this->show        = true;
  }

  public function savePassword()
  {
    if (!Auth::check() || !Auth::user()->es('Coordinador')) {
      abort(403);
    }

    $this->validate(['newPassword' => 'required|string|min:6']);

    $this->user->password = Hash::make($this->newPassword);
    $this->user->save();

    Log::info('Coordinador cambió contraseña (modal)', [
      'coordinator_id' => Auth::id(),
      'target_user_id' => $this->user->id,
    ]);

    $this->notification()->success('Contraseña cambiada', 'La contraseña del estudiante se ha actualizado.');

    $this->show        = false;
    $this->user        = null;
    $this->newPassword = '';
  }

  public function render()
  {
    return view('livewire.users.coordinator-password-modal');
  }
}
