<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みユーザーがコメントを送信できる
     */
    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'コメントテスト商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => 'テストコメント',
        ]);
        $response->assertRedirect('/item/' . $item->id);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
        $response2 = $this->get('/item/' . $item->id);
        $response2->assertSee('テストコメント');
    }

    /**
     * ログイン前ユーザーはコメントを送信できない
     */
    public function test_未ログインユーザーはコメントを送信できない()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '未ログインコメント商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 2000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => '未ログインコメント',
        ]);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => '未ログインコメント',
        ]);
    }

    /**
     * コメントが空の場合のバリデーション
     */
    public function test_コメントが空の場合バリデーションエラー()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '空コメント商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 3000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => '',
        ]);
        $response->assertSessionHasErrors(['content']);
    }

    /**
     * コメントが255字超過の場合のバリデーション
     */
    public function test_コメントが255字超過の場合バリデーションエラー()
    {
        $this->seed();
        $user = User::first();
        $condition = Condition::first() ?: Condition::create(['name' => '良好']);
        $item = Item::create([
            'user_id' => $user->id,
            'name' => '長文コメント商品',
            'brand_name' => null,
            'description' => '説明',
            'price' => 4000,
            'condition_id' => $condition->id,
            'image_url' => 'items/test.jpg',
            'is_sold' => false,
        ]);
        $this->actingAs($user);
        $longComment = str_repeat('あ', 256);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => $longComment,
        ]);
        $response->assertSessionHasErrors(['content']);
    }
} 