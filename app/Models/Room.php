<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['kost_id','number','floor','price','status','description','photos'];
    protected $casts    = ['photos' => 'array', 'price' => 'decimal:2'];

    public function kost()       { return $this->belongsTo(Kost::class); }
    public function facilities() { return $this->belongsToMany(Facility::class, 'room_facilities'); }
    public function tenants()    { return $this->hasMany(Tenant::class); }
    public function activeTenant(){ return $this->hasOne(Tenant::class)->where('status','active'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available'   => 'Tersedia',
            'occupied'    => 'Terisi',
            'maintenance' => 'Maintenance',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available'   => 'green',
            'occupied'    => 'blue',
            'maintenance' => 'yellow',
            default       => 'gray',
        };
    }

    public function getFirstPhotoAttribute(): ?string
    {
        if ($this->photos && count($this->photos) > 0) {
            return asset('storage/'.$this->photos[0]);
        }
        return null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}

// ─────────────────────────────────────────────────────────────────
