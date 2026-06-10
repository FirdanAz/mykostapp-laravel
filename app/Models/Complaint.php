<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'tenant_id','handled_by','title','description',
        'category','status','priority','photos','resolved_at'
    ];

    protected $casts = ['photos' => 'array', 'resolved_at' => 'datetime'];

    public function tenant()    { return $this->belongsTo(Tenant::class); }
    public function handler()   { return $this->belongsTo(User::class, 'handled_by'); }
    public function replies()   { return $this->hasMany(ComplaintReply::class); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new'         => 'Baru',
            'in_progress' => 'Diproses',
            'resolved'    => 'Selesai',
            'rejected'    => 'Ditolak',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new'         => 'blue',
            'in_progress' => 'yellow',
            'resolved'    => 'green',
            'rejected'    => 'red',
            default       => 'gray',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'Rendah',
            'medium' => 'Sedang',
            'high'   => 'Tinggi',
            default  => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'green',
            'medium' => 'yellow',
            'high'   => 'red',
            default  => 'gray',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'facility'   => 'Fasilitas',
            'security'   => 'Keamanan',
            'cleanliness'=> 'Kebersihan',
            'noise'      => 'Kebisingan',
            'other'      => 'Lainnya',
            default      => $this->category,
        };
    }
}
