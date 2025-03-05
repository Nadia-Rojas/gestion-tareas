<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;  // Asegúrate de cambiar esto si hay lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad' => 'required|string|in:baja,media,alta',
            'estado' => 'required|string|in:pendiente,en progreso,completada',
        ];
    }
}
