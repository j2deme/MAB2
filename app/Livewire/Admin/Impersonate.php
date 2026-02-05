<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Movimiento;
use App\Enums\UserRoles;
use App\Traits\UsesSemestreActivo;
use Illuminate\Support\Facades\Auth;

class Impersonate extends Component
{
  use UsesSemestreActivo;

  public $role = null;
  public $carrera_id = null;
  public $user_id = null;
  public $users = [];
  public $coordinators = [];
  public $students = [];
  public $jefes = [];

  public function mount()
  {
    // Ensure only Administrador can access
    if (!Auth::user() || !Auth::user()->es('Administrador')) {
      abort(403);
    }
    $this->loadLists();
  }

  public function updatedRole($_val)
  {
    $this->cargaUsuarios();
  }

  public function updatedCarreraId($_val)
  {
    $this->cargaUsuarios();
  }

  public function cargaUsuarios()
  {
    $this->users = [];

    if (!$this->role) {
      return;
    }
    // Populate users list based on selected role
    if ($this->role === 'JEFE') {
      $this->users = User::where('rol', UserRoles::JEFE)
        ->select('id', 'name', 'username', 'rol')
        ->orderBy('name')
        ->limit(200)
        ->get()
        ->map(fn($u) => [
          'id' => $u->id,
          'label' => $u->username ?? $u->name,
          'rol' => \is_object($u->rol) ? $u->rol->value : $u->rol,
        ])
        ->toArray();
      return;
    }

    if ($this->role === 'COORDINADOR') {
      $q = User::where('rol', UserRoles::COORDINADOR)
        ->select('id', 'name', 'username', 'rol')
        ->orderBy('name')
        ->limit(200);
      if ($this->carrera_id) {
        $q = $q->whereHas('carreras', fn($q2) => $q2->where('carreras.id', $this->carrera_id));
      }
      $this->users = $q->get()->map(fn($u) => [
        'id' => $u->id,
        'label' => $u->username ?? $u->name,
        'rol' => \is_object($u->rol) ? $u->rol->value : $u->rol,
      ])->toArray();
      return;
    }

    if ($this->role === 'ESTUDIANTE') {
      $semId   = $this->getSemestreActivoId();
      $userIds = Movimiento::where('semestre_id', $semId)
        ->select('user_id')
        ->distinct()
        ->pluck('user_id')
        ->take(500);

      $this->users = User::whereIn('id', $userIds)
        ->select('id', 'name', 'username', 'rol')
        ->orderBy('name')
        ->limit(500)
        ->get()
        ->map(fn($u) => [
          'id' => $u->id,
          'label' => $u->username ?? $u->name,
          'rol' => \is_object($u->rol) ? $u->rol->value : $u->rol,
        ])
        ->toArray();
      return;
    }
  }

  /**
   * Build lightweight lists used by the UI to avoid loading full models.
   */
  private function loadLists(): void
  {
    // Lightweight coordinator list (all coordinators)
    $this->coordinators = User::where('rol', UserRoles::COORDINADOR)
      ->select('id', 'name', 'username')
      ->orderBy('name')
      ->get()
      ->map(fn($u) => [
        'id' => $u->id,
        'label' => $u->username ?? $u->name,
      ])->toArray();

    // Lightweight jefe list
    $this->jefes = User::where('rol', UserRoles::JEFE)
      ->select('id', 'name', 'username')
      ->orderBy('name')
      ->get()
      ->map(fn($u) => [
        'id' => $u->id,
        'label' => $u->username ?? $u->name,
      ])->toArray();

    // Students: pick up to 5 random users who have movimientos in active semester
    $semId   = $this->getSemestreActivoId();
    $userIds = Movimiento::where('semestre_id', $semId)
      ->select('user_id')
      ->distinct()
      ->inRandomOrder()
      ->limit(5)
      ->pluck('user_id');

    $this->students = User::whereIn('id', $userIds)
      ->select('id', 'name', 'username')
      ->get()
      ->map(fn($u) => [
        'id' => $u->id,
        'label' => $u->username ?? $u->name,
      ])->toArray();
  }

  /**
   * Start impersonation. Accepts an explicit user id from the UI or uses the
   * component property `user_id` as fallback.
   */
  public function startImpersonation($userId = null)
  {
    if (!Auth::user() || !Auth::user()->es('Administrador')) {
      $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'No autorizado']);
      return;
    }

    $userId = $userId ?? $this->user_id;

    if (!$userId) {
      $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Usuario inválido']);
      return;
    }

    $user = User::find($userId);
    if (!$user) {
      $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Usuario no encontrado']);
      return;
    }

    session(['admin_impersonator_id' => Auth::id()]);
    session(['admin_impersonating' => true]);

    Auth::loginUsingId($user->id);

    return redirect()->route('dashboard');
  }

  // duplicate empty startImpersonation removed

  public function render()
  {
    return view('livewire.admin.impersonate');
  }
}
