<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'photoUrls' => 'nullable|string',
            'imageFile' => 'nullable|image|max:2048',
        ];
    }
}
