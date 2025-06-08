@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">

<div class="item-create-container">
    <h2 class="item-create-title">商品の出品</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="item-create-image-block">
            <div class="item-create-image-label">商品画像</div>
            <div class="item-create-image-drop">
                <label class="item-create-image-btn">
                    画像を選択する
                    <input type="file" name="image" class="item-create-image-input" accept="image/*" onchange="previewImage(this)">
                </label>
                <div id="image-preview" class="image-preview"></div>
            </div>
            @error('image')<div class="item-create-error">{{ $message }}</div>@enderror
        </div>
        <div class="item-create-detail-block">
            <div class="item-create-detail-title">商品の詳細</div>
            <hr class="item-create-hr">
            <div class="item-create-form-group">
                <div class="item-create-label">カテゴリー</div>
                <div class="item-create-category-list">
                    @foreach($categories as $category)
                        <label class="item-create-category-tag {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="item-create-category-checkbox" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
                @error('categories')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
            <div class="item-create-form-group">
                <div class="item-create-label">商品の状態</div>
                <select name="condition_id" class="item-create-select">
                    <option value="">選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                    @endforeach
                </select>
                @error('condition_id')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="item-create-detail-block">
            <div class="item-create-detail-title">商品名と説明</div>
            <hr class="item-create-hr">
            <div class="item-create-form-group">
                <div class="item-create-label">商品名</div>
                <input type="text" name="name" value="{{ old('name') }}" class="item-create-input">
                @error('name')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
            <div class="item-create-form-group">
                <div class="item-create-label">ブランド名</div>
                <input type="text" name="brand_name" value="{{ old('brand_name') }}" class="item-create-input">
                @error('brand_name')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
            <div class="item-create-form-group">
                <div class="item-create-label">商品の説明</div>
                <textarea name="description" class="item-create-textarea">{{ old('description') }}</textarea>
                @error('description')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
            <div class="item-create-form-group">
                <div class="item-create-label">販売価格</div>
                <input type="number" name="price" value="{{ old('price') }}" class="item-create-input" min="1">
                @error('price')<div class="item-create-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="item-create-btn">出品する</button>
    </form>
</div>

<style>
.item-create-category-list .item-create-category-tag {
    display: inline-block;
    padding: 8px 16px;
    margin: 4px;
    border: 1px solid #ddd;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: white;
    color: #333;
}
.item-create-category-list .item-create-category-tag:hover {
    background-color: #f0f0f0;
}
.item-create-category-list .item-create-category-tag.selected {
    background-color: #ff6b6b !important;
    color: white !important;
    border-color: #ff6b6b !important;
}
.item-create-category-checkbox {
    display: none;
}
</style>
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const categoryTags = document.querySelectorAll('.item-create-category-tag');
    categoryTags.forEach(tag => {
        // 初期状態でチェックされているものにselectedクラスを追加
        if (tag.querySelector('input[type="checkbox"]').checked) {
            tag.classList.add('selected');
        }
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected');
        });
    });
});
</script>
@endsection 