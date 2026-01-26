<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TrainerShift;

class TrainerShiftController extends Controller
{
    /**
     * GET /api/trainer/shifts
     * Ambil shift trainer yang sedang login
     */
    public function index(Request $request)
    {

        Log::info('Trainer dari token', [
            'user_id' => $request->user()->id,
            'role'    => $request->user()->role,
            'email'   => $request->user()->email,
        ]);

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

    /**
     * GET /api/trainer/shifts/{id}
     * Detail shift + schedules di dalamnya
     */
    public function show($id, Request $request)
    {
        $trainer = $request->user();

        $shift = TrainerShift::with([
            'schedules' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('start_time');
            },
            'schedules.kelas'
        ])
            ->where('id', $id)
            ->where('trainer_id', $trainer->id)
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shift
        ]);
    }
}
