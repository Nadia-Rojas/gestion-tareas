<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado_id' => 'required|exists:estado,id',
            'prioridad_id' => 'required|exists:prioridad,id',
            'creador_id' => 'required|exists:usuarios,id',
        ];
    }
}
