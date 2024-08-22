<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Grupo
 *
 * @property $id
 * @property $siglas
 * @property $semestre_id
 * @property $materia_id
 * @property $is_disponible
 * @property $is_paralelizable
 * @property $deleted_at
 * @property $created_at
 * @property $updated_at
 * @property Materia $materia
 * @property Semestre $semestre
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereIsDisponible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereIsParalelizable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereMateriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereSemestreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereSiglas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withoutTrashed()
 * @property-read mixed $clave
 * @property-read mixed $nombre
 * @mixin \Eloquent
 */
class Grupo extends Model
{
    use SoftDeletes;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['siglas', 'semestre_id', 'materia_id', 'is_disponible', 'is_paralelizable'];

    protected $casts = [
        'is_disponible' => 'boolean',
        'is_paralelizable' => 'boolean',
    ];


    public function siglas(): Attribute
    {
        return new Attribute(
            get: fn($value) => Str::upper($value),
            set: fn($value) => Str::squish($value),
        );
    }


    public function getclaveAttribute()
    {
        return "{$this->materia->clave} ({$this->siglas})";
    }

    public function getNombreAttribute()
    {
        if ($this->materia) {
            return "[{$this->materia->clave}] {$this->materia->nombre_completo} ({$this->siglas})";
        } else {
            return "[{$this->materia_id}] ({$this->siglas})";
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Materia::class, 'materia_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function semestre(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Semestre::class, 'semestre_id', 'id');
    }
}
