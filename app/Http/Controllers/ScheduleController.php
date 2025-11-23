<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Kelas;
use App\Models\User;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk schedules
        $query = Schedule::with(['kelas', 'trainer']);

        // Filter berdasarkan nama trainer (opsional)
        if ($request->filled('trainer')) {
            $query->whereHas('trainer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->trainer . '%');
            });
        }

        // Filter berdasarkan hari (opsional)
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        // Filter berdasarkan jam (opsional)
        if ($request->filled('time')) {
            $query->whereTime('time', $request->time);
        }

        $schedules = $query->get();

        // Ambil data kelas untuk dropdown
        $kelas = Kelas::orderBy('nama_kelas')->get();

        // Ambil data trainer
        $trainers = User::where('role', 'trainer')->orderBy('name')->get();

        return view('admin.schedule', compact('schedules', 'kelas', 'trainers'));
    }

    // ======================================================
    //        STORE – Menambah Jadwal Baru
    // ======================================================
    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'time' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'trainer_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        Schedule::create([
            'day' => $request->day,
            'time' => $request->time,
            'kelas_id' => $request->kelas_id,
            'trainer_id' => $request->trainer_id,
            'is_active' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan');
    }

    // ======================================================
    //        UPDATE – Edit Jadwal
    // ======================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'day' => 'required',
            'time' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'trainer_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        $schedule = Schedule::findOrFail($id);

        $schedule->update([
            'day' => $request->day,
            'time' => $request->time,
            'kelas_id' => $request->kelas_id,
            'trainer_id' => $request->trainer_id,
            'is_active' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui');
    }

    // ======================================================
    //        DESTROY – Hapus Jadwal
    // ======================================================
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    }
}
