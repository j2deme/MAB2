<?php

namespace App\Livewire;

use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\Materia;
use App\Traits\UsesSemestreActivo;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Lazy;
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

final class GruposTable extends PowerGridComponent
{
    use WithExport;
    use WireUiActions;
    use UsesSemestreActivo;

    public ?int $semestreId = null;

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
        // If the component is mounted inside the semestre show view (semestreId provided)
        // include soft-deleted groups so the admin can see and manage them.
        if ($this->semestreId) {
            return Grupo::withTrashed()
                ->with(['materia', 'materia.carrera'])
                ->where('semestre_id', $this->semestreId);
        }

        $semestreActivoId = $this->semestreId ?? $this->getSemestreActivoId();
        // Default behavior: only non-deleted groups for active context
        return Grupo::query()
            ->with(['materia', 'materia.carrera'])
            ->where('semestre_id', $semestreActivoId);
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
            ->add('estatus_badge', function (Grupo $model) {
                if ($model->deleted_at) {
                    return Blade::render("<x-badge color='red' label='Borrado' sm />");
                }
                return Blade::render("<x-badge color='green' label='Activo' sm />");
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        $cols = [
            Column::make('Materia', 'materia_nombre', 'materia_id')
                ->sortable()
                ->searchable(),

            Column::make('Grupo', 'siglas')
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

        ];

        // If we are in the semestre show (semestreId provided), replace the Paralelizable
        // column with an Estatus column that shows if the group is deleted or active.
        if ($this->semestreId) {
            $cols[] = Column::make('Estatus', 'estatus_badge', 'deleted_at')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false)
                ->sortable();
        } else {
            $cols[] = Column::make('Paralelizable', 'paralelizable_icon', 'is_paralelizable')
                ->contentClasses('flex items-center justify-center')
                ->visibleInExport(false)
                ->sortable();
        }

        $cols[] = Column::action('')
            ->title(Blade::render('<x-icon name="gear" class="w-5 h-5 text-gray-600" />'));

        return $cols;
    }

    public function filters(): array
    {
        $materias = Materia::query()->orderBy('nombre_completo')->get()->map(function ($materia) {
            return [
                'label' => $materia->nombre_completo . ' (' . $materia->clave . ')',
                'value' => $materia->id,
            ];
        });

        $semestreActivoId = $this->semestreId ?? $this->getSemestreActivoId();

        $siglas = Grupo::query()
            ->where('semestre_id', $semestreActivoId)
            ->select('siglas')
            ->distinct()
            ->orderBy('siglas')
            ->pluck('siglas')
            ->map(function ($sigla) {
                return [
                    'label' => $sigla,
                    'value' => $sigla,
                ];
            });

        $carreras = Carrera::query()->orderBy('nombre')->get()->map(function ($carrera) {
            return [
                'label' => $carrera->nombre,
                'value' => $carrera->id,
            ];
        });

        $semestreActivoId = $this->semestreId ?? $this->getSemestreActivoId();

        // Leer filtros actualmente aplicados (PowerGrid puede enviar filtros en la request o mantenerlos en $this->filters)
        $applied = request()->input('filters') ?? $this->filters ?? [];

        $selectedSiglas = $applied['siglas'] ?? null;
        // PowerGrid puede nombrar el filtro de carrera por el primer argumento (carrera_badge) o por el campo (carrera_id)
        $selectedCarrera = $applied['carrera_badge'] ?? $applied['carrera_id'] ?? null;
        $selectedMateria = $applied['materia_id'] ?? null;

        // Materias disponibles condicionadas por filtros
        $matQuery = Materia::query();
        $matQuery->whereHas('grupos', function ($q) use ($semestreActivoId, $selectedSiglas, $selectedCarrera) {
            if ($this->semestreId) {
                $q->withTrashed();
            }
            $q->where('semestre_id', $semestreActivoId);
            if ($selectedSiglas) {
                $q->where('siglas', $selectedSiglas);
            }
            if ($selectedCarrera) {
                $q->whereHas('materia', function ($mq) use ($selectedCarrera) {
                    $mq->where('carrera_id', $selectedCarrera);
                });
            }
        });

        $materias = $matQuery->orderBy('nombre_completo')->get()->map(function ($materia) {
            return [
                'label' => $materia->nombre_completo . ' (' . $materia->clave . ')',
                'value' => $materia->id,
            ];
        });

        // Siglas disponibles condicionadas por filtros
        $siglasQuery = Grupo::query();
        if ($this->semestreId) {
            $siglasQuery->withTrashed();
        }
        $siglasQuery->where('semestre_id', $semestreActivoId);
        if ($selectedCarrera) {
            $siglasQuery->whereHas('materia', function ($q) use ($selectedCarrera) {
                $q->where('carrera_id', $selectedCarrera);
            });
        }
        if ($selectedMateria) {
            $siglasQuery->where('materia_id', $selectedMateria);
        }

        $siglas = $siglasQuery->select('siglas')->distinct()->orderBy('siglas')->pluck('siglas')->map(function ($sigla) {
            return [
                'label' => $sigla,
                'value' => $sigla,
            ];
        });

        // Carreras disponibles condicionadas por filtros
        $carrQuery = Carrera::query()->whereHas('materias', function ($mq) use ($semestreActivoId, $selectedSiglas, $selectedMateria) {
            $mq->whereHas('grupos', function ($gq) use ($semestreActivoId, $selectedSiglas, $selectedMateria) {
                if ($this->semestreId) {
                    $gq->withTrashed();
                }
                $gq->where('semestre_id', $semestreActivoId);
                if ($selectedSiglas) {
                    $gq->where('siglas', $selectedSiglas);
                }
                if ($selectedMateria) {
                    $gq->where('materia_id', $selectedMateria);
                }
            });
        });

        $carreras = $carrQuery->orderBy('nombre')->get()->map(function ($carrera) {
            return [
                'label' => $carrera->nombre,
                'value' => $carrera->id,
            ];
        });

        return [
            Filter::select('materia_id')
                ->dataSource($materias)
                ->optionLabel('label')
                ->optionValue('value'),
            // Filtro por siglas del grupo (coincide con el campo 'siglas')
            Filter::select('siglas', 'siglas')
                ->dataSource($siglas)
                ->optionLabel('label')
                ->optionValue('value')
                ->builder(function (Builder $query, $value) {
                    return $query->where('siglas', $value);
                }),

            Filter::select('carrera_badge', 'carrera_id')
                ->dataSource($carreras)
                ->optionLabel('label')
                ->optionValue('value')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('materia', function ($q) use ($value) {
                        $q->where('carrera_id', $value);
                    });
                }),
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

    public function mount(): void
    {
        parent::mount();
        $this->dispatch('grupos-table-mounted');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        // Buscar el grupo incluyendo los soft-deleted
        $grupo = Grupo::withTrashed()->find($rowId);

        if (!$grupo) {
            $this->notification()->error('No encontrado', 'El grupo solicitado no existe.');
            return;
        }

        // Si el grupo ya está soft-deleted, intentamos un borrado definitivo (forceDelete)
        if ($grupo->trashed()) {
            // Permitir borrado definitivo sólo si NO tiene movimientos activos
            $activeMovs = $grupo->movimientos()->count();
            if ($activeMovs > 0) {
                $this->notification()->error('No se puede eliminar', "El grupo tiene {$activeMovs} movimiento(s) activo(s). Imposible eliminación definitiva.");
                return;
            }

            // Si sólo tiene movimientos soft-deleted, eliminarlos definitivamente primero
            $trashedMovs  = $grupo->movimientos()->onlyTrashed()->get();
            $trashedCount = $trashedMovs->count();
            if ($trashedCount > 0) {
                $trashedMovs->each(fn($m) => $m->forceDelete());
            }

            $grupo->forceDelete();
            $msg = 'Grupo eliminado definitivamente.';
            if ($trashedCount > 0) {
                $msg .= " Se eliminaron definitivamente {$trashedCount} movimiento(s) asociados.";
            }

            $this->notification()->success('Registro eliminado', $msg);
            $this->refresh();
            return;
        }

        // Grupo activo: realizar soft-delete
        $grupo->delete();
        $this->notification()->error('Registro eliminado', 'Grupo eliminado correctamente.');
        $this->refresh();
    }

    public function restore($rowId): void
    {
        $grupo = Grupo::withTrashed()->find($rowId);

        if (!$grupo) {
            $this->notification()->error('No encontrado', 'El grupo solicitado no existe.');
            return;
        }

        if (!$grupo->trashed()) {
            $this->notification()->info('No es necesario', 'El grupo ya está activo.');
            return;
        }

        // Restaurar únicamente el grupo; no restaurar movimientos automáticamente
        $grupo->restore();

        $trashedMovsCount = $grupo->movimientos()->onlyTrashed()->count();
        $msg              = 'Grupo restaurado correctamente.';
        if ($trashedMovsCount > 0) {
            $msg .= " Este grupo tiene {$trashedMovsCount} movimiento(s) eliminados. Puedes verlos en la lista con su estatus.";
        }

        $this->notification()->success('Restaurado', $msg);
        $this->refresh();
    }

    public function forceDelete($rowId): void
    {
        $grupo = Grupo::withTrashed()->find($rowId);

        if (!$grupo) {
            $this->notification()->error('No encontrado', 'El grupo solicitado no existe.');
            return;
        }

        // Permitir borrado definitivo sólo si NO hay movimientos activos
        $activeMovs = $grupo->movimientos()->count();
        if ($activeMovs > 0) {
            $this->notification()->error('No se puede eliminar', "El grupo tiene {$activeMovs} movimiento(s) activo(s). Imposible eliminación definitiva.");
            return;
        }

        // Forzar eliminación definitiva de movimientos soft-deleted primero
        $trashedMovs  = $grupo->movimientos()->onlyTrashed()->get();
        $trashedCount = $trashedMovs->count();
        if ($trashedCount > 0) {
            $trashedMovs->each(fn($m) => $m->forceDelete());
        }

        $grupo->forceDelete();
        $msg = 'Grupo eliminado definitivamente.';
        if ($trashedCount > 0) {
            $msg .= " Se eliminaron definitivamente {$trashedCount} movimiento(s) asociados.";
        }

        $this->notification()->success('Eliminado', $msg);
        $this->refresh();
    }

    public function actions(Grupo $row): array
    {
        return [
            Button::add('actions')
                ->bladeComponent('grupo-row-actions', [
                    'grupo' => $row,
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
