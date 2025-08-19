<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="/">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECHロゴ" class="logo">
            </a>
        </div>
        <div class="header-center">
            <form method="GET" action="/" class="search-form">
                <input type="text" name="keyword" class="search-box" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                @if(request('page'))
                    <input type="hidden" name="page" value="{{ request('page') }}">
                @endif
                <button type="submit" class="hidden-button"></button>
            </form>
        </div>
        <div class="header-right">
            @guest
                <a href="/login" class="header-link">ログイン</a>
            @else
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="header-link logout-button">ログアウト</button>
                </form>
            @endguest
            <a href="/mypage" class="header-link">マイページ</a>
            <a href="/sell" class="header-btn">出品</a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>