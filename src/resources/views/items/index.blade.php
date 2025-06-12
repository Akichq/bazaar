@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
<div class="tab-menu">
    <a href="{{ route('items.index') }}" class="tab {{ !request('page') || request('page') === 'recommend' ? 'active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['page' => 'mylist']) }}" class="tab {{ request('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

@if($items->isEmpty())
    <div class="no-items-message">該当する商品がありません。</div>
@else
<div class="item-list-grid">
    @foreach($items as $item)
        <a href="{{ route('items.show', $item->id) }}" class="item-card">
            <div class="item-image-wrap">
                @if($item->image_url)
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" class="item-image">
                @else
                    <div class="item-image no-image">商品画像</div>
                @endif
                @if($item->purchases()->exists())
                    <div class="sold-label">SOLD</div>
                @endif
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
    @endforeach
</div>
@endif
@endsection 