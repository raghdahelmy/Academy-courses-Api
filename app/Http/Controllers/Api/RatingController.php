<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    public function store(Request $request, Course $course)
{
    $request->validate([
        'value' => 'required|integer|min:1|max:5',
    ]);

    $user = $request->user();
    
if (! in_array($user->role,['admin','trainer'])){
     return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Only trainers or admins can rate courses.'
        ], 403);
}
    $rating = $course->ratings()->create([
        'trainer' => $request->user()->id,
        'value'      => $request->value,
    ]);

    return response()->json([
        'message' => 'Rating added successfully',
        'rating'  => $rating
    ], 201);
}

}
