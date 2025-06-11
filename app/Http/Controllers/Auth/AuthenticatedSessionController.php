<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // プロフィール情報が未入力の場合、プロフィール編集画面にリダイレクト
            if (!$user->isProfileCompleted()) {
                return redirect()->route('users.editProfile')
                    ->with('warning', 'プロフィール情報を入力してください。');
            }
            
            // プロフィール情報が入力済みの場合、マイページにリダイレクト
            return redirect()->intended('/mypage');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ])->withInput($request->only('email'));
    }
} 