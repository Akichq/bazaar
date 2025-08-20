<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rated_user_id',
        'transaction_id',
        'rating',
        'comment'
    ];

    /**
     * 評価を投稿したユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 評価されたユーザーとのリレーション
     */
    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    /**
     * 取引とのリレーション
     */
    public function transaction()
    {
        return $this->belongsTo(Purchase::class, 'transaction_id');
    }
}

