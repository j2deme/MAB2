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
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Carrera extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['siglas', 'clave_interna', 'nombre'];


}
