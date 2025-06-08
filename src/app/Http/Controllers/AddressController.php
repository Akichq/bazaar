<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function edit($itemId)
    {
        $item = \App\Models\Item::findOrFail($itemId);
        $address = \App\Models\Address::where('user_id', auth()->id())->first();
        return view('address.edit', compact('address', 'item'));
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'postcode' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        \App\Models\Address::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        // セッションに購入用住所を保存
        session([
            'purchase_address' => [
                'postal_code' => $request->postcode,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        ]);

        return redirect()->route('items.purchase', $itemId)->with('success', '住所を更新しました');
    }
} 