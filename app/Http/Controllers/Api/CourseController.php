<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{

    public function index()
    {
        $courses = Course::with(['courseVideos','trainer:id,name,avatar','ratings'])->get();

        return response()->json([
            'data' => $courses,
            'message' => 'تم عرض جميع الكورسات بنجاح'
        ], 200);
    }

    /**
     */
    public function show($id)
    {
        $course = Course::with(['courseVideos', 'trainer'])->findOrFail($id);

        return response()->json([
            'data' => $course,
            'message' => 'تم عرض الكورس بنجاح'
        ], 200);
    }

    /**
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($validated);

        return response()->json([
            'data' => $course,
            'message' => 'تم تحديث الكورس بنجاح'
        ], 200);
    }

    public function destroy(Course $course)
    {
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json([
            'message' => 'تم حذف الكورس بنجاح'
        ], 200);
    }
}
