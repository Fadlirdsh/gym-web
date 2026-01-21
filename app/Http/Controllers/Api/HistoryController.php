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

        $reservasi = Reservasi::where('id', $id)
            ->where('pelanggan_id', $userId)
            ->with([
                'schedule.kelas',
                'schedule.trainerShift.trainer',
                'transaksi'
            ])
            ->firstOrFail();

        $transaksi = $reservasi->transaksi;

        if ($transaksi) {
            if ($transaksi->jenis === 'token') {
                $metode = 'token';
            } else {
                $metode = 'midtrans';
            }
        } else {
            $metode = 'free';
        }

        return response()->json([
            // =========================
            // IDENTITAS RESERVASI
            // =========================
            'id' => $reservasi->id,
            'tanggal_reservasi' => $reservasi->tanggal->format('Y-m-d'),
            'jam_mulai' => optional($reservasi->schedule)->start_time,
            'jam_selesai' => optional($reservasi->schedule)->end_time,

            'status_kehadiran' => $reservasi->final_status,
            'status_label' => match ($reservasi->final_status) {
                'attended' => 'Hadir',
                'no_show'  => 'Tidak Hadir',
                'canceled' => 'Dibatalkan',
                default    => 'Menunggu',
            },

            // =========================
            // INFORMASI KELAS
            // =========================
            'kelas' => [
                'nama' => optional($reservasi->schedule->kelas)->nama,
                'tipe' => optional($reservasi->schedule->kelas)->tipe,
                'kapasitas' => [
                    'terisi' => $reservasi->schedule->peserta_count ?? 0,
                    'maksimal' => optional($reservasi->schedule->kelas)->kapasitas,
                ],
            ],

            // =========================
            // TRAINER
            // =========================
            'trainer' => [
                'nama' => optional(
                    $reservasi->schedule?->trainerShift?->trainer
                )->name,
                'foto' => optional(
                    $reservasi->schedule?->trainerShift?->trainer
                )->foto_url,
            ],


            // =========================
            // KEHADIRAN
            // =========================
            'kehadiran' => [
                'check_in' => $reservasi->check_in_time,
                'check_out' => $reservasi->check_out_time,
            ],

            // =========================
            // PEMBAYARAN
            // =========================


            'pembayaran' => $reservasi->transaksi ? [
                'metode' => $metode,
                'harga_kelas' => $reservasi->transaksi->harga_asli,
                'token_dipakai' => $reservasi->transaksi->token_dipakai,
                'diskon' => $reservasi->transaksi->diskon,
                'total_bayar' => $reservasi->transaksi->total_bayar,
                'status' => $reservasi->transaksi->status,
            ] : [
                'status' => 'free',
                'keterangan' => 'Termasuk membership',
            ],
        ]);
    }
}
