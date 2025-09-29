<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitLog::with(['user', 'reservasi.kelas']);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            // filter range tanggal
            $visitLogs = $query->approvedBetween($request->from_date, $request->to_date)
                ->latest()
                ->get();
        } elseif ($request->filled('date')) {
            // filter per tanggal tertentu
            $visitLogs = $query->approvedOnDate($request->date)
                ->latest()
                ->get();
        } else {
            // default hari ini
            $visitLogs = $query->approvedOnDate(today())
                ->latest()
                ->get();
        }

        return view('admin.visitlog', compact('visitLogs'));
    }
}
