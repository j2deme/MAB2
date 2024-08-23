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

final class MovimientosTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
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
            ->add('semestre_id')
            ->add('carrera_id')
            ->add('grupo_id')
            ->add('tipo')
            ->add('estatus')
            ->add('motivo')
            ->add('motivo_adicional')
            ->add('respuesta')
            ->add('respuesta_adicional')
            ->add('asociado_id')
            ->add('is_paralelo')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('User id', 'user_id'),
            Column::make('Semestre id', 'semestre_id'),
            Column::make('Carrera id', 'carrera_id'),
            Column::make('Grupo id', 'grupo_id'),
            Column::make('Tipo', 'tipo')
                ->sortable()
                ->searchable(),

            Column::make('Estatus', 'estatus')
                ->sortable()
                ->searchable(),

            Column::make('Motivo', 'motivo')
                ->sortable()
                ->searchable(),

            Column::make('Motivo adicional', 'motivo_adicional')
                ->sortable()
                ->searchable(),

            Column::make('Respuesta', 'respuesta')
                ->sortable()
                ->searchable(),

            Column::make('Respuesta adicional', 'respuesta_adicional')
                ->sortable()
                ->searchable(),

            Column::make('Asociado id', 'asociado_id'),
            Column::make('Is paralelo', 'is_paralelo')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
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
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
