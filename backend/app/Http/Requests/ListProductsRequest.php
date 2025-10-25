<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * List Products Request
 *
 * Validates query parameters for product listing
 */
class ListProductsRequest extends FormRequest
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
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'available_only' => 'sometimes|boolean',
            'in_stock_only' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page must be a valid integer',
            'page.min' => 'The page must be at least 1',
            'per_page.integer' => 'The per_page must be a valid integer',
            'per_page.min' => 'The per_page must be at least 1',
            'per_page.max' => 'The per_page may not be greater than 100',
        ];
    }
}
