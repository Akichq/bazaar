@extends('layouts.app')

@section('content')
<div class="tab-menu">
    <a href="{{ route('items.index') }}" class="tab {{ !request('page') || request('page') === 'recommend' ? 'active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['page' => 'mylist']) }}" class="tab {{ request('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

@if($items->isEmpty())
    <div style="text-align:center; margin: 40px 0; color: #888;">該当する商品がありません。</div>
@else
<div class="item-list">
    @foreach($items as $item)
        <a href="{{ route('items.show', $item->id) }}" style="text-decoration: none; color: inherit;">
            <div style="position: relative;">
                @if($item->image_url)
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                @else
                    <div style="width: 180px; height: 180px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 8px;">商品画像</div>
                @endif
                @if($item->purchases()->exists())
                    <div style="position: absolute; top: 0; left: 0; background: rgba(255, 0, 0, 0.8); color: white; padding: 4px 8px; border-radius: 8px 0 8px 0; font-weight: bold;">SOLD</div>
                @endif
                <div style="text-align: center; font-size: 16px;">{{ $item->name }}</div>
            </div>
        </a>
    @endforeach
</div>
@endif
@endsection 