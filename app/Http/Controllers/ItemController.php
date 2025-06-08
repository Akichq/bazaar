<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isMylist = $request->page === 'mylist';
        $keyword = $request->input('keyword');

        dump([
            'isMylist' => $isMylist,
            'page' => $request->page,
            'user_id' => $user ? $user->id : null,
            'is_authenticated' => auth()->check()
        ]);

        if ($isMylist) {
            // マイリスト（いいね商品）
            if (!$user) {
                $items = collect(); // 未認証は空
            } else {
                $items = $user->favoriteItems()
                    ->with(['categories', 'condition'])
                    ->when($keyword, function($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->get();
                dump([
                    'favorite_items' => $items->toArray(),
                    'favorite_count' => $items->count()
                ]);
            }
        } else {
            // 全商品（自分の出品は除外）
            $items = \App\Models\Item::with(['categories', 'condition'])
                ->when($user, function($q) use ($user) {
                    $q->where('user_id', '!=', $user->id);
                })
                ->when($keyword, function($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                })
                ->get();
            dump([
                'all_items' => $items->toArray(),
                'all_items_count' => $items->count()
            ]);
        }

        // Soldフラグ付与（例: is_sold プロパティ）
        foreach ($items as $item) {
            $item->is_sold = $item->is_sold ?? false; // 本来はDBの購入済み判定
        }

        return view('items.index', [
            'items' => $items,
            'isMylist' => $isMylist,
            'keyword' => $keyword,
        ]);
    }

    public function show(\App\Models\Item $item)
    {
        // 商品の詳細情報を取得（リレーション含む）
        $item->load(['categories', 'condition', 'comments.user', 'favorites']);
        
        // 現在のユーザーがこの商品をいいねしているかチェック
        $isLiked = false;
        if (auth()->check()) {
            $isLiked = $item->favorites()->where('user_id', auth()->id())->exists();
        }
        
        return view('items.show', compact('item', 'isLiked'));
    }

    public function comment(Request $request, \App\Models\Item $item)
    {
        $request->validate([
            'content' => 'required|max:255'
        ]);

        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return redirect()->route('items.show', $item->id)->with('success', 'コメントを投稿しました。');
    }

    public function toggleFavorite(\App\Models\Item $item)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $favorite = $user->favoriteItems()->where('item_id', $item->id)->first();
        
        if ($favorite) {
            // いいねを削除
            $user->favoriteItems()->detach($item->id);
            $message = 'いいねを削除しました。';
        } else {
            // いいねを追加
            $user->favoriteItems()->attach($item->id);
            $message = 'いいねしました。';
        }

        return redirect()->route('items.show', $item->id)->with('success', $message);
    }
} 