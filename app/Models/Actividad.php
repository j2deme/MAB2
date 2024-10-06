<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Actividade
 *
 * @property $id
 * @property $clave
 * @property $nombre
 * @property $descripcion
 * @property $fecha_inicio
 * @property $fecha_fin
 * @property $is_activo
 * @property $tipo
 * @property $lugar
 * @property $modalidad
 * @property $is_magistral
 * @property $duracion
 * @property $evento_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Evento $evento
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Actividad extends Model
{

    protected $perPage = 20;

    protected $table = 'actividades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['clave', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'is_activo', 'tipo', 'lugar', 'modalidad', 'is_magistral', 'duracion', 'evento_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evento()
    {
        return $this->belongsTo(\App\Models\Evento::class, 'evento_id', 'id');
    }

}
