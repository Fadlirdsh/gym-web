<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE TRANSAKSI (SETELAH RESERVASI DIBUAT)
    |--------------------------------------------------------------------------
    | Dipanggil setelah user klik "Reserve Now"
    | Status SELALU pending
    */
    public function create(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'jenis'          => 'required|in:member,reservasi',
            'source_id'      => 'required|integer',
            'harga_asli'     => 'required|integer|min:1000',
            'diskon'         => 'nullable|integer|min:0',
            'total_bayar'    => 'required|integer|min:1000',
        ]);

        $kode = 'TRX-' . strtoupper(Str::random(10));

        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode,
            'user_id'        => $request->user_id,
            'jenis'          => $request->jenis,
            'source_id'      => $request->source_id,
            'harga_asli'     => $request->harga_asli,
            'diskon'         => $request->diskon ?? 0,
            'total_bayar'    => $request->total_bayar,
            'metode'         => 'midtrans',
            'status'         => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi dibuat, lanjutkan pembayaran',
            'data'    => $transaksi,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST TRANSAKSI
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return Transaksi::with('user')
            ->orderBy('id', 'desc')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL TRANSAKSI
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        return Transaksi::with('user')->findOrFail($id);
    }

    public function showByKode($kode)
    {
        $transaksi = Transaksi::where('kode_transaksi', $kode)
            ->with(['reservasi.kelas'])
            ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($transaksi->status !== 'paid') {
            return response()->json(['message' => 'Transaksi belum selesai'], 400);
        }

        return response()->json([
            'kode'    => $transaksi->kode_transaksi,
            'total'   => $transaksi->total_bayar,
            'status'  => $transaksi->status,
            'kelas'   => $transaksi->reservasi->kelas->nama_kelas,
            'tanggal' => $transaksi->reservasi->jadwal->format('Y-m-d'),
            'jam'     => $transaksi->reservasi->jadwal->format('H:i'),
            'metode'  => $transaksi->metode,
        ]);
    }
}
