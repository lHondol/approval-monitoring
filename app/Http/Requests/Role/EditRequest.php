<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
            "name" => "required",
            'permissions' => 'required|array|min:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'permissions' => 'permissions',
            'permissions.*' => 'permissions',
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'Please assign at least one permission.',
        ];
    }
}
