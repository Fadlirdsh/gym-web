<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = Schedule::with([
            'kelas',
            'trainer',
            'reservasi.pelanggan'
        ]);

        // Filter berdasarkan nama client
        if ($request->filled('client')) {
            $query->whereHas('reservasi.pelanggan', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('time', $request->date);
        }

        // Filter berdasarkan jam
        if ($request->filled('time')) {
            $query->whereTime('time', $request->time);
        }

        $schedules = $query->get();

        return view('admin.schedule', compact('schedules'));
    }
}
