<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィール情報が正しく表示される()
    {
        // テストユーザーの作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'test.jpg'
        ]);

        // 商品の状態を作成
        $condition = Condition::create(['name' => '良好']);

        // 出品した商品を作成
        $exhibitedItem = Item::create([
            'user_id' => $user->id,
            'name' => '出品した商品',
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        // 購入した商品を作成（別ユーザーが出品）
        $otherUser = User::factory()->create();
        $purchasedItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => '購入した商品',
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        // 購入情報を作成
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル'
        ]);

        $purchasedItem->purchases()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'コンビニ払い',
            'stripe_payment_id' => null,
        ]);

        // ログインしてプロフィールページにアクセス
        $this->actingAs($user);
        $response = $this->get('/mypage');

        // ステータスコードの確認
        $response->assertStatus(200);

        // プロフィール情報の確認
        $response->assertSee($user->name); // ユーザー名
        $response->assertSee($user->profile_image); // プロフィール画像

        // 出品した商品の確認
        $response->assertSee($exhibitedItem->name);
        $response->assertSee('出品した商品');

        // 購入した商品の確認
        $response->assertSee($purchasedItem->name);
        $response->assertSee('購入した商品');

        // タブの確認
        $response->assertSee('出品した商品');
        $response->assertSee('購入した商品');
    }

    public function test_プロフィール編集ページで初期値が正しく表示される()
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'test.jpg'
        ]);

        // 住所情報を作成
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building' => 'テストビル'
        ]);

        // ログインしてプロフィール編集ページにアクセス
        $response = $this->actingAs($user)
            ->get('/mypage/profile');

        // ステータスコードの確認
        $response->assertStatus(200);

        // 初期値の表示確認
        $response->assertSee('http://localhost/storage/profile_images/test.jpg'); // プロフィール画像
        $response->assertSee('name="name" value="テストユーザー"', false); // ユーザー名
        $response->assertSee('name="postal_code" value="123-4567"', false); // 郵便番号
        $response->assertSee('name="address" value="テスト住所"', false); // 住所
        $response->assertSee('name="building" value="テストビル"', false); // 建物名
    }
} 