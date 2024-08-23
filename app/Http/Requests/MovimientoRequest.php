<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'user_id' => 'required',
			'semestre_id' => 'required',
			'carrera_id' => 'required',
			'grupo_id' => 'required',
			'tipo' => 'required',
			'estatus' => 'required',
			'motivo' => 'required|string',
			'motivo_adicional' => 'string',
			'respuesta' => 'string',
			'respuesta_adicional' => 'string',
			'is_paralelo' => 'required',
        ];
    }
}
