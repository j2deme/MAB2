<?php

namespace App\Livewire\Forms;

use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\Grupo;
use App\Models\User;
use Livewire\Form;
use App\Traits\UsesSemestreActivo;
use App\Enums\MovesStatus;
use App\Enums\MovesType;
use App\Enums\Ups;
use App\Enums\Downs;
use App\Enums\MovesAnswers;
use App\Enums\UserRoles;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class MovimientoForm extends Form
{
    use UsesSemestreActivo;

    public ?Movimiento $movimientoModel;

    public $user_id = '';
    public $semestre_id = '';
    public $carrera_id = '';
    public $materia_id = '';
    public $grupo_id = '';
    public $tipo = '';
    public $estatus = '';
    public $motivo = '';
    public $motivo_adicional = '';
    public $respuesta = '';
    public $respuesta_adicional = '';
    public $asociado_id = '';
    public $is_paralelo = '';

    // Desplegables
    public $tipos = [];
    public $estatuses = [];
    public $movimientos = [];
    public $grupos = [];
    public $motivos = [];
    public $respuestas = [];

    public $mode = 'create';
    public $outOfRange = false;

    public Semestre $semestre;

    public $max_altas = 3;
    public $altas = [];

    public $backRoute = 'movimientos.index';

    public function rules(): array
    {
        // Por defecto la respuesta no es obligatoria (creación).
        $respuestaRule = 'nullable|string';

        // Determina si el actor actualmente tiene rol para resolver
        $isResponder = Auth::check() && Auth::user()->es([UserRoles::JEFE, UserRoles::COORDINADOR]);
        $isEditing   = isset($this->movimientoModel) && ($this->movimientoModel?->exists ?? false);
        $groupRule   = ['required', 'exists:grupos,id'];

        if (Auth::check() && Auth::user()->es(UserRoles::ESTUDIANTE)) {
            $groupRule = [
                'required',
                Rule::exists('grupos', 'id')->where(fn($query) => $query
                    ->where('semestre_id', $this->getSemestreActivoId())
                    ->where('is_disponible', true)),
            ];
        }

        // Obtén el valor textual del estatus que se está enviando/mostrando en el formulario.
        $estatusValue = null;
        if ($this->estatus instanceof MovesStatus) {
            $estatusValue = $this->estatus->value;
        } elseif (is_string($this->estatus)) {
            $estatusValue = $this->estatus;
        }

        // Exigir respuesta cuando el estatus resultante es de tipo AUTORIZADO o RECHAZADO
        $acceptedOrRejected = in_array($estatusValue, [
            MovesStatus::AUTORIZADO->value,
            MovesStatus::AUTORIZADO_JEFE->value,
            MovesStatus::RECHAZADO->value,
            MovesStatus::RECHAZADO_JEFE->value,
        ], true);

        if ($isResponder && $isEditing && $acceptedOrRejected) {
            $respuestaRule = 'required|string';
        }

        return [
            'user_id' => 'required|exists:users,id',
            'semestre_id' => 'required|exists:semestres,id',
            'carrera_id' => 'nullable|integer|exists:carreras,id',
            'grupo_id' => $groupRule,
            'tipo' => 'required|string',
            'estatus' => 'required|string',
            'motivo' => 'required|string',
            'motivo_adicional' => 'nullable|string|max:200',
            'respuesta' => $respuestaRule,
            'respuesta_adicional' => 'nullable|string',
            'is_paralelo' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'grupo_id.required' => 'El campo grupo es obligatorio.',
            'motivo.required' => 'El campo motivo es obligatorio.',
        ];
    }

    public function setMovimientoModel(Movimiento $movimientoModel, $tipo = null): void
    {
        $this->movimientoModel = $movimientoModel;

        $this->user_id             = $this->movimientoModel->user_id;
        $this->semestre_id         = $this->movimientoModel->semestre_id;
        $this->carrera_id          = $this->movimientoModel->carrera_id;
        $this->grupo_id            = $this->movimientoModel->grupo_id;
        $this->materia_id          = $this->movimientoModel->grupo?->materia_id ?? '';
        $this->tipo                = $this->movimientoModel->tipo;
        $this->estatus             = $this->movimientoModel->estatus;
        $this->motivo              = $this->movimientoModel->motivo;
        $this->motivo_adicional    = $this->movimientoModel->motivo_adicional;
        $this->respuesta           = $this->movimientoModel->respuesta;
        $this->respuesta_adicional = $this->movimientoModel->respuesta_adicional;
        $this->asociado_id         = $this->movimientoModel->asociado_id;
        $this->is_paralelo         = $this->movimientoModel->is_paralelo;

        if (is_null($tipo)) {
            $tipo = $this->movimientoModel->tipo?->value ?? '';
        }

        if ($this->movimientoModel->asociado()->first() !== null) {
            $this->asociado_id = $this->movimientoModel->asociado()->first();
        }

        $this->cargaDesplegables($tipo);

        $semestre       = $this->getSemestreActivo();
        $this->semestre = $semestre;

        $tipoNormalized = $this->normalizeTipo($tipo);

        if ($tipoNormalized === 'alta') {
            $this->max_altas = $semestre?->max_altas ?? 0;

            if ($semestre) {
                $this->altas = Movimiento::where('user_id', Auth::user()->id)
                    ->where('semestre_id', $semestre->id)
                    ->whereNull('deleted_at')
                    ->where('tipo', MovesType::ALTA)
                    ->get();
            }
        }

        if (Auth::user()->es('Estudiante')) {
            $this->outOfRange = $this->isRequestOutsideRange($tipoNormalized, $semestre);
        }

        $this->setBackRoute(request()->headers->get('referer'));
    }

    public function refreshOptionsForCareer($value): void
    {
        $this->materia_id = '';
        $this->grupo_id   = '';
        $this->grupos     = [];
    }

    public function refreshOptionsForMateria($value): void
    {
        $this->materia_id = $value;
        $this->grupo_id   = '';

        $this->grupos = Grupo::query()
            ->select('id', 'siglas')
            ->where('semestre_id', $this->getSemestreActivoId())
            ->where('materia_id', $this->materia_id)
            ->where('is_disponible', true)
            ->when($this->carrera_id, fn($query) => $query->whereHas('materia', fn($materia) => $materia->where('carrera_id', $this->carrera_id)))
            ->orderBy('siglas')
            ->get()
            ->map(fn($grupo) => ['id' => $grupo->id, 'siglas' => $grupo->siglas])
            ->all();
    }

    private function normalizeTipo(string|MovesType|null $tipo): string
    {
        if ($tipo instanceof MovesType) {
            return strtolower($tipo->value);
        }

        return strtolower(trim((string) $tipo));
    }

    public function isRequestOutsideRange(string|MovesType|null $tipo, ?Semestre $semestre = null): bool
    {
        $semestre ??= $this->getSemestreActivo();

        if (!$semestre) {
            return true;
        }

        return match ($this->normalizeTipo($tipo)) {
            'alta' => !now()->between($semestre->inicio_altas, $semestre->fin_altas),
            'baja' => !now()->between($semestre->inicio_bajas, $semestre->fin_bajas),
            default => false,
        };
    }

    public function validateRequestWindow(): void
    {
        if (!Auth::user()?->es('Estudiante')) {
            return;
        }

        $tipo = $this->normalizeTipo($this->tipo ?? $this->movimientoModel?->tipo);

        if ($tipo === '') {
            return;
        }

        if ($this->isRequestOutsideRange($tipo, $this->getSemestreActivo())) {
            throw ValidationException::withMessages([
                'tipo' => ['Fuera de rango para registrar solicitudes de ' . $tipo . ' de materias.'],
            ]);
        }
    }

    public function store(): void
    {
        $this->validateRequestWindow();
        $this->normalizeEnumFields();
        $this->validateStudentGroupSelection();

        $movimiento = $this->movimientoModel->create($this->validate());
        if (!is_null($movimiento)) {
            $this->invalidarCacheMovimientosPorSemestre((int) $movimiento->semestre_id);
            if ($movimiento->grupo_id) {
                $this->invalidarCacheGrupo((int) $movimiento->grupo_id);
            }
            $this->revisaParalelo($movimiento);
            // $this->asociaMovimientoo();
            //$this->reset();
        }
    }

    public function update(): void
    {
        $this->validateRequestWindow();
        $this->normalizeEnumFields();
        $this->validateResponderStatus();
        $this->validateStudentGroupSelection();

        $data = $this->validate();

        // Jefe y Coordinador sólo pueden modificar la resolución: respuesta, respuesta_adicional y estatus
        if (Auth::user()->es([UserRoles::JEFE, UserRoles::COORDINADOR])) {
            $allowed = Arr::only($data, ['respuesta', 'respuesta_adicional', 'estatus']);
        } else {
            $allowed = $data;
        }

        $this->movimientoModel->update($allowed);
        $this->invalidarCacheMovimientosPorSemestre((int) $this->movimientoModel->semestre_id);
        if ($this->movimientoModel->grupo_id) {
            $this->invalidarCacheGrupo((int) $this->movimientoModel->grupo_id);
        }
        $this->revisaParalelo($this->movimientoModel);
        // $this->asociaMovimiento();
        //$this->reset();
    }

    private function normalizeEnumFields(): void
    {
        if ($this->tipo instanceof MovesType) {
            $this->tipo = $this->tipo->value;
        }

        if ($this->estatus instanceof MovesStatus) {
            $this->estatus = $this->estatus->value;
        }
    }

    private function validateResponderStatus(): void
    {
        if (!Auth::check() || !Auth::user()->es([UserRoles::JEFE, UserRoles::COORDINADOR])) {
            return;
        }

        $allowed = Auth::user()->es(UserRoles::JEFE)
            ? [MovesStatus::REGISTRADO->value, MovesStatus::REVISION->value, MovesStatus::RECHAZADO_JEFE->value, MovesStatus::AUTORIZADO_JEFE->value]
            : [MovesStatus::REGISTRADO->value, MovesStatus::REVISION->value, MovesStatus::RECHAZADO->value, MovesStatus::AUTORIZADO->value];

        if (!in_array($this->estatus, $allowed, true)) {
            throw ValidationException::withMessages([
                'estatus' => ['El estatus seleccionado no está permitido para este rol.'],
            ]);
        }
    }

    private function validateStudentGroupSelection(): void
    {
        if (!Auth::check() || !Auth::user()->es(UserRoles::ESTUDIANTE)) {
            return;
        }

        $grupo     = Grupo::with('materia')->find($this->grupo_id);
        $tipo      = $this->normalizeTipo($this->tipo);
        $carreraId = $tipo === 'baja'
            ? Auth::user()->carreras()->value('carreras.id')
            : $this->carrera_id;

        if (!$grupo || (int) $grupo->materia_id !== (int) $this->materia_id || ($carreraId && (int) $grupo->materia->carrera_id !== (int) $carreraId)) {
            throw ValidationException::withMessages([
                'grupo_id' => ['El grupo seleccionado no corresponde a la materia o carrera indicada.'],
            ]);
        }
    }

    private function asociaMovimiento()
    {
        if ($this->asociado_id != -1 and $this->asociado_id != null) {
            // Revisa si hay un movimiento asociado previamente y lo desasocia
            if ($this->movimientoModel->asociado_id != $this->asociado_id and $this->movimientoModel->asociado_id != null) {
                $asociado              = Movimiento::find($this->movimientoModel->asociado_id);
                $asociado->asociado_id = null;
                $asociado->save();
            }

            $asociado                           = Movimiento::find($this->asociado_id);
            $this->movimientoModel->asociado_id = $asociado->id;
            $asociado->asociado_id              = $this->movimientoModel->id;
            $asociado->save();
            $this->movimientoModel->save();
        } else {
            // Revisa si hay un movimiento asociado previamente y lo desasocia
            if ($this->movimientoModel->asociado_id != null) {
                $asociado              = Movimiento::find($this->movimientoModel->asociado_id);
                $asociado->asociado_id = null;
                $asociado->save();
            }
            $this->movimientoModel->asociado_id = null;
            $this->movimientoModel->save();
        }
    }

    private function revisaParalelo(Movimiento $move)
    {
        // Si el usuario no es el dueño del movimiento, no se hace nada
        if (Auth::user()->id !== $move->user_id) {
            return;
        }

        // Si no se ha seleccionado un grupo, no se hace nada
        if ($move->grupo_id == null) {
            return;
        }
        // Try to use already loaded relations when possible to avoid extra queries
        $grupo   = $move->grupo ?? Grupo::with('materia')->find($move->grupo_id);
        $materia = $grupo->materia;

        $owner = $move->user ?? User::with('carreras')->find($move->user_id);

        $ownerCarreras = $owner->carreras ?? collect();

        // Computación explícita del valor para poder registrarlo en logs antes de persistir
        if ($ownerCarreras->isEmpty()) {
            $computed = false;
        } else {
            $computed = !$ownerCarreras->contains('id', $materia->carrera_id);
        }

        // Log no destructivo para diagnosticar falsos positivos en entornos de prueba/producción
        Log::debug('revisaParalelo', [
            'movimiento_id' => $move->id,
            'user_id' => $move->user_id,
            'owner_carreras' => $ownerCarreras->pluck('id')->all(),
            'materia_carrera_id' => $materia->carrera_id ?? null,
            'old_is_paralelo' => $move->is_paralelo ?? null,
            'computed_is_paralelo' => $computed,
        ]);

        // Asignar y persistir
        $move->is_paralelo = $computed;
        $move->carrera_id  = $materia->carrera_id;
        $move->save();
    }

    private function cargaDesplegables($tipo = '')
    {
        $semestre       = $this->getSemestreActivo();
        $this->semestre = $semestre;

        $this->tipos = MovesType::cases();

        // Livewire serializes public properties when hydrating the component.
        // Enum instances may not serialize reliably across the wire boundary,
        // so convert the enum cases to a plain array of strings (value + name)
        // that are safe to render on the client.
        $this->respuestas = collect(MovesAnswers::cases())->map(fn($c) => [
            'name' => $c->name,
            'value' => $c->value,
        ])->values()->all();

        if (Auth::user()->es('Estudiante') && !$this->carrera_id) {
            $this->carrera_id = Auth::user()->carreras()->value('carreras.id');
        }

        $this->movimientos = Movimiento::where('user_id', Auth::user()->id)
            ->where('semestre_id', $semestre->id)
            ->where('estatus', MovesStatus::REGISTRADO)
            ->where('id', '!=', $this->movimientoModel->id)
            ->limit(50)
            ->get();

        $tipoNormalized = is_object($tipo) ? (string) $tipo : (string) $tipo;
        if (strtolower($tipoNormalized) === 'alta') {
            $this->motivos = Ups::cases();
        } else {
            $this->motivos = Downs::cases();
        }

        match (Auth::user()->rol) {
            UserRoles::JEFE => $this->estatuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION, MovesStatus::RECHAZADO_JEFE, MovesStatus::AUTORIZADO_JEFE],
            UserRoles::COORDINADOR => $this->estatuses = [MovesStatus::REGISTRADO, MovesStatus::REVISION, MovesStatus::RECHAZADO, MovesStatus::AUTORIZADO],
            default => $this->estatuses = MovesStatus::cases()
        };
    }

    private function setBackRoute($previous)
    {
        if (Str::contains($previous, 'solicitudes/materias')) {
            $this->backRoute = 'movimientos.materias';
        }

        if (Str::contains($previous, 'solicitudes/generacion')) {
            $this->backRoute = 'movimientos.generacion';
        }

        if (Str::contains($previous, 'solicitudes/pendientes')) {
            $this->backRoute = 'movimientos.pending';
        }

        if (Str::contains($previous, 'solicitudes/sin-respuesta')) {
            $this->backRoute = 'movimientos.missing';
        }

        if (Str::contains($previous, 'solicitudes/atendidas')) {
            $this->backRoute = 'movimientos.attended';
        }
    }
}
