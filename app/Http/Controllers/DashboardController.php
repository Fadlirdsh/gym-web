<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\Diskon;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'pelanggan')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalClasses = Kelas::count();
        $totalSchedules = Schedule::where('is_active', 1)->count();

        // Memperbaiki orderBy dari 'time' ke 'start_time'
        $upcomingSchedules = Schedule::where('is_active', 1)
            ->orderBy('day', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        $activeDiscounts = Diskon::where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->count();

        $totalReservations = Reservasi::count();

        $latestUsers = User::where('role', 'pelanggan')->latest()->take(5)->get();
        $latestDiscounts = Diskon::latest()->take(5)->get();

        // Grafik data: reservasi per bulan
        $reservations = Reservasi::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $reservationsPerMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $reservationsPerMonth[$m] = $reservations[$m] ?? 0;
        }

        // Grafik data: pengguna baru per bulan
        $trainerPerMonth = User::where('role', 'trainer')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $pelangganPerMonth = User::where('role', 'pelanggan')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            if (!isset($trainerPerMonth[$i])) $trainerPerMonth[$i] = 0;
            if (!isset($pelangganPerMonth[$i])) $pelangganPerMonth[$i] = 0;
        }

        ksort($trainerPerMonth);
        ksort($pelangganPerMonth);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTrainers',
            'totalClasses',
            'totalSchedules',
            'activeDiscounts',
            'totalReservations',
            'latestUsers',
            'upcomingSchedules',
            'latestDiscounts',
            'reservationsPerMonth',
            'trainerPerMonth',
            'pelangganPerMonth'
        ));
    }
}
