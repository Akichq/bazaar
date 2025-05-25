<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['ファッション', '家電', '食品', '日用品', '趣味・ホビー', 'その他'];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
} 