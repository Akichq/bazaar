<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressEditRequest;
use App\Models\Address;

class AddressController extends Controller
{
    public function edit($itemId)
    {
        $item = \App\Models\Item::findOrFail($itemId);
        
        // セッションから住所情報を取得
        $sessionAddress = session('purchase_address');
        if ($sessionAddress) {
            $address = (object)[
                'postcode' => $sessionAddress['postal_code'],
                'address' => $sessionAddress['address'],
                'building' => $sessionAddress['building']
            ];
        } else {
            // セッションにない場合はDBから取得
            $address = Address::where('user_id', auth()->id())->first();
        }
        
        return view('address.edit', compact('address', 'item'));
    }

    public function update(\App\Http\Requests\AddressEditRequest $request, $itemId)
    {
        $address = Address::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        session([
            'purchase_address' => [
                'postal_code' => $request->postcode,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        ]);

        $item = \App\Models\Item::findOrFail($itemId);
        return redirect()->route('items.purchase', ['item' => $item->id])->with('success', '住所を更新しました');
    }
} 