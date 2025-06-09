<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 支払い方法プルダウンが購入画面に表示されていることを確認
     */
    public function test_購入画面に支払い方法プルダウンが表示される()
    {
        $this->seed();
        $user = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '支払い方法テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1500,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        $response = $this->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('コンビニ払い');
        $response->assertSee('クレジットカード');
    }

    /**
     * 支払い方法を選択して購入処理を行うと、選択値が反映されることを確認
     */
    public function test_支払い方法選択後に購入処理で値が反映される()
    {
        $this->seed();
        $user = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '支払い方法テスト商品2',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // コンビニ払いで購入
        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => 'コンビニ払い',
            'postal_code' => $address->postal_code,
            'address' => $address->address,
            'building' => $address->building,
        ]);
        $response->assertRedirect('/item/' . $item->id);
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ払い',
        ]);
        // 別商品でクレジットカード払い
        $item2 = Item::create([
            'user_id' => $user->id,
            'name' => '支払い方法テスト商品3',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2500,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $response2 = $this->post('/purchase/' . $item2->id, [
            'payment_method' => 'クレジットカード',
            'postal_code' => $address->postal_code,
            'address' => $address->address,
            'building' => $address->building,
        ]);
        // Stripe決済画面へのリダイレクトを想定（テスト環境ではURLリダイレクトのみ確認）
        $response2->assertStatus(302);
    }
} 