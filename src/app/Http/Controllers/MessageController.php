<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Models\Purchase;
use App\Http\Requests\MessageRequest;

class MessageController extends Controller
{
    /**
     * メッセージを投稿
     */
    public function store(MessageRequest $request, $transactionId)
    {
        $transaction = Purchase::findOrFail($transactionId);
        
        // 認証チェック
        if ($transaction->user_id !== auth()->id() && $transaction->item->user_id !== auth()->id()) {
            abort(403);
        }
        
        $data = $request->validated();
        
        // 画像アップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('messages', 'public');
        }
        
        Message::create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'image_url' => $imagePath,
            'is_read' => false,
        ]);
        
        return redirect()->route('transactions.show', $transactionId)->with('success', 'メッセージを送信しました');
    }

    /**
     * メッセージを編集
     */
    public function update(MessageRequest $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // 認証チェック（自分のメッセージのみ編集可能）
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }
        
        $data = $request->validated();
        
        // 画像アップロード処理
        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($message->image_url) {
                Storage::disk('public')->delete($message->image_url);
            }
            $imagePath = $request->file('image')->store('messages', 'public');
            $data['image_url'] = $imagePath;
        }
        
        $message->update($data);
        
        return redirect()->route('transactions.show', $message->transaction_id)->with('success', 'メッセージを更新しました');
    }

    /**
     * メッセージを削除
     */
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // 認証チェック（自分のメッセージのみ削除可能）
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }
        
        // 画像を削除
        if ($message->image_url) {
            Storage::disk('public')->delete($message->image_url);
        }
        
        $transactionId = $message->transaction_id;
        $message->delete();
        
        return redirect()->route('transactions.show', $transactionId)->with('success', 'メッセージを削除しました');
    }
}

