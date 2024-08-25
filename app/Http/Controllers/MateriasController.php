<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MateriasController extends Controller
{
    public function batch()
    {
        $carreras = \App\Models\Carrera::all();
        return view('livewire.materia.upload', compact('carreras'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'archivo' => 'required|file|mimes:xlsx,xls',
        ]);

        $carrera = \App\Models\Carrera::find($request->carrera_id);

        $archivo  = $request->file('archivo');
        $filetype = $archivo->getMimeType();
        $path     = $archivo->store("materias-{$carrera->siglas}.{$filetype}", 'local');

        $reader      = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(storage_path('app/' . $path));
        $worksheet   = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        foreach ($rows as $row) {
            if (Str::lower($row[0]) == 'clave') {
                // Skip the header row
                continue;
            }

            $data = [
                'clave' => $row[0],
                'nombre' => $row[1],
                'nombre_completo' => $row[2],
                'semestre' => $row[3],
                'ht' => $row[4],
                'hp' => $row[5],
                'cr' => $row[6],
                'activo' => true,
                'carrera_id' => $carrera->id,
            ];

            $materia = Materia::where('clave', $data['clave'])->first() ?? new Materia();
            $materia->fill($data);
            $materia->save();
        }

        return redirect()->route('materias.index')->with('success', count($rows) . " materias importadas correctamente en {$carrera->nombre}");
    }
}
