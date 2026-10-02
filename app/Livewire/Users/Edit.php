<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    use WireUiActions;
    public UserForm $form;

    public function mount(User $user)
    {
        // Authorization: Coordinators can only edit students that belong to
        // one of their assigned carreras. Administradores and Jefes can edit any user.
        if (Auth::check() && Auth::user()->es('Coordinador')) {
            // Ensure target user is a student
            if (!$user->es('Estudiante')) {
                abort(403, 'No autorizado');
            }

            $coordCareerIds = Auth::user()->carreras()->pluck('carreras.id')->toArray();
            $userCareerIds  = $user->carreras()->pluck('carreras.id')->toArray();

            if (empty(array_intersect($coordCareerIds, $userCareerIds))) {
                abort(403, 'No autorizado');
            }
        }

        $this->form->mode = 'update';
        $this->form->setUserModel($user);
    }

    public string $newPassword = '';

    public function coordinatorResetPassword()
    {
        if (!Auth::check() || !Auth::user()->es('Coordinador')) {
            abort(403);
        }

        // Ensure target user exists and is a student in coordinator careers
        $target = $this->form->userModel;
        if (!$target || !$target->es('Estudiante')) {
            $this->notification()->error('No autorizado', 'Solo se pueden cambiar contraseñas de estudiantes.');
            return;
        }

        $coordCareerIds = Auth::user()->carreras()->pluck('carreras.id')->toArray();
        $userCareerIds  = $target->carreras()->pluck('carreras.id')->toArray();
        if (empty(array_intersect($coordCareerIds, $userCareerIds))) {
            $this->notification()->error('No autorizado', 'El estudiante no pertenece a tus carreras.');
            return;
        }

        $this->validate(['newPassword' => 'required|string|min:6']);

        $target->password = Hash::make($this->newPassword);
        $target->save();

        Log::info('Coordinador cambió contraseña', [
            'coordinator_id' => Auth::id(),
            'target_user_id' => $target->id,
        ]);

        $this->notification()->success('Contraseña cambiada', 'La contraseña del estudiante se ha actualizado.');

        $this->newPassword = '';

        // stay on the page but could redirect if desired
    }

    public function save()
    {
        $this->form->update();

        $this->notification()->session()->success('Registro actualizado', 'Usuario actualizado correctamente.');

        return $this->redirectRoute('users.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.user.edit');
    }
}
