@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto 0;">
    <h2 style="text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 40px;">住所の変更</h2>
    <form action="{{ route('address.update') }}" method="POST">
        @csrf
        <div style="margin-bottom: 28px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode', $address->postcode) }}" style="width: 100%; padding: 8px; font-size: 16px;">
        </div>
        <div style="margin-bottom: 28px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">住所</label>
            <input type="text" name="address" value="{{ old('address', $address->address) }}" style="width: 100%; padding: 8px; font-size: 16px;">
        </div>
        <div style="margin-bottom: 40px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">建物名</label>
            <input type="text" name="building" value="{{ old('building', $address->building) }}" style="width: 100%; padding: 8px; font-size: 16px;">
        </div>
        <button type="submit" style="width: 100%; background: #ff6f6f; color: #fff; font-size: 18px; font-weight: bold; padding: 12px 0; border: none; border-radius: 4px;">更新する</button>
    </form>
</div>
@endsection 