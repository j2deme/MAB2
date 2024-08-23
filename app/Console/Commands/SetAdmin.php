<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the given user as admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the user
        $email = $this->argument('email');
        // Find a user with the given email
        $user = User::where('email', $email)->first();

        // If no user is found, ask for information to create a new one
        if (!$user) {
            $this->error('Usuario no encontrado, creando nuevo usuario...');
            $name     = $this->ask('Nombre:');
            $password = $this->secret('Contraseña:');

            // Create a new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'rol' => \App\Enums\UserRoles::ADMIN,
                'password' => bcrypt($password),
            ]);

            $this->info('Administrador creado correctamente');
        } else {
            // Update the user role
            $user->rol = \App\Enums\UserRoles::ADMIN;
            $user->save();

            $this->info('Usuario actualizado correctamente');
        }
    }
}
