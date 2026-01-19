<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Transaksi;


class HistoryController extends Controller
{
    /**
     * HISTORI RESERVASI PELANGGAN
     */
    public function reservasi(Request $request)
    {
        $userId = $request->user()->id;

        $reservasi = \App\Models\Reservasi::history($userId)
            ->with(['schedule'])
            ->paginate(10);

        $reservasi->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal->format('Y-m-d'),
                'jam' => $item->schedule
                    ? substr($item->schedule->start_time, 0, 5)
                    . ' - ' .
                    substr($item->schedule->end_time, 0, 5)
                    : null,
                'status' => $item->final_status,
                'status_label' => match ($item->final_status) {
                    'attended' => 'Hadir',
                    'no_show'  => 'Tidak Hadir',
                    'canceled' => 'Dibatalkan',
                    default    => 'Menunggu',
                },
                'kelas' => optional($item->schedule->kelas)->nama ?? 'Kelas',
                'catatan' => $item->catatan,
            ];
        });


        return response()->json($reservasi);
    }
    public function reservasiDetail(Request $request, $id)
    {
        $userId = $request->user()->id;

        $reservasi = \App\Models\Reservasi::where('id', $id)
            ->where('pelanggan_id', $userId) // ⛔ keamanan WAJIB
            ->with(['schedule.kelas'])
            ->firstOrFail();

        return response()->json([
            'id' => $reservasi->id,
            'tanggal' => $reservasi->tanggal->format('Y-m-d'),
            'jam' => $reservasi->schedule
                ? substr($reservasi->schedule->start_time, 0, 5)
                . ' - ' .
                substr($reservasi->schedule->end_time, 0, 5)
                : null,
            'status' => $reservasi->final_status,
            'status_label' => match ($reservasi->final_status) {
                'attended' => 'Hadir',
                'no_show'  => 'Tidak Hadir',
                'canceled' => 'Dibatalkan',
                default    => 'Menunggu',
            },
            'kelas' => optional($reservasi->schedule->kelas)->nama ?? '-',
            'catatan' => $reservasi->catatan,
            'created_at' => $reservasi->created_at->format('Y-m-d H:i'),
        ]);
    }
}
