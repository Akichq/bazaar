<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ユーザー1: CO01～CO05の商品を出品
        User::create([
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'password' => Hash::make('password'),
        ]);

        // ユーザー2: CO06～CO10の商品を出品
        User::create([
            'name' => '佐藤花子',
            'email' => 'sato@example.com',
            'password' => Hash::make('password'),
        ]);

        // ユーザー3: 何も紐づけられていないユーザー
        User::create([
            'name' => '山田次郎',
            'email' => 'yamada@example.com',
            'password' => Hash::make('password'),
        ]);

        // 既存のadminユーザーも残す
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }
} 