<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Ambil semua user dengan role = trainer
     */
    public function getTrainers()
    {
        try {
            $trainers = User::where('role', 'trainer')->get();

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
}
