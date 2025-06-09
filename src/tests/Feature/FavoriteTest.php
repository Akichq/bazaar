<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいね登録と合計値増加のテスト
     */
    public function test_いいねアイコン押下でいいね登録と合計値増加()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'いいねテスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // いいね前の合計値を確認
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('0');
        // いいね押下（POST）
        $this->post('/item/' . $item->id . '/favorite');
        // いいね後の合計値を確認
        $response2 = $this->get('/item/' . $item->id);
        $response2->assertSee('1');
    }

    /**
     * いいね済みアイコンの色変化のテスト
     */
    public function test_いいね済みアイコンは色が変化する()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '色変化テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // いいね押下
        $this->post('/item/' . $item->id . '/favorite');
        // いいね済みアイコンの色変化を確認（例: class="favorite-active" など）
        $response = $this->get('/item/' . $item->id);
        $response->assertSee('liked');
    }

    /**
     * いいね解除と合計値減少のテスト
     */
    public function test_いいね解除で合計値減少()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '解除テスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 3000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        // いいね押下
        $this->post('/item/' . $item->id . '/favorite');
        // いいね解除（再度POST）
        $this->post('/item/' . $item->id . '/favorite');
        // いいね合計値が0に戻ることを確認
        $response = $this->get('/item/' . $item->id);
        $response->assertSee('0');
    }
} 