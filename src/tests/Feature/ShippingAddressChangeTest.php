<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 住所変更後、購入画面に新しい住所が反映されることを確認
     */
    public function test_住所変更後に購入画面に新しい住所が反映される()
    {
        $this->seed();
        $user = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        // もともとの住所
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都元住所1-1-1',
            'building' => '旧ビル101',
        ]);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '住所反映テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1500,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // 住所変更画面で新しい住所を登録
        $response = $this->post('/purchase/address/' . $item->id, [
            'postcode' => '222-2222',
            'address' => '東京都新住所2-2-2',
            'building' => '新ビル202',
        ]);
        $response->assertRedirect('/purchase/' . $item->id);
        // 購入画面で新しい住所が反映されているか確認
        $response2 = $this->get('/purchase/' . $item->id);
        $response2->assertSee('222-2222');
        $response2->assertSee('東京都新住所2-2-2');
        $response2->assertSee('新ビル202');
    }

    /**
     * 購入した商品に新しい住所が紐づいて登録されることを確認
     */
    public function test_購入時に新しい住所がpurchasesテーブルに保存される()
    {
        $this->seed();
        $user = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        // もともとの住所
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都元住所1-1-1',
            'building' => '旧ビル101',
        ]);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '住所紐付けテスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // 住所変更
        $this->post('/purchase/address/' . $item->id, [
            'postcode' => '333-3333',
            'address' => '東京都購入用住所3-3-3',
            'building' => '購入ビル303',
        ]);
        // 購入処理
        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => 'コンビニ払い',
            'postal_code' => '333-3333',
            'address' => '東京都購入用住所3-3-3',
            'building' => '購入ビル303',
        ]);
        $response->assertRedirect('/item/' . $item->id);
        $newAddress = Address::where('user_id', $user->id)
            ->where('postal_code', '333-3333')
            ->where('address', '東京都購入用住所3-3-3')
            ->where('building', '購入ビル303')
            ->first();
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $newAddress->id,
        ]);
    }
} 