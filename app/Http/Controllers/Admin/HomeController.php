<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $totalPelanggan = \App\Models\User::where('role', 'pelanggan')->count();
        $reservasiHariIni = \App\Models\Reservasi::whereDate('created_at', today())->count();
        $kunjunganHariIni = \App\Models\VisitLog::whereDate('created_at', today())->count();

        return view('admin.home', compact('totalPelanggan', 'reservasiHariIni', 'kunjunganHariIni'))
            ->with('user', auth()->user());
    }

    //
}
