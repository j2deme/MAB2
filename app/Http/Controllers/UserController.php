<?php
namespace App\Http\Controllers;
ini_set('max_execution_time', 600);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function batch()
    {
        $carreras = \App\Models\Carrera::all();
        return view('livewire.user.upload', compact('carreras'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'carrera_id' => 'nullable|exists:carreras,id',
            'archivo' => 'required|file|mimes:xlsx,xls',
        ]);

        $carrera = \App\Models\Carrera::find($request->carrera_id);

        $archivo  = $request->file('archivo');
        $filetype = $archivo->getClientOriginalExtension();
        $filename = "Usuarios.{$filetype}";
        $path     = $archivo->storeAs("usuarios", $filename);

        $reader      = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(storage_path("app/{$path}"));
        $worksheet   = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        foreach ($rows as $row) {
            if (Str::lower($row[0]) == 'número de control' or Str::lower($row[0]) == 'numero de control') {
                // Skip the header row
                continue;
            }

            $data = [
                'username' => Str::squish($row[0]),
                'email' => Str::squish($row[0]) . "@tecvalles.mx",
                'name' => Str::squish("{$row[1]} {$row[2]} {$row[3]}"),
                'password' => Hash::make($row[4]),
                'rol' => \App\Enums\UserRoles::ESTUDIANTE,
                'inscrito' => false,
            ];

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            $user->carreras()->sync($carrera->id);
            $user->save();
        }

        return redirect()->route('users.index')->with('success', count($rows) . " usuarios importados correctamente");
    }
}
