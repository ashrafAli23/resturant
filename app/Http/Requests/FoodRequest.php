<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
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
            'en_title' => 'required|unique:food,en_title|string|min:6',
            'ar_title' => 'required|unique:food,ar_title|string|min:6',
            'en_description' => 'required|string|min:6',
            'ar_description' => 'required|string|min:6',
            'category_id' => 'required|numeric',
        ];
    }
}