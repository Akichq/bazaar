@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transactions/index.css') }}">

<div class="transaction-container">
    <h2 class="transaction-title">取引中の商品</h2>
    
    @if($purchasedTransactions->isEmpty() && $soldTransactions->isEmpty())
        <div class="no-transactions">取引中の商品はありません。</div>
    @else
        <div class="transaction-list">
            {{-- 購入した商品の取引 --}}
            @foreach($purchasedTransactions as $transaction)
                <div class="transaction-item-card">
                    @php
                        $unreadCount = $transaction->getUnreadMessageCount();
                    @endphp
                    @if($unreadCount > 0)
                        <div class="notification-badge">{{ $unreadCount }}</div>
                    @endif
                    
                    <a href="{{ route('transactions.show', $transaction->id) }}" class="transaction-link">
                        <div class="transaction-image-wrap">
                            @if($transaction->item->image_url)
                                <img src="{{ asset('storage/' . $transaction->item->image_url) }}" alt="商品画像" class="transaction-image">
                            @else
                                <div class="transaction-image-placeholder">商品画像</div>
                            @endif
                        </div>
                        <div class="transaction-info">
                            <div class="transaction-item-name">{{ $transaction->item->name }}</div>
                            <div class="transaction-price">¥{{ number_format($transaction->item->price) }}</div>
                            <div class="transaction-status">購入した商品</div>
                        </div>
                    </a>
                </div>
            @endforeach
            
            {{-- 出品した商品の取引 --}}
            @foreach($soldTransactions as $item)
                @foreach($item->purchases as $transaction)
                    @if(!$transaction->is_completed)
                        <div class="transaction-item-card">
                            @php
                                $unreadCount = $transaction->getUnreadMessageCount();
                            @endphp
                            @if($unreadCount > 0)
                                <div class="notification-badge">{{ $unreadCount }}</div>
                            @endif
                            
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="transaction-link">
                                <div class="transaction-image-wrap">
                                    @if($item->image_url)
                                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" class="transaction-image">
                                    @else
                                        <div class="transaction-image-placeholder">商品画像</div>
                                    @endif
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-item-name">{{ $item->name }}</div>
                                    <div class="transaction-price">¥{{ number_format($item->price) }}</div>
                                    <div class="transaction-status">出品した商品</div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif
</div>
@endsection
