<?php

namespace App\Livewire;

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

final class CarrerasTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('carreras')
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
        return Carrera::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('siglas')
            ->add('clave_interna')
            ->add('nombre')
            ->add('color')
            ->add('color_icon', fn(Carrera $model) => Blade::render('<x-icon fill name="square" class="w-7 h-7 text-[' . $model->color . ']" />'));
    }

    public function columns(): array
    {
        return [
            Column::make('Siglas', 'siglas')
                ->sortable()
                ->searchable(),

            Column::make('Clave interna', 'clave_interna')
                ->sortable()
                ->searchable(),

            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable(),

            Column::make('Color', 'color')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Color', 'color_icon')
                ->contentClasses('flex text-center justify-center'),

            Column::action('')
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
        Carrera::query()->find($rowId)->delete();

        $this->notification()->send([
            'icon' => 'error',
            'title' => 'Registro eliminado',
            'description' => 'Carrera eliminada correctamente.',
        ]);

        $this->refresh();
    }

    public function actions(Carrera $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('row-actions', [
                    'model' => 'carreras',
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
