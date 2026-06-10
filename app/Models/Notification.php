<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id','type','title','message','data','url','read_at'];

    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isUnread(): bool { return is_null($this->read_at); }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'invoice_created'    => '📄',
            'payment_submitted'  => '💳',
            'payment_verified'   => '✅',
            'payment_rejected'   => '❌',
            'complaint_created'  => '📢',
            'complaint_replied'  => '💬',
            default              => '🔔',
        };
    }
}
