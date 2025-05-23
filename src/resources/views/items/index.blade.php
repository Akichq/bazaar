@extends('layouts.app')

@section('content')
<div class="tab-menu">
    <span class="tab active">おすすめ</span>
    <span class="tab">マイリスト</span>
</div>
<div class="item-list">
    @foreach($items as $item)
        <div class="item-card">
            <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
            <div class="item-name">{{ $item->name }}</div>
        </div>
    @endforeach
</div>
@endsection 