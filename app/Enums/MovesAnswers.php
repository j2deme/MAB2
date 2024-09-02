<?php

namespace App\Enums;

enum MovesAnswers: string
{
    case EMPALME_1 = 'Presenta 1 empalme';
    case EMPALME_2 = 'Presenta 2 empalmes';
    case EMPALME_3 = 'Presenta 3 o más empalmes';
    case CORREQUISITO = 'Adeuda materia correquisito';
    case PRERREQUISITO = 'Adeuda materia prerrequisito';
    case EXCESO = 'Excede la cantidad de créditos reglamentaria';
    case GRUPO_MAX = 'El grupo solicitado está a capacidad máxima';
    case NO_AUTORIZADO = 'No autorizado por situación académica';
    case NO_EQUIVALENTE = 'La materia no es equivalente para la carrera';
    case SEMESTRE_1 = 'No se autorizan bajas para 1er semestre';
    case GRAVE = 'Presenta grave problema académico';
    case OK = 'Sin problemas';
    case OTRO = 'Otro';
}
