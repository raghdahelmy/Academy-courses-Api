<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function markAsWatched($courseId, $videoId)
    {
        $user = Auth::user();
        $course = Course::with('videos')->findOrFail($courseId);

        // سجل الفيديو كـ watched
        $user->watchedVideos()->syncWithoutDetaching([$videoId]);

        // عدد الفيديوهات اللي الطالب شافها
        $watchedCount = $user->watchedVideos()
                             ->where('course_id', $courseId)
                             ->count();

        // إجمالي الفيديوهات
        $totalVideos = $course->videos->count();

        // نسبة التقدم
        $percentage = $totalVideos > 0 ? ($watchedCount / $totalVideos) * 100 : 0;

        // خزّن أو حدّث الجدول progress
        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $courseId,
            ],
            [
                'videos_watched' => $watchedCount,
                'total_videos'   => $totalVideos,
                'percentage'     => $percentage,
            ]
        );

        return response()->json([
            'message' => 'Video marked as watched',
            'progress' => $progress,
        ]);
    }
}