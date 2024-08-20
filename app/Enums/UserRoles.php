<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'Administrador';
    case JEFE = 'Jefe';
    case COORDINADOR = 'Coordinador';
    case ESTUDIANTE = 'Estudiante';
}
