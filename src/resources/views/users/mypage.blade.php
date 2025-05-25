@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 40px auto 0;">
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-right: 40px;">
            @if($user->profile_image)
                <img src="{{ $user->profile_image }}" alt="プロフィール画像" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                <span>●</span>
            @endif
        </div>
        <div style="font-size: 28px; font-weight: bold;">{{ $user->name }}</div>
        <a href="#" style="margin-left: 40px; border: 2px solid #ff6f6f; color: #ff6f6f; background: #fff; font-weight: bold; padding: 8px 24px; border-radius: 6px; font-size: 16px; text-decoration: none;">プロフィールを編集</a>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 16px;">
        <div style="font-weight: bold; color: #ff6f6f; border-bottom: 2px solid #ff6f6f; padding-bottom: 4px; margin-right: 32px; cursor: pointer;">出品した商品</div>
        <div style="color: #888; font-weight: bold; padding-bottom: 4px; cursor: pointer;">購入した商品</div>
    </div>
    <hr style="margin-bottom: 32px;">
    <div style="display: flex; flex-wrap: wrap; gap: 32px;">
        @foreach($exhibited_items as $item)
            <div style="width: 180px;">
                <div style="width: 180px; height: 180px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 8px;">商品画像</div>
                <div style="text-align: center; font-size: 16px;">{{ $item->name }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection 