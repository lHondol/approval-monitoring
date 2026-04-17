<?php

namespace App\Http\Requests\SampleTransaction;

use Illuminate\Foundation\Http\FormRequest;

class CreateProcessRequest extends FormRequest
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
            'process' => 'required',
            'start_note' => 'nullable',
            'file' => 'required|file|image',
        ];
    }

        
    public function attributes(): array
    {
        return [
            'file' => 'file',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Each file must be a image',
            'file.max' => 'Each file must not exceed 5MB.',
        ];
    }

}
