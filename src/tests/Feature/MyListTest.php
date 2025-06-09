<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねした商品だけが表示される()
    {
        $this->seed();
        $user = User::first();
        $otherUser = User::factory()->create();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->actingAs($user);
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_購入済み商品はSoldと表示される()
    {
        $this->seed();
        $user = User::first();
        $otherUser = User::factory()->create();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => '購入済み商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $address = Address::first();
        if (!$address) {
            $address = Address::create([
                'user_id' => $user->id,
                'postal_code' => '123-4567',
                'address' => '東京都テスト区1-2-3',
                'building' => 'テストビル101'
            ]);
        }
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $item->purchases()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'テスト',
            'stripe_payment_id' => null,
        ]);
        $this->actingAs($user);
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    public function test_自分が出品した商品は表示されない()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        $myItem = Item::create([
            'user_id' => $user->id,
            'name' => '自分の商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 3000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $otherUser = User::factory()->create();
        $otherItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 4000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $otherItem->id,
        ]);
        $this->actingAs($user);
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertDontSee($myItem->name);
        $response->assertSee($otherItem->name);
    }

    public function test_未認証の場合は何も表示されない()
    {
        $this->seed();
        $user = User::first();
        $otherUser = User::factory()->create();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        $item = Item::create([
            'user_id' => $otherUser->id,
            'name' => '未認証テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 5000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertDontSee($item->name);
    }
} 