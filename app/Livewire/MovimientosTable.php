<?php

namespace App\Livewire;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\Support\Facades\Blade;
use WireUi\Traits\WireUiActions;
use Auth;
use App\Enums\MovesStatus;
use App\Enums\MovesType;

final class MovimientosTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public string $tableName = 'MovimientosTable';
    public string $clave = '';
    public string $estudiante = '';

    public $tipos = [];

    public function setUp(): array
    {
        if (request()->routeIs('movimientos.materias.clave')) {
            $this->clave = request()->clave;
        }

        if (request()->routeIs('movimientos.generacion.estudiante')) {
            $this->estudiante = request()->estudiante;
        }

        if (request()->routeIs('movimientos.attended')) {
            $this->tipos = [MovesStatus::AUTORIZADO, MovesStatus::AUTORIZADO_JEFE, MovesStatus::RECHAZADO, MovesStatus::RECHAZADO_JEFE];
        } elseif (request()->routeIs('movimientos.pending')) {
            $this->tipos = [MovesStatus::REGISTRADO, MovesStatus::REVISION];
        } else {
            $this->tipos = MovesStatus::cases();
        }

        $config = [
            Header::make()
                ->showSearchInput(),
            (Auth::user()->es('Estudiante')) ? Footer::make()->showRecordCount() : Footer::make()->showPerPage()
                ->showRecordCount()
        ];

        if (Auth::user()->es(['Administrador', 'Jefe'])) {
            $this->showCheckBox();
            $config[] = Exportable::make('solicitudes')
                ->striped()
                ->type(Exportable::TYPE_XLS);
        }

        return $config;
    }

    public function datasource(): Builder
    {
        $semestre = Semestre::where('activo', true)->first();

        if (Auth::user()->es('Estudiante')) {
            return Movimiento::query()
                ->with('user', 'grupo.materia', 'carrera')
                ->where('user_id', Auth::id())
                ->where('movimientos.semestre_id', $semestre->id)
                ->orderBy('tipo')
                ->orderBy('estatus');
        }

        $query = Movimiento::query()
            ->with('user', 'grupo.materia', 'carrera')
            ->join('users', 'movimientos.user_id', '=', 'users.id')
            ->join('grupos', 'movimientos.grupo_id', '=', 'grupos.id')
            ->join('materias', 'grupos.materia_id', '=', 'materias.id')
            ->select('movimientos.*', 'users.username', 'materias.carrera_id', 'grupos.siglas')
            ->orderBy('users.username')
            ->where('movimientos.semestre_id', $semestre->id)
            ->whereIn('estatus', $this->tipos)
            ->when($this->clave != '', function ($query) {
                return $query->where('materias.clave', $this->clave);
            })
            ->when($this->estudiante != '', function ($query) {
                return $query->where('users.username', $this->estudiante);
            })
            ->when(Auth::user()->es('Coordinador'), function ($query) {
                return $query->whereIn('materias.carrera_id', Auth::user()->carreras->pluck('id'));
            });

        return $query;
    }

    public function relationSearch(): array
    {
        return [
            'user' => ['username'],
            'grupo.materia' => ['nombre_completo'],
            'carrera' => ['nombre'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('user_id')
            ->add('usuario', fn(Movimiento $move) => $move->user->username)
            ->add('semestre_id')
            ->add('carrera_id')
            ->add('carrera', function (Movimiento $move) {
                if ($move->is_paralelo) {
                    return Blade::render('components.carrera-badge', ['carrera' => $move->user->carreras->first(), 'paralelo' => $move->carrera]);
                } else {
                    return Blade::render('components.carrera-badge', ['carrera' => $move->user->carreras->first()]);
                }
            })
            ->add('grupo_id')
            ->add('materia', fn(Movimiento $move) => $move->grupo->materia->nombre_completo)
            ->add('siglas', fn(Movimiento $move) => $move->grupo->siglas)
            ->add('tipo')
            ->add('tipo_string', fn(Movimiento $move) => $move->tipo->value)
            ->add('tipo_icon', fn(Movimiento $move) => Blade::render('components.movimiento-tipo-icon', ['tipo' => $move->tipo->value]))
            ->add('estatus')
            ->add('estatus_badge', fn(Movimiento $move) => Blade::render('components.movimiento-estatus-badge', ['estatus' => $move->estatus]))
            ->add('motivo')
            ->add('motivo_adicional')
            ->add('respuesta')
            ->add('respuesta_adicional')
            ->add('asociado_id')
            ->add('is_paralelo')
            ->add('paralelo_icon', fn(Movimiento $move) => Blade::render('components.paralelo-icon', ['paralelo' => $move->is_paralelo]))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Estudiante', 'usuario', 'user_id')
                // ->hidden(Auth::user()->es('Estudiante'))
                ->contentClasses('justify-center text-center text-wrap font-mono')
                // ->sortable()
                ->searchable(),

            Column::make('Materia', 'materia')
                ->contentClasses('text-wrap text-sm')
                ->searchable(),

            Column::make('Grupo', 'siglas')
                ->contentClasses('text-center')
                // ->sortable()
                ->searchable(),

            Column::make('Carrera', 'carrera', 'carrera_id')
                ->contentClasses('justify-center text-center')
                // ->sortable()
                ->searchable(),

            Column::make('Tipo', 'tipo_icon', 'tipo')
                ->contentClasses('flex justify-center')
                // ->sortable()
                ->searchable(),

            Column::make('Estatus', 'estatus_badge', 'estatus')
                ->contentClasses('flex justify-center')
                ->sortable()
                ->searchable(),

            // Column::make('Asociado id', 'asociado_id'),
            Column::make('¿Paralelo?', 'paralelo_icon', 'is_paralelo')
                ->contentClasses('flex justify-center')
                ->sortable()
                ->searchable(),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        // Filtros para estudiantes
        if (Auth::user()->es('Estudiante')) {
            return [
                Filter::enumSelect('tipo_icon', 'tipo')
                    ->datasource(MovesType::cases())
                    ->optionLabel('movimientos.tipo'),

                Filter::enumSelect('estatus', 'estatus')
                    ->datasource(MovesStatus::cases())
                    ->optionLabel('movimientos.estatus'),
            ];
        }

        // Filtros para coordinadores
        if (Auth::user()->es('Coordinador')) {
            $carreras = Auth::user()->carreras;
            $siglas   = Semestre::where('activo', true)
                ->first()
                ->movimientos()
                ->join('grupos', 'movimientos.grupo_id', '=', 'grupos.id')
                ->whereIn('carrera_id', $carreras->pluck('id'))
                ->groupBy('grupos.siglas')
                ->orderBy('grupos.siglas')
                ->select('grupos.siglas')
                ->get();
        } else {
            // Filtros para administradores y jefes
            $carreras = Carrera::query()
                ->orderBy('siglas')
                ->get();
            $siglas   = Movimiento::query()
                ->join('grupos', 'movimientos.grupo_id', '=', 'grupos.id')
                ->groupBy('grupos.siglas')
                ->orderBy('grupos.siglas')
                ->select('grupos.siglas')
                ->get();
        }

        $estudiantes = User::query()
            ->whereIn('id', $this->datasource()->pluck('user_id')->unique())
            ->orderBy('username')
            ->get();

        return [
            Filter::select('usuario', 'user_id')
                ->datasource($estudiantes)
                ->optionLabel('username')
                ->optionValue('id'),

            Filter::select('siglas')
                ->datasource($siglas)
                ->optionLabel('siglas')
                ->optionValue('siglas'),

            Filter::select('carrera', 'materias.carrera_id')
                ->datasource($carreras)
                ->optionLabel('siglas')
                ->optionValue('id'),

            Filter::enumSelect('tipo_icon', 'tipo')
                ->datasource(MovesType::cases())
                ->optionLabel('movimientos.tipo'),

            Filter::enumSelect('estatus', 'estatus')
                ->datasource(MovesStatus::cases())
                ->optionLabel('movimientos.estatus'),

            Filter::boolean('is_paralelo', 'is_paralelo')
                ->label('Sí', 'No'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Movimiento::query()->find($rowId)->delete();

        $this->notification()->success('Registro eliminado', 'Solicitud eliminada correctamente.');

        $this->refresh();
    }

    public function actions(Movimiento $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('movimiento-row-actions', [
                    'model' => $row
                ]),
        ];
    }
}
