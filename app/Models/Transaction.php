<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_hash',
        'sender_id',
        'receiver_id',
        'external_wallet_address',
        'type',
        'amount',
        'currency',
        'note',
        'status',
    ];

    /**
     * Get the user who initiated the transaction.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the transaction (for internal transfers).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
