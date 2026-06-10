<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','address','phone','email','website','photos','logo'];

    protected $casts = ['photos' => 'array'];

    public function rooms() { return $this->hasMany(Room::class); }

    public function getFirstPhotoAttribute(): ?string
    {
        if ($this->photos && count($this->photos) > 0) {
            return asset('storage/'.$this->photos[0]);
        }
        return null;
    }
}
