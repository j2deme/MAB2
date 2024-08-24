<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Carrera
 *
 * @property $id
 * @property $siglas
 * @property $clave_interna
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera query()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereClaveInterna($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereSiglas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Materia> $materias
 * @property-read int|null $materias_count
 * @property string|null $color
 * @method static \Illuminate\Database\Eloquent\Builder|Carrera whereColor($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 * @property-read int|null $grupos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $usuarios
 * @property-read int|null $usuarios_count
 * @mixin \Eloquent
 */
class Carrera extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['siglas', 'clave_interna', 'nombre', 'color'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function grupos()
    {
        return $this->hasManyThrough(Grupo::class, Materia::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
