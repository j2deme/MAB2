<?php

namespace App\Livewire\Forms;

use App\Enums\UserRoles;
use App\Models\User;
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

    public $tipos = [];
    public $mode = 'create';

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string',
            'rol' => 'required',
            'password' => ['required', 'string', Rules\Password::defaults()],
            'username' => 'nullable|string',
            'inscrito' => 'boolean',
        ];
    }

    public function setUserModel(User $userModel): void
    {
        $this->userModel = $userModel;

        $this->name     = $this->userModel->name;
        $this->email    = $this->userModel->email;
        $this->password = $this->userModel->password;
        $this->rol      = $this->userModel->rol;
        $this->username = $this->userModel->username;
        $this->inscrito = $this->userModel->inscrito;

        $this->tipos = UserRoles::cases();
    }

    public function store(): void
    {
        $this->password = Hash::make($this->password);
        $this->userModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->userModel->update($this->validate());

        $this->reset();
    }
}
