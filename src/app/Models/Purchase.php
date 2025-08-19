<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
        'stripe_payment_id',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * メッセージとのリレーション
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'transaction_id');
    }

    /**
     * 新着メッセージ数を取得
     */
    public function getUnreadMessageCount()
    {
        return $this->messages()->where('is_read', false)->count();
    }

    /**
     * 取引状態判定
     */
    public function isInTransaction()
    {
        return !$this->is_completed && $this->created_at > now()->subDays(30);
    }
}
