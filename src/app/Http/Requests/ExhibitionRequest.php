<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'price' => ['required', 'integer', 'min:0'],
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
            'name.required' => '商品名を入力してください。',
            'name.max' => '商品名は255文字以内で入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '商品画像をアップロードしてください。',
            'image.image' => '商品画像は画像ファイルをアップロードしてください。',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください。',
            'image.max' => '商品画像は2MB以下のサイズでアップロードしてください。',
            'categories.required' => 'カテゴリーを選択してください。',
            'categories.*.exists' => '選択されたカテゴリーは無効です。',
            'condition_id.required' => '商品の状態を選択してください。',
            'condition_id.exists' => '選択された商品の状態は無効です。',
            'price.required' => '商品価格を入力してください。',
            'price.integer' => '商品価格は整数で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
        ];
    }
} 