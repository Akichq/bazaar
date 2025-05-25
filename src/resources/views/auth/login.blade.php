@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<div class="login-container">
    <h2 class="login-title">ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button type="submit" class="login-btn">ログインする</button>
    </form>
    <div class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
@endsection 