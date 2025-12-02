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
        $query = Schedule::with(['kelas', 'trainer']);

        // Filter trainer
        if ($request->filled('trainer')) {
            $query->whereHas('trainer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->trainer . '%');
            });
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter hari
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        // Filter jam mulai
        if ($request->filled('start_time')) {
            $query->where('start_time', $request->start_time);
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $trainers = User::where('role', 'trainer')->orderBy('name')->get();

        // Time options
        $timeOptions = [
            "07:00" => "7:00 AM",
            "07:30" => "7:30 AM",
            "08:00" => "8:00 AM",
            "08:30" => "8:30 AM",
            "09:00" => "9:00 AM",
            "09:30" => "9:30 AM",
            "10:00" => "10:00 AM",
            "10:30" => "10:30 AM",
            "11:00" => "11:00 AM",
            "11:30" => "11:30 AM",
            "12:00" => "12:00 PM",
            "12:30" => "12:30 PM",
            "14:00" => "2:00 PM",
            "14:30" => "2:30 PM",
            "15:00" => "3:00 PM",
            "15:30" => "3:30 PM",
            "16:00" => "4:00 PM",
            "16:30" => "4:30 PM",
            "17:00" => "5:00 PM",
            "18:00" => "6:00 PM",
            "19:00" => "7:00 PM",
        ];

        return view('admin.schedule', compact('schedules', 'kelas', 'trainers', 'timeOptions'));
    }

    // STORE WEEKLY ONLY
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'     => 'required|exists:kelas,id',
            'trainer_id'   => 'required|exists:users,id',
            'day'          => 'required',   // WAJIB karena weekly schedule
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            'class_focus'  => 'nullable|string',
            'is_active'    => 'required|boolean'
        ]);

        // Pastikan date selalu NULL (karena weekly schedule)
        $request->merge(['date' => null]);

        Schedule::create($request->all());

        return back()->with('success', 'Jadwal mingguan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'kelas_id'     => 'required|exists:kelas,id',
            'trainer_id'   => 'required|exists:users,id',
            'day'          => 'required',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            'class_focus'  => 'nullable|string',
            'is_active'    => 'required|boolean'
        ]);

        // Tidak boleh pakai date
        $request->merge(['date' => null]);

        $schedule->update($request->all());

        return back()->with('success', 'Jadwal mingguan berhasil diperbarui');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return back()->with('success', 'Jadwal berhasil dihapus');
    }
}
