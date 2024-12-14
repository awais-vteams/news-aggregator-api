<?php

namespace App\Domains\News\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllArticleRequest extends FormRequest
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
            'keyword' => 'nullable|string',
            'date' => 'nullable|date',
            'category' => 'nullable|string',
            'source' => 'nullable|string',
        ];
    }
}
