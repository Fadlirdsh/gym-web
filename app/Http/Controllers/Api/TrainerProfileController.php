<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainerProfileController extends Controller
{
    /**
     * Ambil profile trainer login
     */
    public function show()
    {
        return response()->json([
            'data' => Auth::user()->trainerProfile
        ]);
    }

    /**
     * Simpan / update profile trainer + foto
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:100',
        ]);

        // handle upload foto
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('trainer_profiles', 'public');
        }

        $profile = TrainerProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Profil trainer berhasil disimpan',
            'data' => $profile
        ]);
    }
}
