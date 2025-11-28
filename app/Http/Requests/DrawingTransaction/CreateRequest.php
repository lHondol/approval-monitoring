<?php

namespace App\Http\Requests\DrawingTransaction;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            "customer_name" => "required",
            "po_number" => "required",
            "description" => "nullable",
            'files' => 'required|array|min:1',
            'files.*' => 'sometimes|file|mimes:pdf|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'files',
            'files.*' => 'files',
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Please upload at least one file.',
            'files.*.mimes' => 'Each file must be a pdf',
            'files.*.max' => 'Each file must not exceed 5MB.',
        ];
    }
}
