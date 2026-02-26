<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * ============================
     * GET /api/user/profile
     * ============================
     * Ambil profile user + data member
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('member');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'membership_status' => $user->membership_status,
                'profile_photo_url' => $user->profile_photo_url,
            ]
        ]);
    }

    /**
     * ============================
     * UPDATE PROFILE
     * ============================
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // VALIDASI
        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        // DATA UPDATE
        $data = [
            'name'  => trim($request->name),
            'phone' => $request->filled('phone')
                ? trim($request->phone)
                : null,
        ];

        // HANDLE FOTO
        if ($request->hasFile('photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $data['profile_photo'] = $request
                ->file('photo')
                ->store('users/profile', 'public');
        }

        // UPDATE USER
        $user->update($data);

        // REFRESH + LOAD RELASI (INI YANG SEBELUMNYA SALAH)
        $user = $user->fresh()->load('member');

        return response()->json([
            'message' => 'Profile updated',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'membership_status' => $user->membership_status,
                'profile_photo_url' => $user->profile_photo_url,
            ]

        ]);
    }
}
