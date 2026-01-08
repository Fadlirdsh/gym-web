<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ VALIDASI SEKALI SAJA (BERSIH & AMAN)
        $validated = $request->validate([
            // ===== USER =====
            'name'  => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',

            // ===== TRAINER PROFILE =====
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:100',
        ]);

        DB::transaction(function () use ($validated, $request, $user) {

            /* =====================
               UPDATE USER (IDENTITY)
            ===================== */
            $dataUser = Arr::only($validated, ['name', 'phone']);

            if (!empty($dataUser)) {
                $user->update($dataUser);
            }

            /* =====================
               UPDATE TRAINER PROFILE
            ===================== */
            $dataProfile = Arr::except($validated, ['name', 'phone']);

            if ($request->hasFile('photo')) {
                $dataProfile['photo'] = $request
                    ->file('photo')
                    ->store('trainer_profiles', 'public');
            }

            TrainerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $dataProfile
            );
        });

        return response()->json([
            'message' => 'Profil trainer berhasil disimpan'
        ]);
    }
}
