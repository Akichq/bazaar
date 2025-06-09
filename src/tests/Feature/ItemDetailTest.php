<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要な情報が表示される()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => '家電']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 12345,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);
        // いいね
        Favorite::create(['user_id' => $user->id, 'item_id' => $item->id]);
        // コメント
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'とても良い商品です！',
        ]);
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee($item->brand_name);
        $response->assertSee($item->description);
        $response->assertSee('¥' . number_format($item->price));
        $response->assertSee($condition->name);
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee('いいね');
        $response->assertSee('コメント');
        $response->assertSee('とても良い商品です！');
        $response->assertSee($user->name);
    }

    public function test_複数カテゴリが商品詳細ページに表示される()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => '家電']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'カテゴリテスト商品',
            'brand_name' => 'ブランドA',
            'description' => 'カテゴリテスト説明',
            'price' => 9999,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }
} 