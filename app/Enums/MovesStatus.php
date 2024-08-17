<?php

namespace App\Enums;

enum MovesStatus: string
{
    case REGISTRADO = 'Registrado';
    case REVISION = 'En revisión';
    case AUTORIZADO = 'Autorizado';
    case AUTORIZADO_JEFE = 'Autorizado por jefe';
    case RECHAZADO = 'Rechazado';
    case RECHAZADO_JEFE = 'Rechazado por jefe';
    case CANCELADO = 'Cancelado';

    public function color(): string
    {
        return match ($this) {
            self::REGISTRADO => 'sky-400',
            self::REVISION => 'amber-400',
            self::AUTORIZADO => 'green-400',
            self::AUTORIZADO_JEFE => 'green-500',
            self::RECHAZADO => 'red-400',
            self::RECHAZADO_JEFE => 'red-500',
            self::CANCELADO => 'gray-400',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::REGISTRADO => 'Registrado',
            self::REVISION => 'En revisión',
            self::AUTORIZADO => 'Autorizado',
            self::AUTORIZADO_JEFE => 'Autorizado',
            self::RECHAZADO => 'Rechazado',
            self::RECHAZADO_JEFE => 'Rechazado',
            self::CANCELADO => 'Cancelado',
        };
    }
}
