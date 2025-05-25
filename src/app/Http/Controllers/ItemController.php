<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('items.index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load('categories', 'condition', 'comments.user');
        return view('items.show', compact('item'));
    }

    public function comment(Request $request, Item $item)
    {
        $request->validate([
            'content' => 'required|max:255'
        ]);
        $item->comments()->create([
            'user_id' => auth()->id() ?? 1, // 未ログイン時は仮で1
            'content' => $request->content
        ]);
        return redirect()->route('items.show', $item->id);
    }

    public function purchase(Item $item)
    {
        // 仮のダミー住所・支払い方法
        $address = (object)[
            'postcode' => '123-4567',
            'full_address' => '東京都渋谷区道玄坂1-2-3 コーポABC 101号室'
        ];
        $payment_method = 'コンビニ払い';
        return view('items.purchase', compact('item', 'address', 'payment_method'));
    }

    public function purchaseStore(Request $request, Item $item)
    {
        // ここで購入処理（例：DB保存、バリデーション等）
        // 今回はダミーでリダイレクトのみ
        return redirect()->route('items.show', $item->id)->with('success', '購入が完了しました！');
    }
}
