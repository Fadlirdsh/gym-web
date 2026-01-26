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
            'user' => $user,
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
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'foto'  => 'nullable|image|max:2048',
        ]);

        // DATA UPDATE
        $data = [
            'name'  => trim($request->name),
            'phone' => $request->filled('phone')
                ? trim($request->phone)
                : null,
        ];

        // HANDLE FOTO
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $data['foto'] = $request
                ->file('foto')
                ->store('users', 'public');
        }

        // UPDATE USER
        $user->update($data);

        // REFRESH + LOAD RELASI (INI YANG SEBELUMNYA SALAH)
        $user = $user->fresh()->load('member');

        return response()->json([
            'message' => 'Profile updated',
            'user'    => $user,
        ]);
    }
}
