<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Log;

class DiagnosticarParalelos extends Command
{
  protected $signature = 'diagnosticar:paralelos {--fix : Corrige los registros que estén mal marcados} {--semestre_id= : Filtrar por semestre_id}';

  protected $description = 'Reporta movimientos marcados como paralelo que en realidad pertenecen a la misma carrera del usuario. Use --fix para corregirlos.';

  public function handle()
  {
    $query = Movimiento::with(['user.carreras', 'grupo.materia']);

    if ($this->option('semestre_id')) {
      $query = $query->where('semestre_id', $this->option('semestre_id'));
    }

    $query = $query->where('is_paralelo', true);

    $this->info('Escaneando movimientos marcados como paralelo...');

    $totalChecked    = 0;
    $totalMismatches = 0;

    $query->chunk(200, function ($movs) use (&$totalChecked, &$totalMismatches) {
      foreach ($movs as $m) {
        $totalChecked++;
        $userCarrIds   = $m->user->carreras->pluck('id')->all();
        $materiaCarrId = $m->grupo->materia->carrera_id ?? $m->carrera_id;

        if (in_array($materiaCarrId, $userCarrIds, true)) {
          $totalMismatches++;
          $this->line("[MISMATCH] movimiento={$m->id} user={$m->user_id} materia_carrera={$materiaCarrId} user_carreras=[" . implode(',', $userCarrIds) . "]");
          Log::warning('diagnosticarParalelos.mismatch', [
            'movimiento_id' => $m->id,
            'user_id' => $m->user_id,
            'user_carreras' => $userCarrIds,
            'materia_carrera_id' => $materiaCarrId,
          ]);

          if ($this->option('fix')) {
            $m->is_paralelo = false;
            $m->save();
            $this->info("  -> corregido movimiento={$m->id}");
            Log::info('diagnosticarParalelos.fixed', ['movimiento_id' => $m->id]);
          }
        }
      }
    });

    $this->info("Escaneo finalizado. Revisados: {$totalChecked}. Falsos positivos: {$totalMismatches}.");
    if ($this->option('fix')) {
      $this->info('Se aplicaron correcciones a los registros detectados.');
    } else {
      $this->info('Ejecute con --fix para corregir automáticamente los casos reportados.');
    }

    return 0;
  }
}
