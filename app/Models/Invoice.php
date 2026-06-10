<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','invoice_number','amount','due_date',
        'period_start','period_end','status','notes'
    ];

    protected $casts = [
        'due_date'     => 'date',
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount'       => 'decimal:2',
    ];

    public function tenant()   { return $this->belongsTo(Tenant::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function latestPayment() { return $this->hasOne(Payment::class)->latestOfMany(); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'unpaid'               => 'Belum Dibayar',
            'pending_verification' => 'Menunggu Verifikasi',
            'paid'                 => 'Lunas',
            'rejected'             => 'Ditolak',
            'overdue'              => 'Terlambat',
            default                => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'unpaid'               => 'yellow',
            'pending_verification' => 'blue',
            'paid'                 => 'green',
            'rejected'             => 'red',
            'overdue'              => 'orange',
            default                => 'gray',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public static function generateNumber(): string
    {
        $prefix = 'INV-' . date('Ymd');
        $last   = static::where('invoice_number','like',$prefix.'%')->latest()->first();
        $seq    = $last ? (int) substr($last->invoice_number, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
