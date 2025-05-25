@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
<div class="item-detail-container">
    <div class="item-detail-left">
        <div class="item-detail-image">
            <img src="{{ $item->image_url }}" alt="商品画像">
        </div>
    </div>
    <div class="item-detail-right">
        <div class="item-title">{{ $item->name }}</div>
        <div class="item-brand">{{ $item->brand_name ?? '' }}</div>
        <div class="item-price">¥{{ number_format($item->price) }} <span class="tax">(税込)</span></div>
        <a href="{{ route('items.purchase', $item->id) }}" class="buy-btn">購入手続きへ</a>
        <div class="item-section">
            <div class="item-section-title">商品説明</div>
            <div class="item-description">{{ $item->description }}</div>
        </div>
        <div class="item-section">
            <div class="item-section-title">商品の情報</div>
            <div>カテゴリー：
                @foreach($item->categories as $category)
                    <span class="category-tag">{{ $category->name }}</span>
                @endforeach
            </div>
            <div>商品の状態：{{ $item->condition->name }}</div>
        </div>
        <div class="item-section">
            <div class="item-section-title">コメント</div>
            @foreach($item->comments as $comment)
                <div class="comment">
                    <span class="comment-user">{{ $comment->user->name }}</span>
                    <span class="comment-content">{{ $comment->content }}</span>
                </div>
            @endforeach
            <form method="POST" action="{{ route('items.comment', $item->id) }}">
                @csrf
                <textarea name="content" class="comment-box"></textarea>
                <button type="submit" class="comment-btn">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection 