<?php

namespace App\Livewire;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\User;
use App\Traits\UsesSemestreActivo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
    use UsesSemestreActivo;

    public string $tableName = 'MovimientosTable';
    public string $clave = '';
    public string $estudiante = '';

    public $tipos = [];

    // Caches para HTML pre-renderizado (evita Blade::render por fila)
    private array $tipoIconCache = [];
    private array $estatusBadgeCache = [];
    private array $carreraBadgeCache = [];
    private ?string $paraleloIconHtml = null;

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

    public function datasource(): Builder|\Illuminate\Database\Query\Builder
    {
        $semestre = $this->getSemestreActivo();

        if (Auth::user()->es('Estudiante')) {
            return Movimiento::query()
                ->select('movimientos.*')
                ->with('user:id,username', 'user.carreras', 'grupo:id,siglas,materia_id', 'grupo.materia:id,nombre_completo,clave,carrera_id', 'carrera:id,nombre,siglas')
                ->where('user_id', Auth::id())
                ->where('semestre_id', $semestre->id)
                ->orderBy('tipo')
                ->orderBy('estatus');
        }

        // Cache the carrera IDs for coordinators to avoid repeated queries
        $carreras = Auth::user()->es('Coordinador')
            ? Auth::user()->carreras->pluck('id')->toArray()
            : [];

        $query = Movimiento::query()
            ->select('movimientos.*')
            ->with(
                'user:id,username',
                'user.carreras',
                'grupo:id,siglas,materia_id',
                'grupo.materia:id,nombre_completo,clave,carrera_id',
                'grupo.materia.carrera:id,nombre,siglas,color',
                'carrera:id,nombre,siglas'
            )
            ->where('semestre_id', $semestre->id)
            ->whereIn('estatus', $this->tipos)
            ->orderBy('updated_at', 'desc')
            ->when($this->clave != '', function ($query) {
                return $query->whereHas('grupo.materia', fn($q) => $q->where('clave', $this->clave));
            })
            ->when($this->estudiante != '', function ($query) {
                return $query->whereHas('user', fn($q) => $q->where('username', $this->estudiante));
            })
            ->when(!empty($carreras), function ($query) use ($carreras) {
                return $query->whereIn('carrera_id', $carreras);
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
                return $this->renderCarreraBadge($move);
            })
            ->add('grupo_id')
            ->add('materia', fn(Movimiento $move) => $move->grupo->materia->nombre_completo)
            ->add('siglas', fn(Movimiento $move) => $move->grupo->siglas)
            ->add('tipo')
            ->add('tipo_string', fn(Movimiento $move) => $move->tipo->value)
            ->add('tipo_icon', function (Movimiento $move) {
                return $this->renderTipoIcon($move);
            })
            ->add('estatus')
            ->add('estatus_badge', function (Movimiento $move) {
                return $this->renderEstatusBadge($move);
            })
            ->add('motivo')
            ->add('motivo_adicional')
            ->add('respuesta')
            ->add('respuesta_adicional')
            ->add('asociado_id')
            ->add('is_paralelo')
            ->add('paralelo_icon', function (Movimiento $move) {
                return $this->renderParaleloIcon($move);
            })
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
                    ->optionLabel('label')
                    ->optionValue('value'),

                Filter::enumSelect('estatus', 'estatus')
                    ->datasource(MovesStatus::cases())
                    ->optionLabel('label')
                    ->optionValue('value'),
            ];
        }

        // Caché granular por usuario para filtros computados
        $cacheKey   = $this->getCacheKeyForUser('movimientos.filters');
        $filterData = Cache::remember($cacheKey, 3600, function () {
            $semestre = $this->getSemestreActivo();

            if (Auth::user()->es('Coordinador')) {
                $carreras = Auth::user()->carreras;
                $siglas   = $semestre->movimientos()
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

            return compact('carreras', 'siglas', 'estudiantes');
        });

        return [
            Filter::select('usuario', 'user_id')
                ->datasource($filterData['estudiantes'])
                ->optionLabel('username')
                ->optionValue('id'),

            Filter::select('siglas')
                ->datasource($filterData['siglas'])
                ->optionLabel('siglas')
                ->optionValue('siglas')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('grupo', fn($q) => $q->where('siglas', $value));
                }),

            Filter::select('carrera', 'carrera_id')
                ->datasource($filterData['carreras'])
                ->optionLabel('siglas')
                ->optionValue('id')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('grupo.materia', fn($q) => $q->where('carrera_id', $value));
                }),

            Filter::enumSelect('tipo_icon', 'tipo')
                ->datasource(MovesType::cases())
                ->optionLabel('label')
                ->optionValue('value'),

            Filter::enumSelect('estatus', 'estatus')
                ->datasource(MovesStatus::cases())
                ->optionLabel('label')
                ->optionValue('value'),

            Filter::boolean('is_paralelo', 'is_paralelo')
                ->label('Sí', 'No'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Movimiento::query()->find($rowId)->delete();

        // Invalida caché de filtros al eliminar
        $this->invalidateCacheForUser('movimientos.filters');

        $this->notification()->success('Registro eliminado', 'Solicitud eliminada correctamente.');

        $this->refresh();
    }

    /**
     * Render helpers that cache HTML snippets to avoid repeated Blade::render calls.
     */
    private function renderTipoIcon(Movimiento $move): string
    {
        // Normalize tipo to enum instance when possible to avoid TypeErrors
        $tipoEnum = null;
        if (is_object($move->tipo)) {
            $tipoEnum = $move->tipo;
        } elseif (is_string($move->tipo) || is_int($move->tipo)) {
            $tipoEnum = \App\Enums\MovesType::tryFrom($move->tipo) ?? null;
        }

        $icon          = $tipoEnum && method_exists($tipoEnum, 'icon') ? $tipoEnum->icon() : null;
        $color         = $tipoEnum && method_exists($tipoEnum, 'color') ? $tipoEnum->color() : 'gray';
        $colorClassMap = ['blue' => 'text-blue-600', 'red' => 'text-red-600'];
        $colorClass    = $colorClassMap[$color] ?? 'text-gray-600';

        $label    = $tipoEnum ? ($tipoEnum->value ?? (string) $tipoEnum) : (is_scalar($move->tipo) ? (string) $move->tipo : '');
        $iconName = $tipoEnum && method_exists($tipoEnum, 'icon') ? $tipoEnum->icon() : $icon;

        $cacheKey = 'tipo_' . ($iconName ?? 'none') . '_' . $colorClass . '_' . md5($label);

        if (isset($this->tipoIconCache[$cacheKey])) {
            return $this->tipoIconCache[$cacheKey];
        }

        if (in_array($iconName, ['arrow-up', 'arrow-down'])) {
            $iconHtml       = Blade::render("<x-icon name=\"{$iconName}\" class=\"w-4 h-4 {$colorClass}\" />");
            $colorTextClass = $iconName === 'arrow-up' ? 'text-blue-600' : 'text-red-600';
            $html           = '<span class="inline-flex items-center space-x-2"><span class="' . $colorTextClass . '">' . $iconHtml . '</span><span class="text-xs font-semibold">' . e($label) . '</span></span>';
        } else {
            $html = '<span class="text-xs">' . e($label) . '</span>';
        }

        $this->tipoIconCache[$cacheKey] = $html;
        return $html;
    }

    private function renderEstatusBadge(Movimiento $move): string
    {
        // Normalize estatus to enum instance when possible
        $estatusEnum = null;
        if (is_object($move->estatus)) {
            $estatusEnum = $move->estatus;
        } elseif (is_string($move->estatus) || is_int($move->estatus)) {
            $estatusEnum = \App\Enums\MovesStatus::tryFrom($move->estatus) ?? null;
        }

        $statusKey = $estatusEnum ? ($estatusEnum->value ?? (string) $estatusEnum) : (is_scalar($move->estatus) ? (string) $move->estatus : '');
        if (isset($this->estatusBadgeCache[$statusKey])) {
            return $this->estatusBadgeCache[$statusKey];
        }

        $label    = $estatusEnum && method_exists($estatusEnum, 'descripcion') ? $estatusEnum->descripcion() : ($estatusEnum->value ?? (is_scalar($move->estatus) ? (string) $move->estatus : 'N/A'));
        $colorKey = $estatusEnum && method_exists($estatusEnum, 'color') ? $estatusEnum->color() : 'gray';

        // Cache per statusKey (small cardinality)
        $html                                = Blade::render("<x-badge :label=\"\$label\" color=\"{$colorKey}\" />", ['label' => $label]);
        $this->estatusBadgeCache[$statusKey] = $html;
        return $html;
    }

    private function renderParaleloIcon(Movimiento $move): string
    {
        if ($this->paraleloIconHtml !== null) {
            // Return appropriate HTML based on paralelo flag
            return $move->is_paralelo
                ? $this->paraleloIconHtml
                : $this->paraleloIconHtml . '<!--not-paralelo-->';
        }

        $pIcon   = Blade::render('<x-icon name="letter-circle-p" bold class="w-5 h-5 text-blue-600" />');
        $notIcon = Blade::render('<x-icon name="minus" bold class="w-5 h-5 text-gray-400" />');

        // Store composite HTMLs keyed by presence; we'll return correct one
        $this->paraleloIconHtml = '<span class="inline-flex items-center text-sm font-semibold">' . $pIcon . '</span>';
        // Append a marker for not-paralelo case to avoid re-rendering notIcon each time
        $notHtml = $notIcon;

        return $move->is_paralelo ? $this->paraleloIconHtml : $notHtml;
    }

    private function renderCarreraBadge(Movimiento $move): string
    {
        // Build a cache key based on user's carreras or single carrera and paralelo target
        $userCarreras = $move->user->carreras ?? collect();
        if ($userCarreras->count() > 1) {
            $ids = $userCarreras->pluck('id')->sort()->values()->toArray();
            $key = 'carr_multi_' . implode('-', $ids);
            if (isset($this->carreraBadgeCache[$key])) {
                return $this->carreraBadgeCache[$key];
            }
            $html                          = view('components.carrera-badge', ['carreras' => $userCarreras])->render();
            $this->carreraBadgeCache[$key] = $html;
            return $html;
        }

        $carrera  = $userCarreras->first() ?? $move->carrera ?? null;
        $paralelo = null;
        if ($move->is_paralelo) {
            $paralelo = $move->grupo && $move->grupo->materia ? $move->grupo->materia->carrera : $move->carrera;
        }

        $carreraId = $carrera?->id ?? 'none';
        $parKey    = $paralelo?->id ?? 'nopar';
        $cacheKey  = "carr_{$carreraId}_par_{$parKey}";

        if (isset($this->carreraBadgeCache[$cacheKey])) {
            return $this->carreraBadgeCache[$cacheKey];
        }

        $html                               = view('components.carrera-badge', ['carrera' => $carrera, 'paralelo' => $paralelo])->render();
        $this->carreraBadgeCache[$cacheKey] = $html;
        return $html;
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

    /**
     * Dispatch a browser event when the component is mounted (useful for showing loaders)
     */
    public function mount(): void
    {
        // Ensure the parent PowerGridComponent mounts and initializes state
        parent::mount();

        // Livewire v3 uses ->dispatch() for events that reach the frontend
        $this->dispatch('movimientos-table-mounted');
    }
}
