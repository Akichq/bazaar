<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 購入ボタン押下で購入が完了する
     */
    public function test_購入ボタン押下で購入が完了する()
    {
        $this->seed();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $address = Address::first() ?: Address::create([
            'user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => '購入テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($buyer);
        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => 'コンビニ払い',
            'postal_code' => $address->postal_code,
            'address' => $address->address,
            'building' => $address->building,
        ]);
        $response->assertRedirect('/item/' . $item->id);
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * 購入した商品は商品一覧でSOLD表示される
     */
    public function test_購入した商品は商品一覧でSOLD表示()
    {
        $this->seed();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $address = Address::first() ?: Address::create([
            'user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'SOLDテスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($buyer);
        $this->post('/purchase/' . $item->id, [
            'payment_method' => 'コンビニ払い',
            'postal_code' => $address->postal_code,
            'address' => $address->address,
            'building' => $address->building,
        ]);
        $response = $this->get('/');
        $response->assertSee('SOLD');
    }

    /**
     * 購入した商品がプロフィールの購入一覧に追加されている
     */
    public function test_購入した商品がプロフィール購入一覧に追加()
    {
        $this->seed();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $address = Address::first() ?: Address::create([
            'user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'プロフィール購入商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 3000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($buyer);
        $this->post('/purchase/' . $item->id, [
            'payment_method' => 'コンビニ払い',
            'postal_code' => $address->postal_code,
            'address' => $address->address,
            'building' => $address->building,
        ]);
        $response = $this->get('/mypage');
        $response->assertSee($item->name);
    }
} 