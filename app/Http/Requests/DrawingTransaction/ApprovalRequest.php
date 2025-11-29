<?php

namespace App\Http\Requests\DrawingTransaction;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
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
        $transaction = $this->route('id') 
            ? \App\Models\DrawingTransaction::find($this->route('id'))
            : null;

        $isSecondApproval = $transaction &&
            $transaction->status === \App\Enums\StatusDrawingTransaction::WAITING_2ND_APPROVAL->value;

        return [
            'so_number' => $isSecondApproval ? 'required|string' : 'nullable|string',
            'reason'    => $this->input('action') === 'reject'
                ? 'required|string'
                : 'nullable|string',
        ];
    }

    protected $errorBag = 'approval';
}
