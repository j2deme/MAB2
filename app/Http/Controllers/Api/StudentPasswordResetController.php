<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRoles;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class StudentPasswordResetController extends Controller
{
    /**
     * Solicita el enlace de recuperación para un estudiante (por número de control).
     * La respuesta es siempre la misma para no revelar si la cuenta existe.
     */
    public function forgot(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            // El enlace del correo siempre apunta a esta aplicación (APP_URL)
            Password::sendResetLink($this->credentials($request->input('username')));
        } catch (\Throwable $e) {
            Log::error('Error al enviar enlace de recuperación (API)', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Si el número de control corresponde a un estudiante, se envió un enlace de recuperación al correo registrado.',
        ]);
    }

    /**
     * Completa el cambio de contraseña con el token recibido por correo.
     */
    public function reset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'],
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $status = Password::reset(
            $this->credentials($request->input('username')) + $request->only('password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'error' => 'Token inválido',
                'message' => 'El enlace de recuperación es inválido o expiró. Solicita uno nuevo.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tu contraseña ha sido restablecida.',
        ]);
    }

    private function credentials(string $username): array
    {
        return [
            'username' => $username,
            'rol' => UserRoles::ESTUDIANTE->value,
        ];
    }

    private function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Datos inválidos',
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $errors,
        ], 422);
    }
}
