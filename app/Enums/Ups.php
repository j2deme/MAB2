<?php

namespace App\Enums;

enum Ups: string
{
    case ADELANTAR_MATERIA = "Adelantar materia";
    case ATRASO_CAMBIO = "Atraso por cambio de carrera";
    case ATRASO_TEMPORAL = "Atraso por baja temporal";
    case GRUPO_CERRADO = "El grupo estaba cerrado";
    case AGOTO_TIEMPO = "Se agotó el tiempo de selección";
    case MATERIA_RECURSAMIENTO = "Tomar materia de recursamiento";
    case MATERIA_ESPECIAL = "Tomar materia de curso especial";
    case OTRO = "Otro";
}
