<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController
{
    public function store(RegisterRequest $request): RegisterResponse
    {
        $user = app(CreateNewUser::class)->create($request->all());

        event(new Registered($user));

        Auth::login($user);

        // 新規登録時のみプロフィール編集画面へ
        return redirect('/mypage/profile/edit');
    }
} 