<?php

namespace App\Http\Requests\SampleTransaction;

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
            'so_number' => 'required',
            'customer' => 'required',
            // 'so_created_at' => 'required',
            'shipment_request' => 'required',
            // 'picture_received_at' => 'required',
            'note' => 'nullable',
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
