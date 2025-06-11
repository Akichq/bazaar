@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 40px auto 0;">
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-right: 40px;">
            @if($user->profile_image)
                <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                <span>●</span>
            @endif
        </div>
        <div style="font-size: 28px; font-weight: bold;">{{ $user->name }}</div>
        <a href="{{ route('users.editProfile') }}" style="margin-left: 40px; border: 2px solid #ff6f6f; color: #ff6f6f; background: #fff; font-weight: bold; padding: 8px 24px; border-radius: 6px; font-size: 16px; text-decoration: none;">プロフィールを編集</a>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 16px;">
        <div id="tab-exhibit" class="mypage-tab active" style="font-weight: bold; color: #ff6f6f; border-bottom: 2px solid #ff6f6f; padding-bottom: 4px; margin-right: 32px; cursor: pointer;">出品した商品</div>
        <div id="tab-purchase" class="mypage-tab" style="color: #888; font-weight: bold; padding-bottom: 4px; cursor: pointer;">購入した商品</div>
    </div>
    <hr style="margin-bottom: 32px;">
    <div id="exhibit-list" class="mypage-list" style="display: flex; flex-wrap: wrap; gap: 32px;">
        @forelse($exhibited_items as $item)
            <a href="{{ route('items.show', $item->id) }}" style="text-decoration: none; color: inherit;">
                <div style="width: 180px; position: relative;">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                    @else
                        <div style="width: 180px; height: 180px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 8px;">商品画像</div>
                    @endif
                    @if($item->is_sold)
                        <div style="position: absolute; top: 0; left: 0; background: rgba(255, 0, 0, 0.8); color: white; padding: 4px 8px; border-radius: 8px 0 8px 0; font-weight: bold;">SOLD</div>
                    @endif
                    <div style="text-align: center; font-size: 16px;">{{ $item->name }}</div>
                </div>
            </a>
        @empty
            <div>出品した商品はありません。</div>
        @endforelse
    </div>
    <div id="purchase-list" class="mypage-list" style="display: none; flex-wrap: wrap; gap: 32px;">
        @forelse($purchased_items as $item)
            <a href="{{ route('items.show', $item->id) }}" style="text-decoration: none; color: inherit;">
                <div style="width: 180px; position: relative;">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                    @else
                        <div style="width: 180px; height: 180px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 8px;">商品画像</div>
                    @endif
                    @if($item->is_sold)
                        <div style="position: absolute; top: 0; left: 0; background: rgba(255, 0, 0, 0.8); color: white; padding: 4px 8px; border-radius: 8px 0 8px 0; font-weight: bold;">SOLD</div>
                    @endif
                    <div style="text-align: center; font-size: 16px;">{{ $item->name }}</div>
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