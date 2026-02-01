<?php

namespace App\Livewire\Forms;

use App\Enums\UserRoles;
use App\Models\User;
use App\Models\Carrera;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $userModel;

    public $name = '';
    public $email = '';
    public $password = '';
    public $rol = '';
    public $username = '';
    public $inscrito = false;
    public $carreras_id = [];

    public $tipos = [];
    public $carreras = [];
    public $mode = 'create';

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string',
            'rol' => 'required',
            'password' => $this->mode === 'create'
                ? ['required', 'string', Rules\Password::defaults()]
                : ['nullable', 'string', 'min:6'],
            'username' => 'nullable|string',
            'inscrito' => 'boolean',
            'carreras_id' => ['nullable', 'array'],
        ];
    }

    public function setUserModel(User $userModel): void
    {
        $this->userModel = $userModel;

        $this->name        = $this->userModel->name;
        $this->email       = $this->userModel->email;
        $this->password    = ''; #$this->userModel->password;
        $this->rol         = $this->userModel->rol;
        $this->username    = $this->userModel->username;
        $this->inscrito    = $this->userModel->inscrito;
        $this->carreras_id = $this->userModel->carreras()->pluck('carreras.id')->toArray();

        $this->tipos = UserRoles::cases();

        $this->carreras = Carrera::all();
    }

    public function store(): void
    {
        $data             = $this->validate();
        $data['password'] = Hash::make($data['password']);
        $user             = $this->userModel->create($data);
        $this->setCarreras($user);

        $this->reset();
    }

    public function update(): void
    {
        $data = $this->validate();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $this->userModel->update($data);
        $this->setCarreras($this->userModel);

        $this->reset();
    }

    public function setCarreras($user)
    {
        $user->carreras()->sync($this->carreras_id);
    }
}
