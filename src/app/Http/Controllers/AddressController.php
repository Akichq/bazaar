<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function edit()
    {
        // 仮のダミー住所
        $address = (object)[
            'postcode' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-2-3',
            'building' => 'コーポABC 101号室'
        ];
        return view('address.edit', compact('address'));
    }

    public function update(Request $request)
    {
        // バリデーション・保存処理（今回はダミー）
        // ...
        return redirect()->route('items.purchase', 1)->with('success', '住所を更新しました');
    }
} 