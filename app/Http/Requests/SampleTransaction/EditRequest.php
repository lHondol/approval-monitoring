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
            'so_number' => 'required',
            'customer' => 'required',
            'so_created_at' => 'required',
            'shipment_request' => 'required',
            'picture_received_at' => 'required'
        ];
    }
}
