<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Carrera;
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
    $carreras        = null;
    $carrerasResumen = null;
    $totalesResumen  = null;

    if ($user->es(['Administrador', 'Jefe'])) {
      $movimientos        = Movimiento::where('semestre_id', $semestre->id)
        ->whereNull('deleted_at')
        ->get();
      $movimientosTotales = $movimientos->count();
      $altasTotales       = $movimientos->where('tipo', MovesType::ALTA)->count();
      $bajasTotales       = $movimientos->where('tipo', MovesType::BAJA)->count();
      $pendientesTotales  = $movimientos->where('estatus', MovesStatus::REGISTRADO)->count();
      $autorizadosTotales = $movimientos->where('estatus', MovesStatus::AUTORIZADO)->count();
      $rechazadosTotales  = $movimientos->where('estatus', MovesStatus::RECHAZADO)->count();

      // Obtener datos para la tabla de resumen por carrera
      $carrerasResumen = Carrera::with([
        'movimientos' => function ($q) use ($semestre) {
          $q->where('semestre_id', $semestre->id)
            ->whereNull('deleted_at');
        }
      ])->get();

      // Calcular totales para la fila de resumen
      $totalesResumen = [
        'altas' => 0,
        'bajas' => 0,
        'pendientes' => 0,
        'autorizados' => 0,
        'rechazados' => 0,
        'total' => 0
      ];

      foreach ($carrerasResumen as $carrera) {
        $altas       = $carrera->movimientos->where('tipo', MovesType::ALTA)->count();
        $bajas       = $carrera->movimientos->where('tipo', MovesType::BAJA)->count();
        $pendientes  = $carrera->movimientos->where('estatus', MovesStatus::REGISTRADO)->count();
        $autorizados = $carrera->movimientos->where('estatus', MovesStatus::AUTORIZADO)->count();
        $rechazados  = $carrera->movimientos->where('estatus', MovesStatus::RECHAZADO)->count();
        $total       = $carrera->movimientos->count();

        // Agregar datos a la carrera para la vista
        $carrera->resumen = [
          'altas' => $altas,
          'bajas' => $bajas,
          'pendientes' => $pendientes,
          'autorizados' => $autorizados,
          'rechazados' => $rechazados,
          'total' => $total
        ];

        // Acumular totales
        $totalesResumen['altas'] += $altas;
        $totalesResumen['bajas'] += $bajas;
        $totalesResumen['pendientes'] += $pendientes;
        $totalesResumen['autorizados'] += $autorizados;
        $totalesResumen['rechazados'] += $rechazados;
        $totalesResumen['total'] += $total;
      }
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
      'carreras',
      'carrerasResumen',
      'totalesResumen',
      'semestre'
    ));
  }
}