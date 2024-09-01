<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Materia
 *
 * @property $id
 * @property $clave
 * @property $nombre
 * @property $nombre_completo
 * @property $carrera_id
 * @property $semestre
 * @property $ht
 * @property $hp
 * @property $cr
 * @property $activo
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 * @property Carrera $carrera
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|Materia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Materia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Materia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Materia query()
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereCarreraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereCr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereHt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereSemestre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Materia withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Materia withoutTrashed()
 * @property-read string $satca
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 * @property-read int|null $grupos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movimiento> $movimientos
 * @property-read int|null $movimientos_count
 * @mixin \Eloquent
 */
class Materia extends Model
{
    use SoftDeletes;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['clave', 'nombre', 'nombre_completo', 'carrera_id', 'semestre', 'ht', 'hp', 'cr', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
        'ht' => 'integer',
        'hp' => 'integer',
        'cr' => 'integer',
        'semestre' => 'integer',
    ];

    # virtual attribute satca composed by ht, hp and cr
    public function getSatcaAttribute(): string
    {
        return "{$this->ht} - {$this->hp} - {$this->cr}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrera()
    {
        return $this->belongsTo(\App\Models\Carrera::class, 'carrera_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grupos()
    {
        return $this->hasMany(\App\Models\Grupo::class, 'materia_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function movimientos()
    {
        return $this->hasManyThrough(\App\Models\Movimiento::class, \App\Models\Grupo::class, 'materia_id', 'grupo_id', 'id', 'id');
    }
}
