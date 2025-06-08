<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
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
            <form method="GET" action="/" style="display: flex; align-items: center;">
                <input type="text" name="keyword" class="search-box" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                @if(request('page'))
                    <input type="hidden" name="page" value="{{ request('page') }}">
                @endif
                <button type="submit" style="display:none;"></button>
            </form>
        </div>
        <div class="header-right">
            @guest
                <a href="/login" class="header-link">ログイン</a>
            @else
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="header-link" style="background:none;border:none;cursor:pointer;">ログアウト</button>
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