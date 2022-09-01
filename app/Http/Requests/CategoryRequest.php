<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'en_title' => 'required|unique:categories,en_title|min:6',
            'ar_title' => 'required|unique:categories,ar_title|min:6',
            'is_active' => 'required|boolean',
        ];
    }
}