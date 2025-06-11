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
        $user = User::first();
        $categories = Category::all();
        $conditions = Condition::all();

        if (!$user || $categories->isEmpty() || $conditions->isEmpty()) {
            return;
        }

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'items/Armani_Mens_Clock.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'items/HDD_Hard_Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'items/iLoveIMG_d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'items/Leather_Shoes_Product_Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_url' => 'items/Living_Room_Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'items/Music_Mic_4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'items/Purse_fashion_pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_url' => 'items/Tumbler_souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_url' => 'items/Waitress_with_Coffee_Grinder.jpg',
                'condition' => '良好',
            ],
            [
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
                'user_id' => $user->id,
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
