<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::first();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'admin_' . time() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/avatars');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $user->avatar_url = '/uploads/avatars/' . $filename;
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Administrator profile updated successfully in database.',
            'data' => $user,
        ]);
    }
}
