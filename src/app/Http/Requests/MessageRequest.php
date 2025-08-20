<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
        return [
            'content' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
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
            'content.required' => '本文を入力してください',
            'content.max' => '本文は400文字以内で入力してください',
            'image.image' => '画像ファイルをアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max' => '画像は2MB以下のサイズでアップロードしてください',
        ];
    }
}

