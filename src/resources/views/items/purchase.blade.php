@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1100px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; margin-top: 40px;">
        <!-- 左側：商品情報・支払い方法・配送先 -->
        <div style="width: 60%;">
            <div style="display: flex; align-items: center;">
                <div style="width: 120px; height: 120px; background: #eee; display: flex; align-items: center; justify-content: center; margin-right: 24px;">
                    @if($item->image_url)
                        <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                    @else
                        商品画像
                    @endif
                </div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;">{{ $item->name ?? '商品名' }}</div>
                    <div style="font-size: 18px; margin-top: 8px;">￥{{ number_format($item->price ?? 47000) }}</div>
                </div>
            </div>
            <hr style="margin: 32px 0;">
            <div style="margin-bottom: 24px;">
                <div style="font-weight: bold; margin-bottom: 8px;">支払い方法</div>
                <select name="payment_method" id="payment-method" style="width: 220px; padding: 6px 8px;" required>
                    <option value="コンビニ払い" selected>コンビニ払い</option>
                    <option value="クレジットカード">クレジットカード</option>
                </select>
                @error('payment_method')
                    <p style="color: #FF6F6F; font-size: 0.85rem;">{{ $message }}</p>
                @enderror
            </div>
            <hr style="margin: 32px 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-weight: bold; margin-bottom: 8px;">配送先</div>
                    <div style="margin-bottom: 4px;">〒 {{ $addressData['postal_code'] ?? 'XXX-YYYY' }}</div>
                    <div>{{ $addressData['address'] ?? 'ここには住所と建物が入ります' }}{{ !empty($addressData['building']) ? ' ' . $addressData['building'] : '' }}</div>
                </div>
                <div>
                    <a href="{{ route('address.edit', $item->id) }}" style="color: #3498db; font-size: 14px;">変更する</a>
                </div>
            </div>
        </div>
        <!-- 右側：購入サマリー -->
        <div style="width: 35%; border: 1px solid #ccc; border-radius: 4px; padding: 24px; height: fit-content;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>商品代金</div>
                <div style="font-size: 18px; font-weight: bold;">￥{{ number_format($item->price ?? 47000) }}</div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-top: 1px solid #ccc; padding-top: 16px;">
                <div>支払い方法</div>
                <div id="selected-payment-method">コンビニ払い</div>
            </div>
            <form id="payment-form" action="{{ route('items.purchase.store', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-input" value="コンビニ払い">
                <button type="submit" id="submit-button" style="width: 100%; background: #ff6f6f; color: #fff; font-size: 18px; font-weight: bold; padding: 12px 0; border: none; border-radius: 4px;">購入する</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment-method');
    const selectedPaymentMethod = document.getElementById('selected-payment-method');
    const paymentMethodInput = document.getElementById('payment-method-input');

    // 支払い方法の変更を監視
    paymentMethod.addEventListener('change', function() {
        const selectedValue = this.value;
        selectedPaymentMethod.textContent = selectedValue;
        paymentMethodInput.value = selectedValue;
    });
});
</script>
@endsection 