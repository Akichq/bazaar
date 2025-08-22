# Mailtrap 設定手順

## 1. Mailtrap アカウントの作成

1. [Mailtrap.io](https://mailtrap.io) にアクセス
2. 無料アカウントを作成
3. ログイン後、新しい Sandbox を作成

## 2. 環境変数の設定

`.env`ファイルに以下の設定を追加してください：

```env
# メール送信設定
MAIL_MAILER=mailtrap
MAIL_FROM_ADDRESS=noreply@coachtech.com
MAIL_FROM_NAME="COACHTECHフリマ"

# Mailtrap設定
MAILTRAP_HOST=smtp.mailtrap.io
MAILTRAP_PORT=2525
MAILTRAP_ENCRYPTION=tls
MAILTRAP_USERNAME=your_mailtrap_username
MAILTRAP_PASSWORD=your_mailtrap_password
```

## 3. Mailtrap 認証情報の取得

1. 以下の情報をコピー：
    - Username
    - Password
    - Host (通常は smtp.mailtrap.io)
    - Port (通常は 2525)

## 4. 設定の反映

```bash
# 設定キャッシュをクリア
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan cache:clear
```

## 5. メール送信のテスト

### 方法 1: Artisan コマンドを使用

```bash
# 取引IDを指定してメール送信をテスト
docker-compose exec php php artisan mail:test-mailtrap {transaction_id}
```

### 方法 2: 実際の取引完了でテスト

1. 取引チャット画面で「取引を完了する」ボタンをクリック
2. 評価を送信
3. Mailtrap ダッシュボードでメールを確認

## 6. メールの確認

1. Mailtrap ダッシュボードで Inbox を開く
2. 送信されたメールを確認
3. メールの内容、HTML レンダリング、添付ファイルなどをテスト

## 注意事項

-   Mailtrap は開発・テスト環境専用です
-   本番環境では実際のメールサービス（SendGrid、Mailgun 等）を使用してください
-   無料プランでは月間 500 通まで送信可能です


