@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">

<div class="container">
    <div class="purchase-content">
        <div class="purchase-left">
            <div class="item-preview">
                <div class="item-image-container">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像">
                    @else
                        商品画像
                    @endif
                </div>
                <div>
                    <div class="item-name">{{ $item->name ?? '商品名' }}</div>
                    <div class="item-price">￥{{ number_format($item->price ?? 47000) }}</div>
                </div>
            </div>
            <hr class="divider">
            <div class="payment-section">
                <div class="section-title">支払い方法</div>
                <select name="payment_method" id="payment-method" class="payment-select" required>
                    <option value="コンビニ払い" selected>コンビニ払い</option>
                    <option value="クレジットカード">クレジットカード</option>
                </select>
                @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <hr class="divider">
            <div class="address-section">
                <div>
                    <div class="section-title">配送先</div>
                    <div>〒 {{ $addressData['postal_code'] ?? 'XXX-YYYY' }}</div>
                    <div>{{ $addressData['address'] ?? 'ここには住所と建物が入ります' }}{{ !empty($addressData['building']) ? ' ' . $addressData['building'] : '' }}</div>
                </div>
                <div>
                    <a href="{{ route('address.edit', $item->id) }}" class="address-link">変更する</a>
                </div>
            </div>
        </div>
        <div class="purchase-summary">
            <div class="summary-row">
                <div>商品代金</div>
                <div class="summary-price">￥{{ number_format($item->price ?? 47000) }}</div>
            </div>
            <div class="summary-row total">
                <div>支払い方法</div>
                <div id="selected-payment-method">コンビニ払い</div>
            </div>
            <form id="payment-form" action="{{ route('items.purchase.store', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-input" value="コンビニ払い">
                <button type="submit" id="submit-button" class="purchase-button">購入する</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment-method');
    const selectedPaymentMethod = document.getElementById('selected-payment-method');
    const paymentMethodInput = document.getElementById('payment-method-input');

    paymentMethod.addEventListener('change', function() {
        const selectedValue = this.value;
        selectedPaymentMethod.textContent = selectedValue;
        paymentMethodInput.value = selectedValue;
    });
});
</script>
@endsection