@extends('layouts.chat')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transactions/show.css') }}">

<div class="chat-layout">
    {{-- ヘッダー --}}
    <div class="chat-header">
        <div class="logo">
            <a href="{{ route('items.index') }}">
                <img src="{{ asset('logo.svg') }}" alt="CT COACHTECH">
            </a>
        </div>
    </div>

    <div class="chat-main">
        {{-- 左サイドバー --}}
        <div class="chat-sidebar">
            <div class="sidebar-title">その他の取引</div>
            <div class="transaction-list">
                @foreach($otherTransactions as $otherTransaction)
                    <a href="{{ route('transactions.show', $otherTransaction->id) }}" 
                       class="transaction-item {{ $otherTransaction->id === $transaction->id ? 'active' : '' }}">
                        {{ $otherTransaction->item->name }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- メインコンテンツ --}}
        <div class="chat-content">
            {{-- 取引ヘッダー --}}
            <div class="transaction-header">
                <div class="user-info">
                    <div class="user-avatar"></div>
                    <div class="user-name">{{ $otherUser->name }}さんとの取引画面</div>
                </div>
                @if(!$transaction->is_completed && $transaction->user_id === auth()->id())
                    <div class="complete-button">
                        <button class="complete-btn" onclick="showRatingModal()">取引を完了する</button>
                    </div>
                @endif
            </div>

            {{-- 商品詳細 --}}
            <div class="product-details">
                <div class="product-image">
                    <img src="{{ asset('storage/' . $transaction->item->image_url) }}" alt="商品画像">
                </div>
                <div class="product-info">
                    <div class="product-name">{{ $transaction->item->name }}</div>
                    <div class="product-price">¥{{ number_format($transaction->item->price) }}</div>
                </div>
            </div>

            {{-- メッセージエリア --}}
            <div class="message-area" id="message-area">
                @if($transaction->messages->isEmpty())
                    <div class="no-messages">まだメッセージはありません。</div>
                @else
                    @foreach($transaction->messages as $message)
                        @if($message->user_id === auth()->id())
                            {{-- 自分のメッセージ（右側） --}}
                            <div class="message-item message-own" data-message-id="{{ $message->id }}">
                                <div class="message-header">
                                    <div class="message-user">{{ $message->user->name }}</div>
                                    <div class="message-avatar"></div>
                                </div>
                                <div class="message-content">
                                    <div class="message-text">{{ $message->content }}</div>
                                    @if($message->image_url)
                                        <div class="message-image">
                                            <img src="{{ asset('storage/' . $message->image_url) }}" alt="添付画像">
                                        </div>
                                    @endif
                                    <div class="message-actions">
                                        <button class="edit-btn" onclick="editMessage({{ $message->id }})">編集</button>
                                        <button class="delete-btn" onclick="deleteMessage({{ $message->id }})">削除</button>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- 相手のメッセージ（左側） --}}
                            <div class="message-item message-other" data-message-id="{{ $message->id }}">
                                <div class="message-header">
                                    <div class="message-avatar"></div>
                                    <div class="message-user">{{ $message->user->name }}</div>
                                </div>
                                <div class="message-content">
                                    <div class="message-text">{{ $message->content }}</div>
                                    @if($message->image_url)
                                        <div class="message-image">
                                            <img src="{{ asset('storage/' . $message->image_url) }}" alt="添付画像">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- メッセージ投稿フォーム --}}
            <div class="message-form-container">
                <form method="POST" action="{{ route('messages.store', $transaction->id) }}" enctype="multipart/form-data" class="message-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <textarea 
                                name="content" 
                                class="message-input" 
                                placeholder="取引メッセージを記入してください"
                                id="message-content"
                            >{{ old('content') }}</textarea>
                            <div class="character-count">0/400</div>
                            @error('content')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-actions">
                            <label for="image" class="image-label">
                                <span class="image-btn">画像を追加</span>
                                <input type="file" name="image" id="image" accept=".jpeg,.jpg,.png" class="hidden-file-input">
                            </label>
                            <button type="submit" class="send-btn">
                                <svg class="send-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 評価モーダル --}}
<div id="rating-modal" class="rating-modal">
    <div class="rating-modal-content">
        <div class="rating-modal-header">
            <h3>取引が完了しました。</h3>
        </div>
        <div class="rating-modal-body">
            <p>今回の取引相手はどうでしたか?</p>
            <form id="rating-form" method="POST" action="{{ route('ratings.store', $transaction->id) }}">
                @csrf
                <div class="rating-stars">
                    <input type="radio" name="rating" value="5" id="star5">
                    <label for="star5">★</label>
                    <input type="radio" name="rating" value="4" id="star4">
                    <label for="star4">★</label>
                    <input type="radio" name="rating" value="3" id="star3">
                    <label for="star3">★</label>
                    <input type="radio" name="rating" value="2" id="star2">
                    <label for="star2">★</label>
                    <input type="radio" name="rating" value="1" id="star1">
                    <label for="star1">★</label>
                </div>
            </form>
        </div>
        <div class="rating-modal-footer">
            <div class="rating-actions">
                <button type="submit" form="rating-form" class="submit-btn">送信する</button>
            </div>
        </div>
    </div>
</div>

<script>
// 画像プレビュー機能
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'image-preview-img';
            preview.appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

// 評価モーダル表示
function showRatingModal() {
    document.getElementById('rating-modal').style.display = 'block';
}

// 評価モーダル非表示
function closeRatingModal() {
    document.getElementById('rating-modal').style.display = 'none';
}

// モーダル外クリックで閉じる
window.onclick = function(event) {
    const modal = document.getElementById('rating-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// メッセージ編集機能
function editMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageText = messageElement.querySelector('.message-text');
    const currentText = messageText.textContent;
    
    // 編集フォームを作成
    const editForm = document.createElement('div');
    editForm.className = 'edit-form';
    editForm.innerHTML = `
        <textarea class="edit-input" maxlength="400">${currentText}</textarea>
        <div class="edit-actions">
            <button class="save-btn" onclick="saveMessage(${messageId})">保存</button>
            <button class="cancel-btn" onclick="cancelEdit(${messageId})">キャンセル</button>
        </div>
    `;
    
    // 元のテキストを隠して編集フォームを表示
    messageText.style.display = 'none';
    messageElement.querySelector('.message-actions').style.display = 'none';
    messageElement.querySelector('.message-content').appendChild(editForm);
}

// メッセージ削除機能
function deleteMessage(messageId) {
    if (confirm('このメッセージを削除しますか？')) {
        // FormDataを使用してフォームデータを作成
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/messages/${messageId}`, {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                console.error('Response status:', response.status);
                response.text().then(text => {
                    console.error('Response text:', text);
                    alert('メッセージの削除に失敗しました');
                });
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('メッセージの削除に失敗しました');
        });
    }
}

// メッセージ保存機能
function saveMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const editInput = messageElement.querySelector('.edit-input');
    const newContent = editInput.value;
    
    // FormDataを使用してフォームデータを作成
    const formData = new FormData();
    formData.append('content', newContent);
    formData.append('_method', 'PUT');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch(`/messages/${messageId}`, {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            location.reload();
        } else {
            console.error('Response status:', response.status);
            response.text().then(text => {
                console.error('Response text:', text);
                alert('メッセージの更新に失敗しました');
            });
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('メッセージの更新に失敗しました');
    });
}

// 編集キャンセル機能
function cancelEdit(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageText = messageElement.querySelector('.message-text');
    const messageActions = messageElement.querySelector('.message-actions');
    const editForm = messageElement.querySelector('.edit-form');
    
    messageText.style.display = 'block';
    messageActions.style.display = 'flex';
    editForm.remove();
}

// ユーザーIDと取引IDを取得
const userId = {{ auth()->id() }};
const transactionId = {{ $transaction->id }};
const storageKey = `messageContent_${userId}_${transactionId}`;

// 文字数カウンター機能
document.getElementById('message-content').addEventListener('input', function(e) {
    const text = e.target.value;
    const charCount = text.length;
    const charCountElement = document.querySelector('.character-count');
    
    charCountElement.textContent = `${charCount}/400`;
    
    if (charCount > 400) {
        charCountElement.classList.add('over-limit');
    } else {
        charCountElement.classList.remove('over-limit');
    }
    
    // 入力情報保持機能（ユーザー・取引別に保存）
    sessionStorage.setItem(storageKey, text);
});

// ページ読み込み時に保存された内容を復元
window.addEventListener('load', function() {
    const savedContent = sessionStorage.getItem(storageKey);
    if (savedContent) {
        document.getElementById('message-content').value = savedContent;
        // 文字数カウンターも更新
        const charCount = savedContent.length;
        const charCountElement = document.querySelector('.character-count');
        charCountElement.textContent = `${charCount}/400`;
        if (charCount > 400) {
            charCountElement.classList.add('over-limit');
        }
    }
    
    @if($transaction->is_completed && !$transaction->isRatedByUser(auth()->id()))
        showRatingModal();
    @endif
});

// 送信成功時にセッションストレージをクリア
document.querySelector('.message-form').addEventListener('submit', function() {
    sessionStorage.removeItem(storageKey);
});
</script>
@endsection
