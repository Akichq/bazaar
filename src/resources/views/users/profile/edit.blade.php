@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/users/profile/edit.css') }}">

<div class="profile-edit-container">
    <div class="profile-edit-form">
        <h2 class="profile-edit-title">プロフィール設定</h2>
        <form method="POST" action="{{ route('users.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-image-section">
                @if($user->profile_image)
                    <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image">
                @else
                    <div class="profile-image-placeholder"></div>
                @endif
                <label class="image-upload-label">
                    <input type="file" name="profile_image" class="hidden-file-input">
                    <span class="image-upload-btn">画像を選択する</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">ユーザー名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" class="form-input">
                @error('postal_code')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">住所</label>
                <input type="text" name="address" value="{{ old('address', $address->address ?? '') }}" class="form-input">
                @error('address')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">建物名</label>
                <input type="text" name="building" value="{{ old('building', $address->building ?? '') }}" class="form-input">
                @error('building')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-submit">
                <button type="submit" class="submit-btn">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection