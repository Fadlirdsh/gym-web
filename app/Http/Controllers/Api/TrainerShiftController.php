<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainerShift;

class TrainerShiftController extends Controller
{
    /**
     * GET /api/trainer/shifts
     * Ambil shift trainer yang sedang login
     */
    public function index(Request $request)
    {
        $trainer = $request->user(); // dari JWT

        $shifts = TrainerShift::where('trainer_id', $trainer->id)
            ->orderByRaw("
                FIELD(day,
                    'Monday','Tuesday','Wednesday',
                    'Thursday','Friday','Saturday','Sunday'
                )
            ")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }
}
