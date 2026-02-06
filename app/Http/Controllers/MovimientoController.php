<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
  /**
   * Return partial HTML for a movimiento to be injected into a modal.
   */
  public function showPartial(Movimiento $movimiento)
  {
    // `estatus` and `tipo` are enum-casted attributes, not Eloquent relations.
    $movimiento->load(['grupo.materia.carrera', 'user.carreras', 'semestre']);
    $semestre = $movimiento->semestre;

    // Return a lightweight partial (no Livewire page slots) to avoid rendering page components
    return view('partials.movimiento-show-partial', compact('movimiento', 'semestre'));
  }
}
