<?php

namespace App\Livewire;

use App\Models\Materia;
use App\Models\Carrera;
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

final class MateriasTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('materias')
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
        return Materia::query()->with('carrera');
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
            ->add('carrera_id')
            ->add('carrera_nombre', fn(Materia $model) => e($model->carrera->nombre))
            ->add('semestre')
            ->add('ht')
            ->add('hp')
            ->add('cr')
            ->add('satca')
            ->add('activo')
            ->add('activo_icon', function (Materia $model) {
                return $model->activo ? Blade::render('<x-icon name="check" bold class="w-5 h-5 text-green-500" />') : Blade::render('<x-icon name="x" bold class="w-5 h-5 text-red-500" />');
            })
            ->add('activo_formatted', fn(Materia $model) => ($model->activo) ? 'Sí' : 'No');
    }

    public function columns(): array
    {
        return [
            Column::make('Clave', 'clave')
                ->sortable()
                ->searchable(),

            Column::make('Nombre', 'nombre')
                ->hidden(true)
                ->visibleInExport(true),

            Column::make('Nombre completo', 'nombre_completo')
                ->contentClasses('text-wrap text-justify')
                ->sortable()
                ->searchable(),

            Column::make('Carrera', 'carrera_nombre', 'carrera_id')
                ->contentClasses('text-wrap text-justify')
                ->sortable()
                ->searchable(),

            Column::make('Semestre', 'semestre')
                ->contentClasses('text-center')
                ->sortable()
                ->searchable(),

            Column::make('HT', 'ht')
                ->hidden(true)
                ->visibleInExport(true),

            Column::make('HP', 'hp')
                ->hidden(true)
                ->visibleInExport(true),

            Column::make('Créditos', 'cr')
                ->hidden(true)
                ->visibleInExport(true),

            Column::make('SATCA', 'satca')
                ->sortable(),

            Column::make('Activo', 'activo_icon')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        $semestres = collect(range(1, 9))->map(fn($semestre) => ['value' => $semestre, 'label' => $semestre]);

        return [
            Filter::select('carrera_nombre', 'carrera_id')
                ->dataSource(Carrera::all())
                ->optionLabel('nombre')
                ->optionValue('id'),
            Filter::select('semestre')
                ->dataSource($semestres)
                ->optionLabel('label')
                ->optionValue('value'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Materia::query()->find($rowId)->delete();

        $this->notification()->error('Registro eliminado', 'Materia eliminada correctamente.');

        $this->refresh();
    }

    public function actions(Materia $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('row-actions', [
                    'model' => 'materias',
                    'id' => $row->id
                ]),
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
