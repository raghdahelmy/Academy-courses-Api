<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Course;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

 public function store(Request $request, Course $course)
    {
        $user = $request->user();

        // هات الموجود أو أنشئ جديد بحالة pending (منطق الموافقة عند الأدمن)
        $sub = Subscription::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status'  => 'pending']
        );

        // لو عنده اشتراك مُفعّل بالفعل، امنع التكرار
        if ($sub->status === 'active') {
            return response()->json(['message' => 'Already subscribed.'], 409);
        }
    }

    // public function index()
    // {
    //     $subscriptions = Subscription::with('user')->get();

    //     return response()->json([
    //         'data' => $subscriptions,
    //         'message' => 'تم جلب جميع الاشتراكات بنجاح'
    //     ], 200);
    // }



    // public function show(Subscription $subscription)
    // {
    //     $subscription::with('user');

    //     return response()->json([
    //         'data' => $subscription,
    //         'message' => 'تم عرض الاشتراك بنجاح'
    //     ], 200);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id'   => 'required|exists:users,id',
    //         'plan_name' => 'required|string|max:255',
    //         'price'     => 'required|numeric|min:0',
    //         'starts_at' => 'required|date',
    //         'ends_at'   => 'required|date|after:starts_at',
    //         'status'    => 'in:active,expired,canceled',
    //     ]);

    //     $subscription = Subscription::create($validated);

    //     return response()->json([
    //         'data' => $subscription,
    //         'message' => 'تم إنشاء الاشتراك بنجاح'
    //     ], 201);
    // }

    // public function update(Request $request, Subscription $subscription)
    // {
    //     $validated = $request->validate([
    //         'plan_name' => 'sometimes|string|max:255',
    //         'price'     => 'sometimes|numeric|min:0',
    //         'starts_at' => 'sometimes|date',
    //         'ends_at'   => 'sometimes|date|after:starts_at',
    //         'status'    => 'in:active,expired,canceled',
    //     ]);

    //     $subscription->update($validated);

    //     return response()->json([
    //         'data' => $subscription,
    //         'message' => 'تم تحديث الاشتراك بنجاح'
    //     ], 200);
    // }


    // public function destroy(Subscription $subscription)
    // {
    //     $subscription->delete();

    //     return response()->json([
    //         'message' => 'تم حذف الاشتراك بنجاح'
    //     ], 200);
    // }
}
