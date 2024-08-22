<?php

namespace App\Enums;

enum Downs: string
{
    case DIFICULTAD_ASISTIR = "No puedo asistir a la materia";
    case EMPALME = "La materia se empalma con otra";
    case HORARIO_DISTANTE = "Horario distante del bloque";
    case HORARIO_SOBRECARGADO = "Horario sobrecargado";
    case BAJA_TEMPORAL = "Voy a solicitar baja temporal";
    case BAJA_DEFINITIVA = "Voy a solicitar baja definitiva";
    case OTRO = "Otro";
}
