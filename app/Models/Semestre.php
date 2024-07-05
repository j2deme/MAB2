<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Semestre
 *
 * @property $id
 * @property $clave
 * @property $nombre
 * @property $nombre_completo
 * @property $inicio_altas
 * @property $fin_altas
 * @property $inicio_bajas
 * @property $fin_bajas
 * @property $max_altas
 * @property $activo
 * @property $created_at
 * @property $updated_at
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property-read string $periodo_altas
 * @property-read string $periodo_bajas
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereFinAltas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereFinBajas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereInicioAltas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereInicioBajas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereMaxAltas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semestre whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Semestre extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['clave', 'nombre', 'nombre_completo', 'inicio_altas', 'fin_altas', 'inicio_bajas', 'fin_bajas', 'max_altas', 'activo'];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = ['inicio_altas' => 'datetime', 'fin_altas' => 'datetime', 'inicio_bajas' => 'datetime', 'fin_bajas' => 'datetime', 'max_altas' => 'integer', 'activo' => 'boolean'];

    public function getPeriodoAltasAttribute(): string
    {
        return $this->inicio_altas->format('d/m/Y') . ' - ' . $this->fin_altas->format('d/m/Y');
    }

    public function getPeriodoBajasAttribute(): string
    {
        return $this->inicio_bajas->format('d/m/Y') . ' - ' . $this->fin_bajas->format('d/m/Y');
    }

}
