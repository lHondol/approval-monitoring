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
            'start_at' => 'required',
            'finish_at' => 'required',
            'files' => 'required|array|min:1',
            'files.*' => 'sometimes|image|max:5120',
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
            'files.*.mimes' => 'Each file must be a image',
            'files.*.max' => 'Each file must not exceed 5MB.',
        ];
    }
}
