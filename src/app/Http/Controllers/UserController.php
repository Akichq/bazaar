<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 評価平均値を取得
        $ratingController = new \App\Http\Controllers\RatingController();
        $averageRating = $ratingController->getAverageRating($user->id);
        
        // 取引通知数を計算
        $transactionNotificationCount = $this->calculateTransactionNotificationCount($user->id);
        
        // 出品した商品
        $exhibited_items = \App\Models\Item::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        // 購入した商品
        $purchased_items = \App\Models\Item::whereHas('purchases', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('is_sold', true)
          ->orderBy('created_at', 'desc')
          ->get();
          
        return view('users.mypage', compact('user', 'exhibited_items', 'purchased_items', 'averageRating', 'transactionNotificationCount'));
    }

    /**
     * 取引通知数を計算
     */
    private function calculateTransactionNotificationCount($userId)
    {
        // 購入した商品の新着メッセージ数（取引完了済みも含む）
        $purchasedCount = \App\Models\Purchase::where('user_id', $userId)
            ->where('is_completed', false)
            ->withCount(['messages' => function($query) {
                $query->where('is_read', false);
            }])
            ->get()
            ->sum('messages_count');
            
        // 出品した商品の新着メッセージ数（取引完了済みも含む）
        $soldCount = \App\Models\Item::where('user_id', $userId)
            ->whereHas('purchases', function($query) {
                $query->where('is_completed', false);
            })
            ->with(['purchases.messages' => function($query) {
                $query->where('is_read', false);
            }])
            ->get()
            ->flatMap->purchases
            ->flatMap->messages
            ->count();
            
        return $purchasedCount + $soldCount;
    }

    /**
     * 取引一覧を新規メッセージ順にソート
     */
    private function getSortedTransactions($userId)
    {
        // 購入した商品の取引を取得 - 新規メッセージ順にソート
        $purchasedTransactions = \App\Models\Purchase::where('user_id', $userId)
            ->where('is_completed', false)
            ->with(['item', 'item.user', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->sortByDesc(function($transaction) {
                return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
            });
            
        // 出品した商品の取引を取得 - 新規メッセージ順にソート
        $soldTransactions = \App\Models\Item::where('user_id', $userId)
            ->whereHas('purchases', function($query) {
                $query->where('is_completed', false);
            })
            ->with(['purchases' => function($query) {
                $query->with(['user', 'messages' => function($subQuery) {
                    $subQuery->orderBy('created_at', 'desc');
                }]);
            }])
            ->get()
            ->map(function($item) {
                $item->purchases = $item->purchases->sortByDesc(function($transaction) {
                    return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
                });
                return $item;
            });
            
        return compact('purchasedTransactions', 'soldTransactions');
    }

    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        $user = Auth::user();
        $address = \App\Models\Address::where('user_id', $user->id)->first();
        return view('users.profile.edit', compact('user', 'address'));
    }

    /**
     * プロフィール情報を更新
     */
    public function updateProfile(\App\Http\Requests\AddressRequest $request)
    {
        $user = \App\Models\User::find(Auth::id());

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::delete('public/profile_images/' . $user->profile_image);
            }
            $image = $request->file('profile_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/profile_images', $filename);
            $user->profile_image = $filename;
        }

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->save();

        // 住所テーブルに保存/更新
        \App\Models\Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('users.mypage')->with('success', 'プロフィールを更新しました。');
    }
} 