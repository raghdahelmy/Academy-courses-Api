<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'], // password_confirmation
            'avatar'       => ['nullable', 'string', 'max:255'],
            'role'         => ['nullable', 'in:user,trainer,admin'],
            'device_name'  => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar'   => $data['avatar'] ?? null,
            'role'     => $data['role'] ?? 'user',
        ]);

        $token = $user->createToken($data['device_name'] ?? 'register')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully.',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // اختياري: امنع إنشاء أكثر من توكن بنفس اسم الجهاز
        $user->tokens()->where('name', $data['device_name'])->delete();

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // POST /api/logout  (يلغي التوكن الحالي فقط)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    // POST /api/logout-all  (يلغي كل توكنات المستخدم على كل الأجهزة)
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices.',
        ]);
    }

    // GET /api/me
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // PATCH /api/password  (تغيير الباسورد)
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    // PATCH /api/profile  (تحديث بروفايل بسيط)
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'   => ['sometimes', 'string', 'max:255'],
            'avatar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email'  => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => $request->user()->fresh(),
        ]);
    }
}
