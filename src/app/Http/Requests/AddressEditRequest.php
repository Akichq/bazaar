<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressEditRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'postcode.required' => '郵便番号を入力してください。',
            'postcode.regex' => '郵便番号はハイフンを含む8文字で入力してください。（例：123-4567）',
            'address.required' => '住所を入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building.required' => '建物名を入力してください。',
            'building.max' => '建物名は255文字以内で入力してください。',
        ];
    }
} 