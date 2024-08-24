<?php

namespace App\Livewire;

use App\Models\Movimiento;
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

final class MovimientosTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public string $tableName = 'MovimientosTable';

    public function setUp(): array
    {
        $this->showCheckBox();

        $config = [
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];

        if (!Auth::user()->es('Estudiante')) {
            $config[] = Exportable::make('solicitudes')
                ->striped()
                ->type(Exportable::TYPE_XLS);
        }

        return $config;
    }

    public function datasource(): Builder
    {
        return Movimiento::query();
    }

    public function relationSearch(): array
    {
        return [];
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
                ->hidden(Auth::user()->es('Estudiante'))
                ->sortable()
                ->searchable(),

            Column::make('Materia', 'materia')
                ->searchable(),

            Column::make('Grupo', 'siglas')
                ->contentClasses('text-center')
                ->searchable(),

            Column::make('Carrera', 'carrera', 'carrera_id')
                ->contentClasses('justify-center text-center')
                ->sortable()
                ->searchable(),

            Column::make('Tipo', 'tipo_icon', 'tipo')
                ->sortable()
                ->searchable(),

            Column::make('Estatus', 'estatus_badge', 'estatus')
                ->sortable()
                ->searchable(),

            Column::make('Motivo', 'motivo')
                ->hidden()
                ->toggleable(),

            Column::make('Respuesta', 'respuesta')
                ->hidden()
                ->toggleable(),

            // Column::make('Asociado id', 'asociado_id'),
            Column::make('¿Paralelo?', 'paralelo_icon', 'is_paralelo')
                ->contentClasses('flex justify-center text-center')
                ->sortable()
                ->searchable(),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
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
