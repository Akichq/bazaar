@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
<div class="register-container">
    <div class="register-header">
        <h2 class="register-title">会員登録</h2>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="register-form-group">
            <label class="register-label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name') }}" class="register-input">
            @error('name')
                <div class="register-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="register-form-group">
            <label class="register-label">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" class="register-input">
            @error('email')
                <div class="register-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="register-form-group">
            <label class="register-label">パスワード</label>
            <input type="password" name="password" class="register-input">
            @error('password')
                <div class="register-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="register-form-group">
            <label class="register-label">確認用パスワード</label>
            <input type="password" name="password_confirmation" class="register-input">
            @error('password_confirmation')
                <div class="register-error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="register-btn">登録する</button>
    </form>
    <div class="register-link-wrap">
        <a href="{{ route('login') }}" class="register-link">ログインはこちら</a>
    </div>
</div>
@endsection 