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

        // 田中太郎（CO01～CO05）
        $tanaka = $users->where('email', 'tanaka@example.com')->first();
        // 佐藤花子（CO06～CO10）
        $sato = $users->where('email', 'sato@example.com')->first();

        if (!$tanaka || !$sato) {
            return;
        }

        $items = [
            // 田中太郎の商品（CO01～CO05）
            [
                'user_id' => $tanaka->id,
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'items/Armani_Mens_Clock.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $tanaka->id,
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'items/HDD_Hard_Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $tanaka->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'items/iLoveIMG_d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $tanaka->id,
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'items/Leather_Shoes_Product_Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $tanaka->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_url' => 'items/Living_Room_Laptop.jpg',
                'condition' => '良好',
            ],
            // 佐藤花子の商品（CO06～CO10）
            [
                'user_id' => $sato->id,
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'items/Music_Mic_4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $sato->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'items/Purse_fashion_pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $sato->id,
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_url' => 'items/Tumbler_souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $sato->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_url' => 'items/Waitress_with_Coffee_Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $sato->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_url' => 'items/Makeup_Set.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $itemData) {
            $condition = $conditions->where('name', $itemData['condition'])->first();
            $category = $categories->random();
            $item = Item::create([
                'user_id' => $itemData['user_id'],
                'name' => $itemData['name'],
                'brand_name' => null,
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'condition_id' => $condition ? $condition->id : $conditions->first()->id,
                'image_url' => $itemData['image_url'],
                'is_sold' => false,
            ]);
            $item->categories()->attach($category->id);
        }
    }
}
