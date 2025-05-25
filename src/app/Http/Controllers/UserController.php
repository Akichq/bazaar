<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // 仮のダミーユーザー・商品データ
        $user = (object)[
            'name' => 'ユーザー名',
            'profile_image' => null,
        ];
        $exhibited_items = array_fill(0, 6, (object)[
            'name' => '商品名',
            'image_url' => null,
        ]);
        $purchased_items = array_fill(0, 3, (object)[
            'name' => '商品名',
            'image_url' => null,
        ]);
        return view('users.mypage', compact('user', 'exhibited_items', 'purchased_items'));
    }
} 