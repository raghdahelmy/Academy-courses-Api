<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseVideoController extends Controller
{
    
    public function index(Course $course)
    {
        $videos = $course->courseVideos()->orderBy('order')->get();
  $videos = $videos->map(function ($video) {
        if (!$video->is_free) {
            $video->video_path = null; // مقفول لغير المشترك
        }
        return $video;
        return response()->json([
            'data' => $videos,
            'message' => 'تم جلب الفيديوهات بنجاح'
        ], 200);
       });
}

    
    public function show(CourseVideo $courseVideo)
    {
        return response()->json([
            'data' => $courseVideo,
            'message' => 'تم عرض الفيديو بنجاح'
        ], 200);
    }

    /**
     * إنشاء فيديو جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string',
            'video_path' => 'required|string',
            'content' => 'nullable|string',
            'progress' => 'nullable|integer',
            'overview' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean'
        ]);

        $video = CourseVideo::create($data);

        return response()->json([
            'data' => $video,
            'message' => 'تم إنشاء الفيديو بنجاح'
        ], 201);
    }

    /**
     * تحديث فيديو موجود
     */
    public function update(Request $request, CourseVideo $courseVideo)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'video_path' => 'sometimes|string',
            'content' => 'nullable|string',
            'progress' => 'nullable|integer',
            'overview' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean'
        ]);

        $courseVideo->update($data);

        return response()->json([
            'data' => $courseVideo,
            'message' => 'تم تحديث الفيديو بنجاح'
        ], 200);
    }

    /**
     * حذف فيديو
     */
    public function destroy(CourseVideo $courseVideo)
    {
        $courseVideo->delete();

        return response()->json([
            'message' => 'تم حذف الفيديو بنجاح'
        ], 200);
    }
}
