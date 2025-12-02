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
        $data = Schedule::with(['kelas', 'trainer'])
            ->orderBy('day')
            ->orderBy('start_time') // gunakan start_time, bukan "time"
            ->get();

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

    /**
     * Get schedules for a specific trainer
     */
    public function byTrainer(Request $request)
    {
        $trainerId = $request->trainer_id;

        if (!$trainerId) {
            return response()->json([
                'status' => false,
                'message' => 'trainer_id wajib dikirim'
            ], 400);
        }

        $data = Schedule::with(['kelas', 'trainer'])
            ->where('trainer_id', $trainerId)
            ->where('is_active', 1)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data schedule trainer berhasil diambil',
            'data' => $data
        ]);
    }
}
