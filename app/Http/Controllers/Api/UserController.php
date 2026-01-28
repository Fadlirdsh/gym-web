<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Ambil semua user dengan role = trainer beserta profile
     */
    public function getTrainers()
    {
        try {
            // Ambil semua trainer + relasi trainerProfile
            $trainers = User::with('trainerProfile')
                ->where('role', 'trainer')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trainers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data trainer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail trainer berdasarkan ID
     */
    public function getTrainerDetail($id)
    {
        try {
            $trainer = User::with('trainerProfile')
                ->where('role', 'trainer')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $trainer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail trainer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
