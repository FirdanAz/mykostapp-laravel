<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'name',
        'email',
        'phone',
        'gender',
        'address',
        'id_card',
        'id_card_photo',
        'start_date',
        'duration_months',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Tidak Diketahui',
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }

    public function getIdCardPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->id_card_photo);
    }
}
