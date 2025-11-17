<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleApiController extends Controller
{
    /**
     * Get all schedules
     */
    public function index()
    {
        $data = Schedule::with(['kelas', 'trainer'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Data schedule berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * Get schedule by ID
     */
    public function show($id)
    {
        $schedule = Schedule::with(['kelas', 'trainer'])->find($id);

        if (!$schedule) {
            return response()->json([
                'status' => false,
                'message' => 'Schedule tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $schedule
        ]);
    }
}
