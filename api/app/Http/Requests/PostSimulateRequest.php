<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostSimulateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules() : array
    {
        return [
          'cpf' => 'required|min:11|max:11|in:11111111111,12312312312,22222222222',
          'simulateValue' => 'required|numeric'
        ];
    }
}
