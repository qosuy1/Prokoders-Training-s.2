<?php

namespace App\Http\Requests\V1\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole(['admin', 'editor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $this->category->id,
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category name already exists.',
            'name.max' => 'Category name must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 500 characters.',
        ];
    }
}
