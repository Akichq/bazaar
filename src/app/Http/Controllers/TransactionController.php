<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;

class TransactionController extends Controller
{
    /**
     * 取引一覧を表示
     */
    public function index()
    {
        $user = auth()->user();
        
        // 取引中の商品を取得（購入した商品）- 新規メッセージ順にソート
        $purchasedTransactions = Purchase::where('user_id', $user->id)
            ->where('is_completed', false)
            ->with(['item', 'item.user', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->sortByDesc(function($transaction) {
                return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
            });
            
        // 出品した商品の取引を取得 - 新規メッセージ順にソート
        $soldTransactions = Item::where('user_id', $user->id)
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
            
        // 全体の新着メッセージ数を計算
        $notificationCount = $this->calculateTotalNotificationCount($user->id);
        
        return view('transactions.index', compact('purchasedTransactions', 'soldTransactions', 'notificationCount'));
    }

    /**
     * 取引チャット画面を表示
     */
    public function show($transactionId)
    {
        $user = auth()->user();
        $transaction = Purchase::with(['item', 'item.user', 'user', 'messages.user'])
            ->findOrFail($transactionId);
            
        // 認証チェック
        if ($transaction->user_id !== $user->id && $transaction->item->user_id !== $user->id) {
            abort(403);
        }
        
        // 相手のユーザー情報を取得
        $otherUser = $transaction->user_id === $user->id ? $transaction->item->user : $transaction->user;
        
        // その他の取引を取得（サイドバー用）
        $otherTransactions = $this->getOtherTransactions($user->id, $transactionId);
        
        return view('transactions.show', compact('transaction', 'otherUser', 'otherTransactions'));
    }

    /**
     * 取引完了処理
     */
    public function complete(Request $request, $transactionId)
    {
        $transaction = Purchase::findOrFail($transactionId);
        
        // 認証チェック
        if ($transaction->user_id !== auth()->id() && $transaction->item->user_id !== auth()->id()) {
            abort(403);
        }
        
        $transaction->update(['is_completed' => true]);
        
        return redirect()->route('items.index')->with('success', '取引が完了しました');
    }

    /**
     * 全体の新着メッセージ数を計算
     */
    private function calculateTotalNotificationCount($userId)
    {
        // 購入した商品の新着メッセージ数
        $purchasedCount = Purchase::where('user_id', $userId)
            ->where('is_completed', false)
            ->withCount(['messages' => function($query) {
                $query->where('is_read', false);
            }])
            ->get()
            ->sum('messages_count');
            
        // 出品した商品の新着メッセージ数
        $soldCount = Item::where('user_id', $userId)
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
     * その他の取引を取得（サイドバー用）
     */
    private function getOtherTransactions($userId, $currentTransactionId)
    {
        // 購入した商品の取引
        $purchasedTransactions = Purchase::where('user_id', $userId)
            ->where('id', '!=', $currentTransactionId)
            ->where('is_completed', false)
            ->with(['item'])
            ->get();
            
        // 出品した商品の取引
        $soldTransactions = Item::where('user_id', $userId)
            ->whereHas('purchases', function($query) use ($currentTransactionId) {
                $query->where('id', '!=', $currentTransactionId)
                      ->where('is_completed', false);
            })
            ->with(['purchases' => function($query) {
                $query->with(['user']);
            }])
            ->get()
            ->flatMap->purchases;
            
        return $purchasedTransactions->merge($soldTransactions);
    }
}
