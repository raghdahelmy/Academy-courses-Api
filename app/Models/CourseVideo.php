<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVideo extends Model
{
     protected $fillable = [
        'course_id',
        'name',
        'video_path',
        'content',
        'progress',
        'overview',
        'learning_outcomes',
        'order',
        'is_free',
    ];

      public function course()
    {
        return $this->belongsTo(Course::class);
    }

    
}
