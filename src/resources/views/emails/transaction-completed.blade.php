<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>取引完了通知</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ff6f6f;">取引が完了しました</h2>
        
        <p>こんにちは、{{ $transaction->item->user->name }}さん</p>
        
        <p>商品「{{ $transaction->item->name }}」の取引が完了しました。</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>取引詳細</h3>
            <p><strong>商品名:</strong> {{ $transaction->item->name }}</p>
            <p><strong>価格:</strong> ¥{{ number_format($transaction->item->price) }}</p>
            <p><strong>購入者:</strong> {{ $transaction->user->name }}</p>
            <p><strong>取引完了日時:</strong> {{ $transaction->updated_at->format('Y年m月d日 H:i') }}</p>
        </div>
        
        <p>取引チャット画面で購入者からの評価を確認できます。</p>
        
        <p>引き続きCOACHTECHフリマをご利用ください。</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">
            このメールはCOACHTECHフリマから自動送信されています。<br>
            ご不明な点がございましたら、お気軽にお問い合わせください。
        </p>
    </div>
</body>
</html>

