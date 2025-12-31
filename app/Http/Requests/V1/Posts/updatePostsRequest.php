<?php

namespace App\Http\Requests\v1\Posts;

use Illuminate\Foundation\Http\FormRequest;

class updatePostsRequest extends FormRequest
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
            'title' => 'max:255|string',
            'body' => "string",
            'author_id' => "exists:authors,id",
            "categories_id" => "sometimes|nullable|array"
        ];
    }
}
