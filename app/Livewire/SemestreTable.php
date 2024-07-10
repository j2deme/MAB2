<?php

namespace App\Livewire;

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
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Blade;

final class SemestreTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('semestres')
                ->striped()
                ->type(Exportable::TYPE_XLS),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Semestre::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('clave')
            ->add('nombre')
            ->add('nombre_completo')
            ->add('inicio_altas_formatted', fn(Semestre $model) => Carbon::parse($model->inicio_altas)->format('d/m/Y'))
            ->add('fin_altas_formatted', fn(Semestre $model) => Carbon::parse($model->fin_altas)->format('d/m/Y'))
            ->add('inicio_bajas_formatted', fn(Semestre $model) => Carbon::parse($model->inicio_bajas)->format('d/m/Y'))
            ->add('fin_bajas_formatted', fn(Semestre $model) => Carbon::parse($model->fin_bajas)->format('d/m/Y'))
            ->add('periodo_altas')
            ->add('periodo_bajas')
            ->add('max_altas')
            ->add('activo')
            ->add('activo_icon', function (Semestre $model) {
                return $model->activo ? Blade::render('<x-icon name="check" bold class="w-5 h-5 text-green-500" />') : Blade::render('<x-icon name="x" bold class="w-5 h-5 text-red-500" />');
            })
            ->add('activo_formatted', fn(Semestre $model) => ($model->activo) ? 'Sí' : 'No');
    }

    public function columns(): array
    {
        return [
            Column::make('Clave', 'clave')
                ->sortable()
                ->searchable(),

            Column::make('Nombre Corto', 'nombre')
                ->sortable()
                ->searchable(),

            Column::make('Nombre completo', 'nombre_completo')
                ->sortable()
                ->searchable(),

            Column::make('Periodo Altas', 'periodo_altas')
                ->searchable(),

            Column::make('Periodo Bajas', 'periodo_altas')
                ->searchable(),

            Column::make('Máx. altas', 'max_altas')
                ->contentClasses('text-center'),

            Column::make('Activo', 'activo')
                ->hidden(true)
                ->visibleInExport(false),

            Column::make('Estatus', 'activo_icon')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false),

            Column::make('Activo', 'activo_formatted')
                ->hidden(true)
                ->visibleInExport(true),

            Column::action(''),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Semestre::query()->find($rowId)->delete();

        $this->notification()->send([
            'icon' => 'error',
            'title' => 'Registro eliminado',
            'description' => 'Semestre eliminado correctamente.',
        ]);

        $this->refresh();
    }

    public function actions(Semestre $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('row-actions', [
                    'model' => 'semestres',
                    'id' => $row->id
                ]),
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        Semestre::query()->find($id)->update([
            $field => e($value),
        ]);
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
