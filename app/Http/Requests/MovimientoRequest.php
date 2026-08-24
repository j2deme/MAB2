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
            'user_id' => 'required|exists:users,id',
            'semestre_id' => 'required|exists:semestres,id',
            'carrera_id' => 'nullable|integer|exists:carreras,id',
            'grupo_id' => 'required|exists:grupos,id',
            'tipo' => 'required|string',
            'estatus' => 'required|string',
            'motivo' => 'required|string',
            'motivo_adicional' => 'nullable|string|max:200',
            'respuesta' => 'nullable|string',
            'respuesta_adicional' => 'nullable|string',
            'is_paralelo' => 'nullable|boolean',
        ];
    }
}
