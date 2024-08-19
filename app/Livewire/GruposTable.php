<?php

namespace App\Livewire;

use App\Models\Grupo;
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

final class GruposTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('grupos')
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
        return Grupo::query()
            ->join('materias', function ($materias) {
                $materias->on('grupos.materia_id', '=', 'materias.id');
            })
            ->join('carreras', function ($carreras) {
                $carreras->on('materias.carrera_id', '=', 'carreras.id');
            })
            ->select([
                'grupos.id',
                'grupos.siglas as grupo_siglas',
                'grupos.semestre_id',
                'grupos.materia_id',
                'materias.nombre_completo as materia_nombre',
                'materias.clave',
                'carreras.id as carrera_id',
                'carreras.siglas as carrera_siglas',
                'carreras.color as carrera_color',
                'grupos.is_disponible',
                'grupos.is_paralelizable',
                'grupos.created_at',
            ]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('grupo_siglas')
            ->add('semestre_id')
            ->add('materia_id')
            ->add('materia_nombre', fn(Grupo $model) => e($model->materia->nombre_completo . ' (' . $model->materia->clave . ')'))
            ->add('carrera_id', fn(Grupo $model) => e($model->materia->carrera->id))
            ->add('carrera_siglas', fn(Grupo $model) => e($model->materia->carrera->siglas))
            ->add('carrera_badge', fn(Grupo $model) => Blade::render("components.carrera-badge", ['carrera' => $model->materia->carrera]))
            ->add('is_disponible')
            ->add('disponible_icon', fn(Grupo $model) => Blade::render('components.disponible-icon', ['disponible' => $model->is_disponible]))
            ->add('is_paralelizable')
            ->add('paralelizable_icon', fn(Grupo $model) => Blade::render('components.paralelo-icon', ['paralelo' => $model->is_paralelizable]))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Materia', 'materia_nombre', 'materia_id')
                ->sortable()
                ->searchable(),

            Column::make('Grupo', 'grupo_siglas')
                ->contentClasses('flex items-center justify-center')
                ->sortable()
                ->searchable(),

            Column::make('Carrera', 'carrera_badge', 'carrera_id')
                ->contentClasses('flex items-center justify-center')
                ->sortable(),

            Column::make('Disponible', 'is_disponible')
                ->hidden(),

            Column::make('Disponible', 'disponible_icon', 'is_disponible')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false)
                ->sortable(),

            Column::make('Paralelizable', 'paralelizable_icon', 'is_paralelizable')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false)
                ->sortable(),

            Column::action('')
                ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
        ];
    }

    public function filters(): array
    {
        $materias = Grupo::query()->with('materia')->groupBy('materia_id')->select('materia_id')->get()->map(function ($materia) {
            return [
                'label' => $materia->materia->nombre_completo . ' (' . $materia->materia->clave . ')',
                'value' => $materia->materia_id,
            ];
        });

        $siglas = Grupo::query()->groupBy('siglas')->select('siglas')->get()->map(function ($sigla) {
            return [
                'label' => $sigla->siglas,
                'value' => $sigla->siglas,
            ];
        });

        return [
            Filter::select('materia_id')
                ->dataSource($materias)
                ->optionLabel('label')
                ->optionValue('value'),
            Filter::select('siglas')
                ->dataSource($siglas)
                ->optionLabel('label')
                ->optionValue('value'),
            Filter::select('is_disponible')
                ->dataSource([
                    ['label' => 'Sí', 'value' => '1'],
                    ['label' => 'No', 'value' => '0'],
                ])
                ->optionLabel('label')
                ->optionValue('value'),
            Filter::select('is_paralelizable')
                ->dataSource([
                    ['label' => 'Sí', 'value' => '1'],
                    ['label' => 'No', 'value' => '0'],
                ])
                ->optionLabel('label')
                ->optionValue('value'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Grupo::query()->find($rowId)->delete();

        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente.');

        $this->refresh();
    }

    public function actions(Grupo $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('row-actions', [
                    'model' => 'grupos',
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
