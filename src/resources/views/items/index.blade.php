@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
<div class="tab-menu">
    <span class="tab active">おすすめ</span>
    <span class="tab">マイリスト</span>
</div>
<div class="item-list">
    @foreach($items as $item)
        <div class="item-card">
            <div class="item-image-wrap">
                <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </div>
    @endforeach
</div>
@endsection 