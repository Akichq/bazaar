<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品名で部分一致検索ができる()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        // 商品を2つ作成
        $item1 = Item::create([
            'user_id' => $user->id,
            'name' => 'アップルウォッチ',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $item2 = Item::create([
            'user_id' => $user->id,
            'name' => 'バナナバッグ',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        // 検索（「ウォッチ」で部分一致）
        $response = $this->get('/?keyword=ウォッチ');
        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first();
        if (!$condition) {
            $condition = Condition::create(['name' => '良好']);
        }
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テストウォッチ',
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
        // ホームで検索
        $response = $this->get('/?keyword=ウォッチ');
        $response->assertStatus(200);
        $response->assertSee('ウォッチ'); // 検索キーワードが保持されている
        // マイリストページに遷移
        $response2 = $this->get('/?page=mylist&keyword=ウォッチ');
        $response2->assertStatus(200);
        $response2->assertSee('ウォッチ'); // 検索キーワードが保持されている
    }
} 