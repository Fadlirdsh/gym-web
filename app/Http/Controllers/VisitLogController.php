<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    public function index()
    {
        // Ambil semua data VisitLog + relasi user & reservasi.kelas
        $visitLogs = VisitLog::with(['user', 'reservasi.kelas'])
            ->latest()
            ->get();

        // Kirim ke view
        return view('admin.visitlog', compact('visitLogs'));
    }
}
