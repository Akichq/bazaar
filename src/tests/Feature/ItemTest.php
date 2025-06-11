<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品情報が正しく保存されることを確認
     */
    public function test_商品出品情報が正しく保存される()
    {
        // ユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリ・商品の状態を作成
        $category = Category::create(['name' => 'テストカテゴリ']);
        $condition = Condition::create(['name' => '新品']);

        // ダミー画像を用意（画像バリデーション回避用）
        $imagePath = base_path('tests/Feature/dummy.jpg');
        copy(base_path('tests/Feature/stub.jpg'), $imagePath);
        $image = new \Illuminate\Http\UploadedFile($imagePath, 'dummy.jpg', null, null, true);

        // 商品出品リクエスト
        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明',
            'price' => 1234,
            'condition_id' => $condition->id,
            'categories' => [$category->id],
            'image' => $image,
        ]);

        $response->assertRedirect();

        // DBに保存されているか確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明',
            'price' => 1234,
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);

        $item = Item::where('name', 'テスト商品')->first();
        $this->assertTrue($item->categories->contains($category->id));
    }
} 