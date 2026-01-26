<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleApiController extends Controller
{
    /**
     * =========================
     * ADMIN / UMUM
     * =========================
     */

    // Get all schedules (ADMIN)
    public function index()
    {
        $data = Schedule::with([
            'kelas',
            'trainerShift.trainer'
        ])
            ->orderBy('trainer_shift_id')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Get schedule by ID
    public function show($id)
    {
        $schedule = Schedule::with([
            'kelas',
            'trainerShift.trainer'
        ])->find($id);

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

    // Get schedules by trainer (ADMIN)
    public function byTrainer(Request $request)
    {
        $trainerId = $request->trainer_id;

        if (!$trainerId) {
            return response()->json([
                'status' => false,
                'message' => 'trainer_id wajib dikirim'
            ], 400);
        }

        $data = Schedule::whereHas('trainerShift', function ($q) use ($trainerId) {
            $q->where('trainer_id', $trainerId);
        })
            ->with(['kelas', 'trainerShift.trainer'])
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function mySchedules(Request $request)
    {
         $trainerId = auth()->id(); 

        $data = Schedule::with(['kelas', 'trainerShift'])
            ->whereHas('trainerShift', function ($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId)
                    ->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * =========================
     * USER BOOKING (INI YANG DIPAKAI FRONTEND)
     * =========================
     */

    // Get available schedules by kelas + tanggal
    public function available(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal'  => 'required|date',
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $dayIso  = $tanggal->dayOfWeekIso; // 1–7 (Senin–Minggu)

        $schedules = Schedule::with(['trainerShift'])
            ->where('kelas_id', $request->kelas_id)
            ->where('is_active', true)
            ->whereHas('trainerShift', function ($q) use ($dayIso) {
                $q->where('day', $dayIso)
                    ->where('is_active', true);
            })
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) use ($request) {

                $trainer = $schedule->trainerShift?->trainer;
                $profile = $trainer?->trainerProfile;

                return [
                    'id'         => $schedule->id,
                    'start_time' => $schedule->start_time,
                    'end_time'   => $schedule->end_time,
                    'sisa_slot'  => $schedule->sisaSlot($request->tanggal),

                    'trainer' => [
                        'nama' => $trainer?->name,
                        'keahlian' => $profile && $profile->skills
                            ? implode(', ', $profile->skills)
                            : '-',
                        'pengalaman' => $profile?->experience_years
                            ? $profile->experience_years . ' tahun'
                            : '-',
                    ],
                ];
            })
            ->filter(fn($s) => $s['sisa_slot'] > 0)
            ->values();

        return response()->json($schedules);
    }
}
