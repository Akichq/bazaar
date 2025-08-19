<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Purchase;

class RatingController extends Controller
{
    /**
     * 評価平均値を取得
     */
    public function getAverageRating($userId)
    {
        $ratings = Rating::where('rated_user_id', $userId)->get();
        
        if ($ratings->isEmpty()) {
            return 0;
        }
        
        $average = $ratings->avg('rating');
        return round($average); // 四捨五入
    }

    /**
     * 評価を保存
     */
    public function store(Request $request, $transactionId)
    {
        $transaction = Purchase::findOrFail($transactionId);
        
        // 認証チェック
        if ($transaction->user_id !== auth()->id() && $transaction->item->user_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);
        
        // 評価対象のユーザーIDを決定
        $ratedUserId = $transaction->user_id === auth()->id() 
            ? $transaction->item->user_id 
            : $transaction->user_id;
        
        Rating::create([
            'user_id' => auth()->id(),
            'rated_user_id' => $ratedUserId,
            'transaction_id' => $transactionId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('items.index')->with('success', '評価を送信しました');
    }
}
