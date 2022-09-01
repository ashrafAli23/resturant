<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'en_title' => 'required|unique:blogs,en_title|min:6|string',
            'ar_title' => 'required|unique:blogs,ar_title|min:6|string',
            'en_description' => 'required|min:6|string',
            'ar_description' => 'required|min:6|string',
            'image' => 'nullable|file',
            'date' => 'required|string',
        ];
    }
}