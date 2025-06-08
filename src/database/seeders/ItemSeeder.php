<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $categories = Category::all();
        $conditions = Condition::all();

        if ($users->isEmpty() || $categories->isEmpty() || $conditions->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            Item::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'name' => 'テスト商品 ' . ($i + 1),
                'brand_name' => 'テストブランド',
                'description' => 'これはテスト商品の説明です。',
                'price' => rand(1000, 100000),
                'condition_id' => $conditions->random()->id,
                'image_url' => 'items/test.jpg',
                'is_sold' => false,
            ]);
        }
    }
}
