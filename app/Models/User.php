<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','avatar','phone'];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTenant(): bool { return $this->role === 'tenant'; }

    public function tenant() { return $this->hasOne(Tenant::class); }

    public function appNotifications() { return $this->hasMany(Notification::class); }

    public function unreadNotifications() { return $this->appNotifications()->whereNull('read_at'); }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) return asset('storage/'.$this->avatar);
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=2563EB&color=fff&bold=true';
    }
}
