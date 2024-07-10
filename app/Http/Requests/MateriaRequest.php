<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MateriaRequest extends FormRequest
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
            'clave' => 'required|string',
            'nombre' => 'required|string',
            'nombre_completo' => 'required|string',
            'carrera_id' => 'required',
            'semestre' => 'required',
            'ht' => 'required',
            'hp' => 'required',
            'cr' => 'required',
            'activo' => 'required',
        ];
    }
}
