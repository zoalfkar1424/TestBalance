<?php

namespace App\Modules\Balance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_user_id' => ['required', 'integer', 'exists:users,id'],
            'to_user_id' => ['required', 'integer', 'exists:users,id', 'different:from_user_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'from_user_id.required' => 'From User ID is required',
            'from_user_id.exists' => 'From User not found',
            'to_user_id.required' => 'To User ID is required',
            'to_user_id.exists' => 'To User not found',
            'to_user_id.different' => 'Cannot transfer to the same user',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than 0',
        ];
    }
}

