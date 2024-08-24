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
            self::REGISTRADO => 'sky',
            self::REVISION => 'amber',
            self::AUTORIZADO => 'green',
            self::AUTORIZADO_JEFE => 'emerald',
            self::RECHAZADO => 'red',
            self::RECHAZADO_JEFE => 'rose',
            self::CANCELADO => 'gray',
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
