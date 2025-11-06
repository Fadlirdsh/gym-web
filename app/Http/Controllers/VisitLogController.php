<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitLog::with(['user', 'reservasi.kelas']);

        // 🔹 Jika filter cepat (today, yesterday, week)
        if ($request->filter) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
            }
        }
        // 🔹 Jika filter manual by date range
        elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }
        // 🔹 Jika filter satu tanggal (opsional)
        elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        // 🔹 Default: tampilkan data hari ini
        else {
            $query->whereDate('created_at', today());
        }

        // 🔹 Ambil hasil akhir
        $visitLogs = $query->latest()->get();

        // 🔹 Tampilkan ke view
        return view('admin.visitlog', compact('visitLogs'));
    }
}
