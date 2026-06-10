<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','room_id','name','email','phone','gender',
        'address','id_card','photo','start_date','end_date','status','notes'
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function user()      { return $this->belongsTo(User::class); }
    public function room()      { return $this->belongsTo(Room::class); }
    public function invoices()  { return $this->hasMany(Invoice::class); }
    public function complaints(){ return $this->hasMany(Complaint::class); }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) return asset('storage/'.$this->photo);
        $bg = $this->gender === 'female' ? 'EC4899' : '2563EB';
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background='.$bg.'&color=fff&bold=true';
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Aktif' : 'Tidak Aktif';
    }

    public function unpaidInvoices()
    {
        return $this->invoices()->whereIn('status', ['unpaid','overdue']);
    }
}
