<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id','proof_file','amount','status',
        'rejection_reason','verified_by','verified_at'
    ];

    protected $casts = ['verified_at' => 'datetime', 'amount' => 'decimal:2'];

    public function invoice()    { return $this->belongsTo(Invoice::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'Menunggu',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }

    public function getProofUrlAttribute(): string
    {
        return asset('storage/'.$this->proof_file);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
