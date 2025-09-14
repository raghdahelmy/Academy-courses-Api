<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'video_path',
        'is_free',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'user_video')->withTimestamps();
    }
}