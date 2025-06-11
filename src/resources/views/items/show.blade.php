@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/items/show.css?v=' . time()) }}">

<div class="item-detail-container">
    <div class="item-detail-left">
        <div class="item-detail-image">
            @if($item->image_url)
                <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}">
            @else
                商品画像
            @endif
        </div>
    </div>

    <div class="item-detail-right">
        <h1 class="item-title">{{ $item->name }}</h1>
        @if($item->brand_name)
            <div class="item-brand" style="font-size: 0.95em; color: #888; margin-bottom: 8px;">
                {{ $item->brand_name }}
            </div>
        @endif
        <div class="item-price">¥{{ number_format($item->price) }} <span class="tax">(税込)</span></div>

        <div class="item-stats">
            @auth
                <form method="POST" action="{{ route('items.favorite', $item->id) }}" class="favorite-form">
                    @csrf
                    <button type="submit" class="favorite-btn">
                        <span class="favorite-icon {{ $isLiked ? 'liked' : '' }}">
                            {{ $isLiked ? '★' : '☆' }}
                        </span>
                        <span>{{ $item->favorites->count() }}</span>
                    </button>
                </form>
            @else
                <div class="stat-item">
                    <span class="favorite-icon">☆</span>
                    <span>{{ $item->favorites->count() }}</span>
                </div>
            @endauth

            <div class="stat-item">
                <span class="comment-icon">💬</span>
                <span>{{ $item->comments->count() }}</span>
            </div>
        </div>

        <div class="buy-btn-wrapper">
            <a href="{{ route('items.purchase', $item->id) }}" class="buy-btn">購入手続きへ</a>
        </div>

        <div class="item-section">
            <h2 class="item-section-title">商品説明</h2>
            @if($item->color)
                <div class="item-description">カラー：{{ $item->color }}</div>
            @endif
            @if($item->condition)
                <div class="item-description">{{ $item->condition->name }}</div>
            @endif
            @if($item->description)
                <div class="item-description">{{ $item->description }}</div>
            @endif
        </div>

        <div class="item-section">
            <h2 class="item-section-title">商品の情報</h2>
            
            @if($item->categories && $item->categories->count() > 0)
                <div class="product-info-row">
                    <div class="info-label">カテゴリー</div>
                    <div class="info-value">
                        <div class="category-tags">
                            @foreach($item->categories as $category)
                                <span class="category-tag">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($item->condition)
                <div class="product-info-row">
                    <div class="info-label">商品の状態</div>
                    <div class="info-value">{{ $item->condition->name }}</div>
                </div>
            @endif
        </div>

        <div class="item-section">
            <h2 class="item-section-title">コメント({{ $item->comments->count() }})</h2>

            @if($item->comments->count() > 0)
                @foreach($item->comments as $comment)
                    <div class="comment-item">
                        <div class="comment-header">
                            <div class="comment-avatar"></div>
                            <div class="comment-user">{{ $comment->user->name }}</div>
                        </div>
                        <div class="comment-content">{{ $comment->content }}</div>
                    </div>
                @endforeach
            @endif

            @auth
                <div class="comment-form">
                    <div class="comment-form-title">商品へのコメント</div>
                    <form method="POST" action="{{ route('items.comment', $item->id) }}">
                        @csrf
                        <textarea
                            name="content"
                            class="comment-textarea"
                            placeholder="コメントを入力してください"
                        ></textarea>
                        @error('content')
                            <div class="error-message" style="color: red; margin-top: 4px; font-size: 14px;">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="comment-submit-btn">コメントを送信する</button>
                    </form>
                </div>
            @else
                <div class="login-prompt">
                    <a href="{{ route('login') }}">ログイン</a>してコメントを投稿
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection