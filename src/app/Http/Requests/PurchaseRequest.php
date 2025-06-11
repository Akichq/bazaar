<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if ($this->isMethod('get')) {
            return [];
        }

        return [
            'payment_method' => ['required', 'string', 'in:クレジットカード,コンビニ払い'],
            'address_id' => ['required', 'exists:addresses,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '選択された支払い方法は無効です。',
            'address_id.required' => '配送先を選択してください。',
            'address_id.exists' => '選択された配送先は無効です。',
        ];
    }
} 