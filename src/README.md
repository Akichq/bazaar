# コーチテックフリマ - 追加機能

## 追加機能概要

このプロジェクトには以下の追加機能が実装されています：

### 1. 取引チャット機能

-   取引中の商品のメッセージやり取り
-   画像添付機能（JPEG/PNG 形式）
-   メッセージの編集・削除機能
-   新着メッセージ通知

### 2. 取引評価機能

-   取引完了後の相互評価システム
-   評価平均値の表示
-   星評価（1-5 段階）

### 3. メール通知機能

-   取引完了時の自動メール送信
-   Mailtrap を使用した開発環境でのメールテスト

## 環境構築

### 前提条件

-   Docker
-   Docker Compose

### セットアップ手順

1. **リポジトリをクローン**

    ```bash
    git clone https://github.com/Akichq/bazaar.git
    cd bazaar
    ```

2. **Docker イメージのビルド・起動**

    ```bash
    docker-compose build
    docker-compose up -d
    ```

3. **コンテナに入る**

    ```bash
    docker-compose exec php bash
    ```

4. **依存パッケージインストール**

    ```bash
    composer install
    ```

5. **.env ファイルの作成・編集**

    ```bash
    cp .env.example .env
    # 必要に応じてDBやメール設定を編集
    ```

6. **アプリケーションキー発行**

    ```bash
    php artisan key:generate
    ```

7. **マイグレーション・シーディング**

    ```bash
    php artisan migrate --seed
    ```

8. **ストレージリンク作成**
    ```bash
    php artisan storage:link
    ```

## ダミーデータ情報

### ユーザー情報

以下のダミーユーザーが作成されます：

| 名前     | メールアドレス     | パスワード | 役割                    |
| -------- | ------------------ | ---------- | ----------------------- |
| 田中太郎 | tanaka@example.com | password   | 商品出品者（CO01-CO05） |
| 佐藤花子 | sato@example.com   | password   | 商品出品者（CO06-CO10） |
| 山田次郎 | yamada@example.com | password   | 一般ユーザー            |

### 商品情報

-   **田中太郎の商品（CO01-CO05）**：

    -   腕時計（¥15,000）
    -   HDD（¥5,000）
    -   玉ねぎ 3 束（¥300）
    -   革靴（¥4,000）
    -   ノート PC（¥45,000）

-   **佐藤花子の商品（CO06-CO10）**：
    -   マイク（¥8,000）
    -   ショルダーバッグ（¥3,500）
    -   タンブラー（¥500）
    -   コーヒーミル（¥4,000）
    -   メイクセット（¥2,500）

## 開発言語・技術スタック

-   **バックエンド**: PHP 7.4.9, Laravel 8.83.8
-   **データベース**: MySQL
-   **フロントエンド**: HTML, CSS, JavaScript
-   **コンテナ**: Docker, Docker Compose
-   **認証**: Laravel Fortify
-   **決済**: Stripe（テスト環境）
-   **メール**: Mailtrap（開発環境）

## Mailtrap 設定方法

メール機能のテストには Mailtrap を使用しています。詳細な設定方法は以下を参照してください：

[src/MAILTRAP_SETUP.md](src/MAILTRAP_SETUP.md)

### 簡単な設定手順

1. [Mailtrap.io](https://mailtrap.io)でアカウント作成
2. `.env`ファイルに Mailtrap 設定を追加
3. 設定キャッシュをクリア
4. メール送信テストを実行

## 新規追加 URL

### 取引関連

-   取引一覧画面： `/transactions`
-   取引チャット画面： `/transaction/{transaction_id}`

### 既存 URL の拡張

-   マイページ： `/mypage` （取引中商品タブ追加）
-   プロフィール編集： `/mypage/profile` （評価平均表示追加）

## 機能テスト方法

### 1. 取引チャット機能

1. 商品を購入
2. マイページの「取引中の商品」タブを確認
3. 取引チャット画面でメッセージ送信
4. 画像添付機能をテスト
5. メッセージの編集・削除をテスト

### 2. 評価機能

1. 取引チャット画面で「取引を完了する」ボタンをクリック
2. 評価モーダルで星評価とコメントを入力
3. マイページで評価平均を確認

### 3. メール機能

```bash
# メール送信テスト
docker-compose exec php php artisan mail:test-mailtrap {transaction_id}
```

## 注意事項

-   画像アップロードは JPEG/PNG 形式のみ対応
-   メッセージは最大 400 文字まで入力可能
