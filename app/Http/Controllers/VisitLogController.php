<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    /**
     * READ ONLY — Visit Log
     * Sumber kehadiran = reservasi.status_hadir
     */
    public function index(Request $request)
    {
        $query = VisitLog::with([
            'user',
            'kelas',
            'reservasi'
        ])->whereHas('reservasi', function ($q) {
            $q->where('status', 'paid')
              ->where('status_hadir', 'hadir');
        });

        /* ==============================
         | QUICK FILTER
         ============================== */
        if ($request->filter) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('checkin_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('checkin_at', today()->subDay());
                    break;

                case 'week':
                    $query->whereBetween('checkin_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
            }
        }
        /* ==============================
         | RANGE FILTER
         ============================== */
        elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('checkin_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }
        /* ==============================
         | SINGLE DATE
         ============================== */
        elseif ($request->filled('date')) {
            $query->whereDate('checkin_at', $request->date);
        }
        /* ==============================
         | DEFAULT: TODAY
         ============================== */
        else {
            $query->whereDate('checkin_at', today());
        }

        $visitLogs = $query
            ->latest('checkin_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.visitlog', compact('visitLogs'));
    }
}
