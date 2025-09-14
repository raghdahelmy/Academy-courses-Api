<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
        protected $fillable = [
        'name',
        'image',
        'type',
        'category_id',
        'user_id',
        'rating',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
{
    return $this->hasMany(Rating::class);
}


    public function CourseVideo()
    {
        return $this->hasMany(CourseVideo::class);
    }

public function subscriptions()
 {
    return $this->hasMany(Subscription::class);
}

public function progresses()
{
    return $this->hasMany(Progress::class);
}

}
