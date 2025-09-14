<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
        use HasRoles , HasApiTokens;

        public function isTrainer(){
            return $this->role ==='trainer';
        }
        public function isAdmin(){
            return $this->role ==='admin';
        }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $fillable = [
        'name', 'email', 'password', 'avatar', 'role',
    ];


public function course(){

return $this->hasMany(Course::class);

}

public function ratings()
{
    return $this->hasMany(Rating::class, 'trainer_id');
}

public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}

public function activeSubscription()
{
    return $this->hasOne(Subscription::class)
                ->where('status', 'active')
                ->where('ends_at', '>=', now());
}


public function progresses()
{
    return $this->hasMany(Progress::class);
}


public function watchedVideos()
{
    return $this->belongsToMany(Video::class, 'user_video')->withTimestamps();
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
