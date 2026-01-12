<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;
use App\Enums\VisitLogStatus;

class VisitLogController extends Controller
{
    /**
     * READ ONLY — Visit Log
     * Sumber kehadiran = visit_logs.status (EVENT LOG)
     */
    public function index(Request $request)
    {
        $query = VisitLog::with([
            'user',
            'reservasi.schedule.kelas',
            'reservasi.schedule.trainerShift.trainer',
        ])->where('status', VisitLogStatus::HADIR->value);

        /* ==============================
         | QUICK FILTER
         ============================== */
        if ($request->filter) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
            }
        }
        /* ==============================
         | RANGE FILTER
         ============================== */ elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }
        /* ==============================
         | SINGLE DATE
         ============================== */ elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        /* ==============================
         | DEFAULT: TODAY
         ============================== */ else {
            $query->whereDate('created_at', today());
        }

        $visitLogs = $query
            ->latest('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.visitlog', compact('visitLogs'));
    }
}
