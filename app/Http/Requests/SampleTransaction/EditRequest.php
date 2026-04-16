<?php

namespace App\Http\Requests\SampleTransaction;

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
            'customer' => 'required',
            'so_created_at' => 'required',
            'shipment_request' => 'required',
            // 'picture_received_at' => 'required',
            'note' => 'nullable',
            'files' => 'nullable|array|min:1',
            'files.*' => 'sometimes|file|mimes:pdf|max:5120',
            'existing_file' => 'nullable|string',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
    
            if (!$this->hasFile('files') && !$this->filled('existing_file')) {
                $validator->errors()->add('files', 'Please upload at least one file.');
            }
    
        });
    }
}
