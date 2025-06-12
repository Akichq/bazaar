@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/users/mypage.css') }}">

<div class="mypage-container">
    <div class="mypage-profile-row">
        <div class="mypage-profile-image-wrap">
            @if($user->profile_image)
                <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" class="mypage-profile-image">
            @else
                <span class="mypage-profile-placeholder">NO IMAGE</span>
            @endif
        </div>
        <div class="mypage-username">{{ $user->name }}</div>
        <a href="{{ route('users.editProfile') }}" class="mypage-edit-btn">プロフィールを編集</a>
    </div>
    <div class="mypage-tab-row">
        <div id="tab-exhibit" class="mypage-tab mypage-tab-active">出品した商品</div>
        <div id="tab-purchase" class="mypage-tab">購入した商品</div>
    </div>
    <hr class="mypage-hr">
    <div id="exhibit-list" class="mypage-list">
        @forelse($exhibited_items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="mypage-item-link">
                <div class="mypage-item-card">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" class="mypage-item-image">
                    @else
                        <div class="mypage-item-image-placeholder">商品画像</div>
                    @endif
                    @if($item->is_sold)
                        <div class="mypage-sold-badge">SOLD</div>
                    @endif
                    <div class="mypage-item-name">{{ $item->name }}</div>
                </div>
            </a>
        @empty
            <div>出品した商品はありません。</div>
        @endforelse
    </div>
    <div id="purchase-list" class="mypage-list hidden">
        @forelse($purchased_items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="mypage-item-link">
                <div class="mypage-item-card">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" class="mypage-item-image">
                    @else
                        <div class="mypage-item-image-placeholder">商品画像</div>
                    @endif
                    @if($item->is_sold)
                        <div class="mypage-sold-badge">SOLD</div>
                    @endif
                    <div class="mypage-item-name">{{ $item->name }}</div>
                </div>
            </a>
        @empty
            <div>購入した商品はありません。</div>
        @endforelse
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabExhibit = document.getElementById('tab-exhibit');
    const tabPurchase = document.getElementById('tab-purchase');
    const exhibitList = document.getElementById('exhibit-list');
    const purchaseList = document.getElementById('purchase-list');

    tabExhibit.addEventListener('click', function() {
        tabExhibit.classList.add('active');
        tabExhibit.style.color = '#ff6f6f';
        tabExhibit.style.borderBottom = '2px solid #ff6f6f';
        tabPurchase.classList.remove('active');
        tabPurchase.style.color = '#888';
        tabPurchase.style.borderBottom = 'none';
        exhibitList.style.display = 'flex';
        purchaseList.style.display = 'none';
    });
    tabPurchase.addEventListener('click', function() {
        tabPurchase.classList.add('active');
        tabPurchase.style.color = '#ff6f6f';
        tabPurchase.style.borderBottom = '2px solid #ff6f6f';
        tabExhibit.classList.remove('active');
        tabExhibit.style.color = '#888';
        tabExhibit.style.borderBottom = 'none';
        exhibitList.style.display = 'none';
        purchaseList.style.display = 'flex';
    });
});
</script>
@endsection 