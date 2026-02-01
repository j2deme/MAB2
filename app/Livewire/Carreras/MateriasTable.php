<?php

namespace App\Livewire\Carreras;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Support\Facades\Blade;

final class MateriasTable extends PowerGridComponent
{
  public string $carreraId;

  public function setUp(): array
  {
    return [
      Exportable::make('materias')
        ->striped()
        ->type(Exportable::TYPE_XLS),
      Header::make()->showSearchInput(),
      Footer::make()
        ->showPerPage(10)
        ->showRecordCount(),
    ];
  }

  public function datasource(): Builder
  {
    /** @var Builder */
    $builder = Materia::query()
      ->where('carrera_id', $this->carreraId)
      ->orderBy('semestre')
      ->orderBy('clave', 'asc');

    return $builder;
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
      ->add('semestre')
      ->add('ht')
      ->add('hp')
      ->add('cr')
      ->add('activo')
      ->add('grupos_count', fn(Materia $model) => $model->grupos->count());
  }

  public function columns(): array
  {
    return [
      Column::make('Clave', 'clave')
        ->sortable()
        ->searchable(),
      Column::make('Nombre', 'nombre')
        ->sortable()
        ->searchable(),
      Column::make('Semestre', 'semestre')
        ->sortable()
        ->contentClasses('text-center'),
      Column::make('SATCA', 'satca')
        ->contentClasses('text-center'),
      Column::make('Grupos (Actual)', 'grupos_count')
        ->contentClasses('text-center'),
      Column::make('Estado', 'activo')
        ->contentClasses('text-center'),
      Column::action('Acciones')
        ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />')),
    ];
  }

  public function actions(Materia $row): array
  {
    return [
      Button::add('view')
        ->slot('Ver')
        ->class('px-3 py-1 text-sm text-indigo-600 hover:text-indigo-900')
        ->route('materias.show', ['materia' => $row->id]),
    ];
  }
}

