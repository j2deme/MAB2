<?php

namespace App\Models;

use App\Enums\MovesStatus;
use App\Enums\MovesType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Movimiento
 *
 * @property $id
 * @property $user_id
 * @property $semestre_id
 * @property $carrera_id
 * @property $grupo_id
 * @property $tipo
 * @property $estatus
 * @property $motivo
 * @property $motivo_adicional
 * @property $respuesta
 * @property $respuesta_adicional
 * @property $asociado_id
 * @property $is_paralelo
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 * @property Movimiento $movimiento
 * @property Carrera $carrera
 * @property Grupo $grupo
 * @property Semestre $semestre
 * @property User $user
 * @property Movimiento[] $movimientos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property-read int|null $movimientos_count
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento query()
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereAsociadoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereCarreraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereEstatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereGrupoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereIsParalelo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereMotivoAdicional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereRespuesta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereRespuestaAdicional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereSemestreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Movimiento withoutTrashed()
 * @property-read mixed $nombre
 * @mixin \Eloquent
 */
class Movimiento extends Model
{
    use SoftDeletes;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'semestre_id', 'carrera_id', 'grupo_id', 'tipo', 'estatus', 'motivo', 'motivo_adicional', 'respuesta', 'respuesta_adicional', 'asociado_id', 'is_paralelo'];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_paralelo' => 'boolean',
        'estatus' => MovesStatus::class,
        'tipo' => MovesType::class,
    ];



    public function getNombreAttribute()
    {
        return "{$this->tipo->value} {$this->grupo->materia->nombre_completo} ({$this->grupo->siglas}) [{$this->grupo->materia->carrera->siglas}]";
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movimiento()
    {
        return $this->belongsTo(\App\Models\Movimiento::class, 'asociado_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrera()
    {
        return $this->belongsTo(\App\Models\Carrera::class, 'carrera_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupo()
    {
        return $this->belongsTo(\App\Models\Grupo::class, 'grupo_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function semestre()
    {
        return $this->belongsTo(\App\Models\Semestre::class, 'semestre_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function asociado()
    {
        return $this->hasOne(\App\Models\Movimiento::class, 'id', 'asociado_id');
    }

}
