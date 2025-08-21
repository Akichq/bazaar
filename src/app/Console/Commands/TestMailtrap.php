<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompleted;
use App\Models\Purchase;

class TestMailtrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-mailtrap {transaction_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mailtrapでメール送信をテストする';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactionId = $this->argument('transaction_id');
        
        try {
            $transaction = Purchase::with(['item.user', 'user'])->findOrFail($transactionId);
            
            $this->info('取引情報を取得しました:');
            $this->line("商品: {$transaction->item->name}");
            $this->line("出品者: {$transaction->item->user->name} ({$transaction->item->user->email})");
            $this->line("購入者: {$transaction->user->name}");
            
            // メール送信
            Mail::to($transaction->item->user->email)
                ->send(new TransactionCompleted($transaction));
            
            $this->info('メール送信が完了しました！');
            $this->line('Mailtrapのダッシュボードでメールを確認してください。');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('エラーが発生しました: ' . $e->getMessage());
            return 1;
        }
    }
}

