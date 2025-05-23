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
            <img src="{{ asset('logo.svg') }}" alt="COACHTECHロゴ" class="logo">
        </div>
        <div class="header-center">
            <input type="text" class="search-box" placeholder="なにをお探しですか？">
        </div>
        <div class="header-right">
            <a href="/login" class="header-link">ログイン</a>
            <a href="/mypage" class="header-link">マイページ</a>
            <a href="/sell" class="header-btn">出品</a>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html> 