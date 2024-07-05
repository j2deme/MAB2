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
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
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


}
