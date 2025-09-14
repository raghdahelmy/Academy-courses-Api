<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('courses')->get;
        return response()->json([ 
            'data' => $categories,
            'message' => 'تم عرض الاقسام بنجاح'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $validated = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($validated);
        return response()->json([ 
            'data' => $category,
            'message' => 'تم انشاء القسم بنجاح'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        $category = Category::with('courses')->get;
        return response()->json([ 
            'data' => $category,
            'message' => 'تم عرض القسم بنجاح'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
     $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة لو موجودة
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return response()->json([
            'data' => $category,
            'message' => 'تم تحديث القسم بنجاح'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'message' => 'تم حذف القسم بنجاح'
        ], 200);
    
    }
}
