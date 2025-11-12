<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // ✅ Total pelanggan (user dengan role = 'pelanggan')
        $totalPelanggan = User::where('role', 'pelanggan')->count();

        // ✅ Total reservasi hari ini
        $reservasiHariIni = Reservasi::whereDate('created_at', Carbon::today())->count();

        // ✅ Total kunjungan hari ini (jika kolom 'tanggal_hadir' ada)
        $kunjunganHariIni = 0;
        if (Schema::hasColumn('reservasis', 'tanggal_hadir')) {
            $kunjunganHariIni = Reservasi::whereDate('tanggal_hadir', Carbon::today())->count();
        }

        // ✅ Panggil view yang ada di folder /resources/views/admin/
        return view('admin.home', compact(
            'totalPelanggan',
            'reservasiHariIni',
            'kunjunganHariIni'
        ));
    }
}
