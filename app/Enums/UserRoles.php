<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'Administrador';
    case JEFE = 'Jefe';
    case COORDINADOR = 'Coordinador';
    case ESTUDIANTE = 'Estudiante';

    public static function es(string $value): self
    {
        return match ($value) {
            'Administrador' => self::ADMIN,
            'Jefe' => self::JEFE,
            'Coordinador' => self::COORDINADOR,
            'Estudiante' => self::ESTUDIANTE,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'black',
            self::JEFE => 'blue',
            self::COORDINADOR => 'green',
            self::ESTUDIANTE => 'sky',
        };
    }
}
