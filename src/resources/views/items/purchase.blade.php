@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1100px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; margin-top: 40px;">
        <!-- 左側：商品情報・支払い方法・配送先 -->
        <div style="width: 60%;">
            <div style="display: flex; align-items: center;">
                <div style="width: 120px; height: 120px; background: #eee; display: flex; align-items: center; justify-content: center; margin-right: 24px;">
                    商品画像
                </div>
                <div>
                    <div style="font-size: 20px; font-weight: bold;">{{ $item->name ?? '商品名' }}</div>
                    <div style="font-size: 18px; margin-top: 8px;">￥{{ number_format($item->price ?? 47000) }}</div>
                </div>
            </div>
            <hr style="margin: 32px 0;">
            <div style="margin-bottom: 24px;">
                <div style="font-weight: bold; margin-bottom: 8px;">支払い方法</div>
                <select style="width: 220px; padding: 6px 8px;">
                    <option>選択してください</option>
                    <option>クレジットカード</option>
                    <option>コンビニ払い</option>
                    <option>銀行振込</option>
                </select>
            </div>
            <hr style="margin: 32px 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-weight: bold; margin-bottom: 8px;">配送先</div>
                    <div style="margin-bottom: 4px;">〒 {{ $address->postcode ?? 'XXX-YYYY' }}</div>
                    <div>{{ $address->full_address ?? 'ここには住所と建物が入ります' }}</div>
                </div>
                <div>
                    <a href="{{ route('address.edit') }}" style="color: #3498db; font-size: 14px;">変更する</a>
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
                <div>{{ $payment_method ?? 'コンビニ払い' }}</div>
            </div>
            <form action="{{ route('items.purchase.store', $item->id) }}" method="POST">
                @csrf
                <button type="submit" style="width: 100%; background: #ff6f6f; color: #fff; font-size: 18px; font-weight: bold; padding: 12px 0; border: none; border-radius: 4px;">購入する</button>
            </form>
        </div>
    </div>
</div>
@endsection 