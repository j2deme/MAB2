<?php

namespace App\Enums;

enum MovesType: string
{
    case ALTA = 'Alta';
    case BAJA = 'Baja';

    public function icon(): string
    {
        return match ($this) {
            self::ALTA => 'arrow-up',
            self::BAJA => 'arrow-down',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ALTA => 'blue',
            self::BAJA => 'red',
        };
    }
}
