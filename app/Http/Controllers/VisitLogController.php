<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitLogController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 load relasi yang benar
        $query = VisitLog::with(['user', 'kelas']);

        // Filter cepat
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
        // Filter manual range
        elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }
        // Filter 1 tanggal
        elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        // Default
        else {
            $query->whereDate('created_at', today());
        }

        $visitLogs = $query->latest()->get();

        return view('admin.visitlog', compact('visitLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        VisitLog::create([
            'user_id' => auth()->id(),
            'kelas_id' => $request->kelas_id,
            'status' => 'hadir',
            'catatan' => null,
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
        ]);
    }
    
    public function generateQR()
    {
        $qr = QrCode::size(300)->generate('Hello World');
        return view('qr', compact('qr'));
    }
}
