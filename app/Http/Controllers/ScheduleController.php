<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Kelas;
use App\Models\User;
use App\Models\TrainerShift;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleController extends Controller
{
    /**
     * =========================
     * LIST SCHEDULE
     * =========================
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['kelas', 'trainerShift.trainer']);

        // filter trainer (via shift)
        if ($request->filled('trainer_id')) {
            $query->whereHas('trainerShift', function ($q) use ($request) {
                $q->where('trainer_id', $request->trainer_id);
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // filter hari (via shift)
        if ($request->filled('day')) {
            $query->whereHas('trainerShift', function ($q) use ($request) {
                $q->where('day', $request->day);
            });
        }

        if ($request->filled('start_time')) {
            $query->where('start_time', $request->start_time);
        }

        $schedules = $query
            ->orderBy('start_time')
            ->paginate(15);

        $kelas    = Kelas::orderBy('nama_kelas')->get();
        $trainers = User::where('role', 'trainer')->orderBy('name')->get();

        $shifts = TrainerShift::with('trainer')
            ->orderByRaw("
                FIELD(day,
                    'Monday','Tuesday','Wednesday',
                    'Thursday','Friday','Saturday','Sunday'
                )
            ")
            ->orderBy('shift_start')
            ->paginate(10);

        return view('admin.schedule', compact(
            'schedules',
            'kelas',
            'trainers',
            'shifts'
        ));
    }

    /**
     * =========================
     * STORE SCHEDULE
     * =========================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trainer_shift_id' => 'required|exists:trainer_shifts,id',
            'kelas_id'         => 'required|exists:kelas,id',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'class_focus'      => 'nullable|string',
            'is_active'        => 'required|boolean',
            'capacity'         => 'required|integer|min:1',
        ]);

        $shift = TrainerShift::findOrFail($validated['trainer_shift_id']);

        // jam harus di dalam shift
        if (
            $validated['start_time'] < $shift->shift_start ||
            $validated['end_time']   > $shift->shift_end
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Jam kelas harus berada di dalam jam kerja trainer'
            ], 422);
        }

        // cek bentrok (hari diambil dari shift)
        $overlap = Schedule::whereHas('trainerShift', function ($q) use ($shift) {
                $q->where('trainer_id', $shift->trainer_id)
                  ->where('day', $shift->day);
            })
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal kelas bentrok dengan kelas lain'
            ], 422);
        }

        $schedule = Schedule::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Jadwal kelas berhasil ditambahkan',
            'schedule' => $schedule->load(['kelas', 'trainerShift.trainer'])
        ]);
    }

    /**
     * =========================
     * UPDATE SCHEDULE
     * =========================
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'trainer_shift_id' => 'required|exists:trainer_shifts,id',
            'kelas_id'         => 'required|exists:kelas,id',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'class_focus'      => 'nullable|string',
            'is_active'        => 'required|boolean',
            'capacity'         => 'required|integer|min:1',
        ]);

        $shift = TrainerShift::findOrFail($validated['trainer_shift_id']);

        if (
            $validated['start_time'] < $shift->shift_start ||
            $validated['end_time']   > $shift->shift_end
        ) {
            return back()->withErrors('Jam kelas harus di dalam jam kerja trainer');
        }

        $overlap = Schedule::whereHas('trainerShift', function ($q) use ($shift) {
                $q->where('trainer_id', $shift->trainer_id)
                  ->where('day', $shift->day);
            })
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors('Jadwal kelas bentrok dengan kelas lain');
        }

        $schedule->update($validated);

        return back()->with('success', 'Jadwal kelas berhasil diperbarui');
    }

    /**
     * =========================
     * DELETE
     * =========================
     */
    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kelas berhasil dihapus'
        ]);
    }

    /**
     * =========================
     * EDIT (AJAX)
     * =========================
     */
    public function edit($id)
    {
        return response()->json(
            Schedule::with(['kelas', 'trainerShift.trainer'])->findOrFail($id)
        );
    }

    /**
     * =========================
     * EXPORT PDF
     * =========================
     */
    public function exportPDF(Request $request)
    {
        $query = TrainerShift::with(['trainer', 'schedules.kelas']);

        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $shifts = $query
            ->orderByRaw("
                FIELD(day,
                    'Monday','Tuesday','Wednesday',
                    'Thursday','Friday','Saturday','Sunday'
                )
            ")
            ->orderBy('shift_start')
            ->get();

        $pdf = Pdf::loadView('admin.shift_report_pdf', compact('shifts'));

        return $pdf->download('laporan_jam_kerja_trainer.pdf');
    }
}
