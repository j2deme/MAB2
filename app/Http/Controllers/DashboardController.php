<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Semestre;
use App\Enums\MovesType;
use App\Enums\MovesStatus;
use Auth;

class DashboardController extends Controller
{
  public function index()
  {
    $semestre = Semestre::where('activo', true)->first();
    $user     = auth()->user();

    $movimientosTotales = $altasTotales = $bajasTotales = $pendientesTotales = $autorizadosTotales = $rechazadosTotales = null;
    $carreras = null;

    if ($user->es(['Administrador', 'Jefe'])) {
      $movimientos        = Movimiento::where('semestre_id', $semestre->id)->get();
      $movimientosTotales = $movimientos->count();
      $altasTotales       = $movimientos->where('tipo', MovesType::ALTA)->count();
      $bajasTotales       = $movimientos->where('tipo', MovesType::BAJA)->count();
      $pendientesTotales  = $movimientos->where('estatus', MovesStatus::REGISTRADO)->count();
      $autorizadosTotales = $movimientos->where('estatus', MovesStatus::AUTORIZADO)->count();
      $rechazadosTotales  = $movimientos->where('estatus', MovesStatus::RECHAZADO)->count();
    }

    if ($user->es('Coordinador')) {
      $carreras = $user->carreras;
      foreach ($carreras as $carrera) {
        $movs                = Movimiento::where('semestre_id', $semestre->id)
          ->where('carrera_id', $carrera->id)->get();
        $carrera->altas      = $movs->where('tipo', MovesType::ALTA)->count();
        $carrera->bajas      = $movs->where('tipo', MovesType::BAJA)->count();
        $carrera->pendientes = $movs->where('estatus', MovesStatus::REGISTRADO)->count();
      }
    }

    return view('dashboard', compact(
      'movimientosTotales',
      'altasTotales',
      'bajasTotales',
      'pendientesTotales',
      'autorizadosTotales',
      'rechazadosTotales',
      'carreras'
    ));
  }
}
