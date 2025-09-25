<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\Diskon;
use App\Models\Reservasi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'pelanggan')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalClasses = Kelas::count();
        $totalSchedules = Schedule::where('is_active', 1)->count();

        $upcomingSchedules = Schedule::where('is_active', 1)
            ->orderBy('day', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        $activeDiscounts = Diskon::where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->count();

        $totalReservations = Reservasi::count();

        $latestUsers = User::latest()->take(5)->get();
        $latestDiscounts = Diskon::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTrainers',
            'totalClasses',
            'totalSchedules',
            'activeDiscounts',
            'totalReservations',
            'latestUsers',
            'upcomingSchedules',
            'latestDiscounts'
        ));
    }
}
