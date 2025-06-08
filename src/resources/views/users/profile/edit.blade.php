@extends('layouts.app')

@section('content')
<div style="background: #fff; min-height: 100vh;">
    <div style="max-width: 400px; margin: 40px auto; padding: 32px 24px; border-radius: 8px;">
        <h2 style="text-align: center; font-weight: bold; font-size: 1.5rem; margin-bottom: 32px;">プロフィール設定</h2>
        <form method="POST" action="{{ route('users.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- プロフィール画像 -->
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 24px;">
                @if($user->profile_image)
                    <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="プロフィール画像" style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; background: #eee;">
                @else
                    <div style="width: 96px; height: 96px; border-radius: 50%; background: #eee;"></div>
                @endif
                <label style="margin-top: 8px;">
                    <input type="file" name="profile_image" style="display: none;">
                    <span style="color: #FF6F6F; border: 1px solid #FF6F6F; border-radius: 4px; padding: 4px 12px; font-size: 0.9rem; cursor: pointer;">画像を選択する</span>
                </label>
            </div>

            <!-- ユーザー名 -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: bold; font-size: 0.95rem;">ユーザー名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 8px; margin-top: 4px;">
                @error('name')
                    <p style="color: #FF6F6F; font-size: 0.85rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- 郵便番号 -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: bold; font-size: 0.95rem;">郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 8px; margin-top: 4px;">
                @error('postal_code')
                    <p style="color: #FF6F6F; font-size: 0.85rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- 住所 -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: bold; font-size: 0.95rem;">住所</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 8px; margin-top: 4px;">
                @error('address')
                    <p style="color: #FF6F6F; font-size: 0.85rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- 建物名 -->
            <div style="margin-bottom: 32px;">
                <label style="font-weight: bold; font-size: 0.95rem;">建物名</label>
                <input type="text" name="building" value="{{ old('building', optional($user->addresses->first())->building) }}" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 8px; margin-top: 4px;">
                @error('building')
                    <p style="color: #FF6F6F; font-size: 0.85rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="text-align: center;">
                <button type="submit" style="background: #FF6F6F; color: #fff; font-weight: bold; border: none; border-radius: 6px; width: 100%; padding: 12px 0; font-size: 1rem;">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 