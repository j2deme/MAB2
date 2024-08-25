<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GruposController extends Controller
{
    public function batch()
    {
        $carreras = \App\Models\Carrera::all();
        return view('livewire.grupo.upload', compact('carreras'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'carrera_id' => 'nullable|exists:carreras,id',
            'archivo' => 'required|file|mimes:xlsx,xls',
        ]);

        // $carrera  = \App\Models\Carrera::find($request->carrera_id);
        $semestre = \App\Models\Semestre::where('activo', true)->first();

        $archivo  = $request->file('archivo');
        $filetype = $archivo->getClientOriginalExtension();
        $filename = "{$semestre->clave}.{$filetype}";
        $path     = $archivo->storeAs("grupos", $filename);

        $reader      = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(storage_path("app/{$path}"));
        $worksheet   = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        foreach ($rows as $row) {
            if (Str::lower($row[0]) == 'clave') {
                // Skip the header row
                continue;
            }

            $materia = Materia::where('clave', $row[0])->first();

            if (!$materia) {
                continue;
                // return redirect()->route('materias.index')->with('error', "La materia con clave {$row[0]} no existe en el sistema");
            }

            $data = [
                'siglas' => $row[1],
                'is_disponible' => true,
                'is_paralelizable' => false,
            ];

            $grupo = Grupo::updateOrCreate(
                ['materia_id' => $materia->id, 'semestre_id' => $semestre->id, 'siglas' => $data['siglas']],
                $data
            );

            $grupo->save();
        }

        return redirect()->route('grupos.index')->with('success', count($rows) . " grupos importados correctamente");
    }
}
