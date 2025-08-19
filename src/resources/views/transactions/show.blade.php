@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transactions/show.css') }}">

<div class="chat-layout">
    {{-- ヘッダー --}}
    <div class="chat-header">
        <div class="logo">
            <img src="{{ asset('logo.svg') }}" alt="CT COACHTECH">
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
                <div class="complete-button">
                    <button class="complete-btn">取引を完了する</button>
                </div>
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
                        <div class="message-item {{ $message->user_id === auth()->id() ? 'message-own' : 'message-other' }}" data-message-id="{{ $message->id }}">
                            <div class="message-avatar"></div>
                            <div class="message-content">
                                <div class="message-user">{{ $message->user->name }}</div>
                                <div class="message-text">{{ $message->content }}</div>
                                @if($message->image_url)
                                    <div class="message-image">
                                        <img src="{{ asset('storage/' . $message->image_url) }}" alt="添付画像">
                                    </div>
                                @endif
                                @if($message->user_id === auth()->id())
                                    <div class="message-actions">
                                        <button class="edit-btn" onclick="editMessage({{ $message->id }})">編集</button>
                                        <button class="delete-btn" onclick="deleteMessage({{ $message->id }})">削除</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- メッセージ投稿フォーム --}}
            <div class="message-form-container">
                <form method="POST" action="{{ route('messages.store', $transaction->id) }}" enctype="multipart/form-data" class="message-form">
                    @csrf
                    <div class="form-group">
                        <textarea 
                            name="content" 
                            class="message-input" 
                            placeholder="取引メッセージを記入してください"
                            maxlength="400"
                            id="message-content"
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-actions">
                        <label for="image" class="image-label">
                            <span class="image-btn">画像を追加</span>
                            <input type="file" name="image" id="image" accept=".jpeg,.jpg,.png" style="display: none;">
                        </label>
                        <button type="submit" class="send-btn">
                            <i class="send-icon">📤</i>
                        </button>
                    </div>
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </form>
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
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

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

// 入力情報保持機能（セッションストレージ使用）
document.getElementById('message-content').addEventListener('input', function(e) {
    sessionStorage.setItem('messageContent', e.target.value);
});

// ページ読み込み時に保存された内容を復元
window.addEventListener('load', function() {
    const savedContent = sessionStorage.getItem('messageContent');
    if (savedContent) {
        document.getElementById('message-content').value = savedContent;
    }
});

// 送信成功時にセッションストレージをクリア
document.querySelector('.message-form').addEventListener('submit', function() {
    sessionStorage.removeItem('messageContent');
});
</script>
@endsection
