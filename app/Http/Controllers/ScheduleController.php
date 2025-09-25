<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Kelas;
use App\Models\User;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['kelas', 'trainer'])->get();
        $kelas = Kelas::all();
        $trainers = User::where('role', 'trainer')->get(); // ambil dari users

        return view('admin.schedule', compact('schedules', 'kelas', 'trainers'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $trainers = User::where('role', 'trainer')->get(); // ambil dari users

        return view('admin.schedules.create', compact('kelas', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required',
            'trainer_id' => 'required',
            'day'        => 'required',
            'time'       => 'required',
        ]);

        Schedule::create($request->all());

        return redirect()
            ->route('schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    // edit atau update
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'trainer_id' => 'required|exists:trainers,id',
            'day'        => 'required|string|max:20',
            'time'       => 'required',
        ]);

        $schedule->update([
            'kelas_id'   => $request->kelas_id,
            'trainer_id' => $request->trainer_id,
            'day'        => $request->day,
            'time'       => $request->time,
        ]);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }
    public function toggleActive(Schedule $schedule)
    {
        $schedule->is_active = !$schedule->is_active; // kalau 1 jadi 0, kalau 0 jadi 1
        $schedule->save();

        return redirect()->back()->with('success', 'Status jadwal berhasil diubah.');
    }
}
