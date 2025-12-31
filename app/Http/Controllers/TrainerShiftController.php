<?php

namespace App\Http\Controllers;

use App\Models\TrainerShift;
use App\Models\User;
use Illuminate\Http\Request;

class TrainerShiftController extends Controller
{
    /**
     * Tampilkan daftar shift
     */
    public function index()
    {
        $shifts = TrainerShift::with('trainer')
            ->orderByRaw("
        FIELD(day,
            'Monday','Tuesday','Wednesday',
            'Thursday','Friday','Saturday','Sunday')
        ")
            ->orderBy('shift_start')
            ->paginate(10);


        $trainers = User::where('role', 'trainer')->orderBy('name')->get();

        return view('admin.trainer_shift', compact('shifts', 'trainers'));
    }

    /**
     * Simpan shift baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trainer_id'   => 'required|exists:users,id',
            'day'          => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'shift_start'  => 'required',
            'shift_end'    => 'required|after:shift_start',
            'is_active'    => 'required|boolean',
        ]);

        $start = strtotime($validated['shift_start']);
        $end   = strtotime($validated['shift_end']);

        $durationMinutes = ($end - $start) / 60;

        // Minimal 2 jam (120 menit)
        if ($durationMinutes < 120) {
            return back()->withErrors([
                'shift_end' => 'Durasi shift minimal 2 jam'
            ]);
        }

        // Maksimal 10 jam (600 menit)
        if ($durationMinutes > 600) {
            return back()->withErrors([
                'shift_end' => 'Durasi shift maksimal 10 jam'
            ]);
        }

        // ❗ VALIDASI BENTROK SHIFT (WAJIB)
        $overlap = TrainerShift::where('trainer_id', $validated['trainer_id'])
            ->where('day', $validated['day'])
            ->where(function ($q) use ($validated) {
                $q->where('shift_start', '<', $validated['shift_end'])
                    ->where('shift_end', '>', $validated['shift_start']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'shift_start' => 'Shift trainer bentrok dengan shift lain'
            ]);
        }

        TrainerShift::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shift trainer berhasil ditambahkan'
        ]);
    }

    /**
     * Ambil data shift (AJAX / Edit)
     */
    public function show($id)
    {
        return response()->json(
            TrainerShift::with('trainer')->findOrFail($id)
        );
    }

    /**
     * Update shift
     */
    public function update(Request $request, $id)
    {
        $shift = TrainerShift::findOrFail($id);

        $validated = $request->validate([
            'trainer_id'   => 'required|exists:users,id',
            'day'          => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'shift_start'  => 'required',
            'shift_end'    => 'required|after:shift_start',
            'is_active'    => 'required|boolean',
        ]);

        $start = strtotime($validated['shift_start']);
        $end   = strtotime($validated['shift_end']);

        $durationMinutes = ($end - $start) / 60;

        // Minimal 2 jam (120 menit)
        if ($durationMinutes < 120) {
            return back()->withErrors([
                'shift_end' => 'Durasi shift minimal 2 jam'
            ]);
        }

        // Maksimal 10 jam (600 menit)
        if ($durationMinutes > 600) {
            return back()->withErrors([
                'shift_end' => 'Durasi shift maksimal 10 jam'
            ]);
        }

        // ❗ CEK BENTROK (KECUALI DIRI SENDIRI)
        $overlap = TrainerShift::where('trainer_id', $validated['trainer_id'])
            ->where('day', $validated['day'])
            ->where('id', '!=', $shift->id)
            ->where(function ($q) use ($validated) {
                $q->where('shift_start', '<', $validated['shift_end'])
                    ->where('shift_end', '>', $validated['shift_start']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'shift_start' => 'Shift trainer bentrok dengan shift lain'
            ]);
        }

        $shift->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shift trainer berhasil diperbarui'
        ]);
    }

    /**
     * Hapus shift
     */
    public function destroy($id)
    {
        TrainerShift::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shift trainer berhasil dihapus'
        ]);
    }
}
