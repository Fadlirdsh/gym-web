<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * GET /api/user/profile
     */
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * UPDATE PROFILE
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // VALIDASI TETAP
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'foto'  => 'nullable|image|max:2048',
        ]);

        // DATA UPDATE DIAMBIL LANGSUNG
        $data = [
            'name'  => trim($request->name),
            'phone' => $request->filled('phone')
                ? trim($request->phone)
                : null,
        ];

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $data['foto'] = $request
                ->file('foto')
                ->store('users', 'public');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated',
            'user'    => $user->fresh(),
        ]);
    }
}
