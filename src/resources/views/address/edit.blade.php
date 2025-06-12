@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/address/edit.css') }}">
<div class="address-edit-container">
    <h2 class="address-edit-title">住所の変更</h2>
    <form action="{{ route('address.update', $item->id) }}" method="POST">
        @csrf
        <div class="address-edit-form-group">
            <label class="address-edit-label">郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode', $address->postcode) }}" class="address-edit-input">
            @error('postcode')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="address-edit-form-group">
            <label class="address-edit-label">住所</label>
            <input type="text" name="address" value="{{ old('address', $address->address) }}" class="address-edit-input">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="address-edit-form-group">
            <label class="address-edit-label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $address->building ?? '') }}" class="address-edit-input">
            @error('building')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="address-edit-btn">更新する</button>
    </form>
</div>
@endsection 