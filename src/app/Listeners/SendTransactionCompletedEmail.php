<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Events\TransactionCompleted;
use App\Mail\TransactionCompleted as TransactionCompletedMail;

class SendTransactionCompletedEmail
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TransactionCompleted  $event
     * @return void
     */
    public function handle(TransactionCompleted $event)
    {
        $transaction = $event->transaction;
        
        try {
            // 出品者にメールを送信
            Mail::to($transaction->item->user->email)
                ->send(new TransactionCompletedMail($transaction));
            
            // メール送信成功をログに記録
            Log::info('取引完了メール送信成功', [
                'transaction_id' => $transaction->id,
                'seller_email' => $transaction->item->user->email,
                'item_name' => $transaction->item->name
            ]);
        } catch (\Exception $e) {
            // メール送信エラーをログに記録（アプリケーションは停止しない）
            Log::error('メール送信エラー: ' . $e->getMessage());
        }
    }
}
