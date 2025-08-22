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
            <div class="mypage-user-info">
        <div class="mypage-username">{{ $user->name }}</div>
        <div class="rating-stars">
            @for($i = 1; $i <= 5; $i++)
                <span class="star {{ $i <= $averageRating ? 'star-filled' : 'star-empty' }}">★</span>
            @endfor
        </div>
    </div>
    <a href="{{ route('users.editProfile') }}" class="mypage-edit-btn">プロフィールを編集</a>
    </div>
    <div class="mypage-tab-row">
        <div id="tab-exhibit" class="mypage-tab mypage-tab-active">出品した商品</div>
        <div id="tab-purchase" class="mypage-tab">購入した商品</div>
        <div id="tab-transaction" class="mypage-tab">
            取引中の商品
            @if($transactionNotificationCount > 0)
                <span class="notification-badge">{{ $transactionNotificationCount }}</span>
            @endif
        </div>
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
    <div id="transaction-list" class="mypage-list hidden">
        @php
            $purchasedTransactions = \App\Models\Purchase::where('user_id', $user->id)
                ->where('is_completed', false)
                ->with(['item', 'item.user', 'messages' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->get()
                ->sortByDesc(function($transaction) {
                    return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
                });

            $soldTransactions = \App\Models\Item::where('user_id', $user->id)
                ->whereHas('purchases', function($query) use ($user) {
                    $query->where(function($q) use ($user) {
                        $q->where('is_completed', false)
                            ->orWhere(function($subQ) use ($user) {
                                $subQ->where('is_completed', true)
                                    ->whereDoesntHave('ratings', function($ratingQ) use ($user) {
                                        $ratingQ->where('user_id', $user->id);
                                    });
                            });
                    });
                })
                ->with(['purchases' => function($query) use ($user) {
                    $query->where(function($q) use ($user) {
                        $q->where('is_completed', false)
                            ->orWhere(function($subQ) use ($user) {
                                $subQ->where('is_completed', true)
                                    ->whereDoesntHave('ratings', function($ratingQ) use ($user) {
                                        $ratingQ->where('user_id', $user->id);
                                    });
                            });
                    })
                    ->with(['user', 'messages' => function($subQuery) {
                        $subQuery->orderBy('created_at', 'desc');
                    }, 'ratings']);
                }])
                ->get()
                ->flatMap(function($item) {
                    // 各商品の取引を新着メッセージ順にソート
                    $sortedPurchases = $item->purchases->sortByDesc(function($transaction) {
                        return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
                    });

                    // 各取引に商品情報を追加
                    return $sortedPurchases->map(function($purchase) use ($item) {
                        $purchase->item = $item;
                        return $purchase;
                    });
                })
                ->sortByDesc(function($transaction) {
                    return $transaction->messages->first() ? $transaction->messages->first()->created_at : $transaction->created_at;
                });
        @endphp

        @php
            $hasPurchasedTransactions = $purchasedTransactions->isNotEmpty();
            $hasSoldTransactions = $soldTransactions->isNotEmpty();
        @endphp

        @if(!$hasPurchasedTransactions && !$hasSoldTransactions)
            <div>取引中の商品はありません。</div>
        @else
            {{-- 購入した商品の取引 --}}
            @foreach($purchasedTransactions as $transaction)
                <a href="{{ route('transactions.show', $transaction->id) }}" class="mypage-item-link">
                    <div class="mypage-item-card">
                        @php
                            $unreadCount = $transaction->getUnreadMessageCount();
                        @endphp
                        @if($unreadCount > 0)
                            <div class="notification-badge">{{ $unreadCount }}</div>
                        @endif
                        @if($transaction->item->image_url)
                            <img src="{{ asset('storage/' . $transaction->item->image_url) }}" alt="商品画像" class="mypage-item-image">
                        @else
                            <div class="mypage-item-image-placeholder">商品画像</div>
                        @endif
                        <div class="mypage-item-name">{{ $transaction->item->name }}</div>
                    </div>
                </a>
            @endforeach

            {{-- 出品した商品の取引 --}}
            @foreach($soldTransactions as $transaction)
                <a href="{{ route('transactions.show', $transaction->id) }}" class="mypage-item-link">
                    <div class="mypage-item-card">
                        @php
                            $unreadCount = $transaction->getUnreadMessageCount();
                        @endphp
                        @if($unreadCount > 0)
                            <div class="notification-badge">{{ $unreadCount }}</div>
                        @endif
                        @if($transaction->item->image_url)
                            <img src="{{ asset('storage/' . $transaction->item->image_url) }}" alt="商品画像" class="mypage-item-image">
                        @else
                            <div class="mypage-item-image-placeholder">商品画像</div>
                        @endif
                        <div class="mypage-item-name">{{ $transaction->item->name }}</div>
                        @if($transaction->is_completed)
                            <div class="completed-badge">完了</div>
                        @endif
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabExhibit = document.getElementById('tab-exhibit');
    const tabPurchase = document.getElementById('tab-purchase');
    const exhibitList = document.getElementById('exhibit-list');
    const purchaseList = document.getElementById('purchase-list');

    const tabTransaction = document.getElementById('tab-transaction');
    const transactionList = document.getElementById('transaction-list');

    tabExhibit.addEventListener('click', function() {
        tabExhibit.classList.add('active');
        tabExhibit.style.color = '#ff6f6f';
        tabExhibit.style.borderBottom = '2px solid #ff6f6f';
        tabPurchase.classList.remove('active');
        tabPurchase.style.color = '#888';
        tabPurchase.style.borderBottom = 'none';
        tabTransaction.classList.remove('active');
        tabTransaction.style.color = '#888';
        tabTransaction.style.borderBottom = 'none';
        exhibitList.style.display = 'flex';
        purchaseList.style.display = 'none';
        if (transactionList) transactionList.style.display = 'none';
    });

    tabPurchase.addEventListener('click', function() {
        tabPurchase.classList.add('active');
        tabPurchase.style.color = '#ff6f6f';
        tabPurchase.style.borderBottom = '2px solid #ff6f6f';
        tabExhibit.classList.remove('active');
        tabExhibit.style.color = '#888';
        tabExhibit.style.borderBottom = 'none';
        tabTransaction.classList.remove('active');
        tabTransaction.style.color = '#888';
        tabTransaction.style.borderBottom = 'none';
        exhibitList.style.display = 'none';
        purchaseList.style.display = 'flex';
        if (transactionList) transactionList.style.display = 'none';
    });

    tabTransaction.addEventListener('click', function() {
        tabTransaction.classList.add('active');
        tabTransaction.style.color = '#ff6f6f';
        tabTransaction.style.borderBottom = '2px solid #ff6f6f';
        tabExhibit.classList.remove('active');
        tabExhibit.style.color = '#888';
        tabExhibit.style.borderBottom = 'none';
        tabPurchase.classList.remove('active');
        tabPurchase.style.color = '#888';
        tabPurchase.style.borderBottom = 'none';
        exhibitList.style.display = 'none';
        purchaseList.style.display = 'none';
        if (transactionList) transactionList.style.display = 'flex';
    });
});
</script>
@endsection