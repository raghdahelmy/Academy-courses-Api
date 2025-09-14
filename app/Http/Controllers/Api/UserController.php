<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مسموح'], 403);
        }

        $users = User::all();
        return response()->json([
            'data' => $users,
            'message' => 'تم جلب جميع المستخدمين بنجاح'
        ]);
    }

   
    
    public function show(User $user)
    {
        // المستخدم العادي يقدر يشوف نفسه فقط
        if (Auth::user()->role !== 'admin' && auth()->id() !== $user->id) {
            return response()->json(['message' => 'غير مسموح'], 403);
        }

        return response()->json([
            'data' => $user,
            'message' => 'تم جلب بيانات المستخدم بنجاح'
        ]);
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin' && auth()->id() !== $user->id) {
            return response()->json(['message' => 'غير مسموح'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|confirmed|min:6',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'role' => 'sometimes|in:user,trainer,admin'
        ]);

        // المستخدم العادي ممنوع يغير الـ role
        if (Auth::user()->role !== 'admin') {
            unset($data['role']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'data' => $user,
            'message' => 'تم تحديث المستخدم بنجاح'
        ]);
    }

    
    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مسموح'], 403);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(['message' => 'تم حذف المستخدم بنجاح']);
    }
}
