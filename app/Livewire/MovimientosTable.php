<?php

namespace App\Livewire;

use App\Models\Movimiento;
use App\Models\Semestre;
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

    public function setUp(): array
    {

        $config = [
            Header::make()
                ->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
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
        if (Auth::user()->es('Estudiante')) {
            return Movimiento::query()
                ->with('user', 'grupo.materia', 'carrera')
                ->where('user_id', Auth::id())
                ->orderBy('tipo')
                ->orderBy('estatus');
        }

        if (request()->routeIs('movimientos.attended')) {
            $tipos = [MovesStatus::AUTORIZADO, MovesStatus::AUTORIZADO_JEFE, MovesStatus::RECHAZADO, MovesStatus::RECHAZADO_JEFE];
        } elseif (request()->routeIs('movimientos.pending')) {
            $tipos = [MovesStatus::REGISTRADO, MovesStatus::REVISION];
        } else {
            $tipos = MovesStatus::cases();
        }

        if (Auth::user()->es(['Coordinador'])) {

            return Movimiento::query()
                ->with('user', 'grupo.materia', 'carrera')
                ->where('is_paralelo', false)
                ->join('users', 'movimientos.user_id', '=', 'users.id')
                ->select('movimientos.*', 'users.username')
                ->orderBy('users.username')
                ->whereIn('estatus', $tipos)
                ->whereIn('carrera_id', Auth::user()->carreras->pluck('id'));
        }

        return Movimiento::query()
            ->with('user', 'grupo.materia', 'carrera')
            ->join('users', 'movimientos.user_id', '=', 'users.id')
            ->join('grupos', 'movimientos.grupo_id', '=', 'grupos.id')
            ->join('materias', 'grupos.materia_id', '=', 'materias.id')
            ->select('movimientos.*', 'users.username', 'materias.carrera_id', 'grupos.siglas')
            ->orderBy('users.username')
            ->whereIn('estatus', $tipos);
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
            ->add('carrera', fn(Movimiento $move) => Blade::render('components.carrera-badge', ['carrera' => $move->carrera]))
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
                ->sortable()
                ->searchable(),

            Column::make('Materia', 'materia')
                ->contentClasses('text-wrap text-sm')
                ->searchable(),

            Column::make('Grupo', 'siglas')
                ->contentClasses('text-center')
                ->sortable()
                ->searchable(),

            Column::make('Carrera', 'carrera', 'carrera_id')
                ->contentClasses('justify-center text-center')
                ->sortable()
                ->searchable(),

            Column::make('Tipo', 'tipo_icon', 'tipo')
                ->contentClasses('flex justify-center')
                ->sortable()
                ->searchable(),

            Column::make('Estatus', 'estatus_badge', 'estatus')
                ->contentClasses('flex justify-center')
                ->sortable()
                ->searchable(),

            // Column::make('Asociado id', 'asociado_id'),
            Column::make('¿Paralelo?', 'paralelo_icon', 'is_paralelo')
                ->contentClasses('flex justify-center')
                ->hidden(!Auth::user()->es(['Administrador', 'Jefe']))
                ->sortable()
                ->searchable(),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        if (Auth::user()->es('Estudiante')) {
            return [
                Filter::select('tipo_icon', 'tipo')
                    ->datasource(MovesType::cases())
                    ->optionLabel('movimientos.tipo'),

                Filter::enumSelect('estatus', 'estatus')
                    ->datasource(MovesStatus::cases())
                    ->optionLabel('movimientos.estatus'),
            ];
        }

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
            $carreras = \App\Models\Carrera::query()
                ->orderBy('siglas')
                ->get();
            $siglas   = Movimiento::query()
                ->join('grupos', 'movimientos.grupo_id', '=', 'grupos.id')
                ->groupBy('grupos.siglas')
                ->orderBy('grupos.siglas')
                ->select('grupos.siglas')
                ->get();
        }

        $estudiantes = \App\Models\User::query()
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
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Movimiento::query()->find($rowId)->delete();

        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente.');

        // $this->refresh();
        redirect()->route('movimientos.index');
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
