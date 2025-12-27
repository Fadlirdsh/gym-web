<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 load relasi yang benar
        $query = VisitLog::with(['user', 'kelas']);

        // 🔹 Quick filter
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
        // 🔹 Filter manual range
        elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }
        // 🔹 Filter 1 tanggal
        elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        // 🔹 Default: hari ini
        else {
            $query->whereDate('created_at', today());
        }

        // ✅ PAGINATION DITAMBAHKAN (INI SATU-SATUNYA PERUBAHAN BESAR)
        $visitLogs = $query
            ->latest()
            ->paginate(10)
            ->appends($request->query());


        return view('admin.visitlog', compact('visitLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        VisitLog::create([
            'user_id'  => auth()->id(),
            'kelas_id' => $request->kelas_id,
            'status'   => 'hadir',
            'catatan'  => null,
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
        ]);
    }
}
