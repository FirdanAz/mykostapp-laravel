<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'address', 'city',
        'phone', 'email', 'website', 'type', 'photos', 'logo', 'is_published',
    ];

    protected $casts = [
        'photos'       => 'array',
        'is_published' => 'boolean',
    ];

    public function owner()  { return $this->belongsTo(User::class, 'user_id'); }
    public function rooms()  { return $this->hasMany(Room::class); }

    public function getFirstPhotoAttribute(): ?string
    {
        if ($this->photos && count($this->photos) > 0) {
            return asset('storage/' . $this->photos[0]);
        }
        return null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) return asset('storage/' . $this->logo);
        return null;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'putra'  => 'Kos Putra',
            'putri'  => 'Kos Putri',
            'campur' => 'Kos Campur',
            default  => $this->type,
        };
    }

    public function getAvailableRoomsCountAttribute(): int
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    public function getMinPriceAttribute(): ?float
    {
        return $this->rooms()->min('price');
    }
}
