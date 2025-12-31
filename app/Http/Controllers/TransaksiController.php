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
    | CALLBACK MIDTRANS (SATU-SATUNYA TEMPAT UPDATE STATUS)
    |--------------------------------------------------------------------------
    */
    public function callback(Request $request)
    {
        \Log::info('🔥 MIDTRANS CALLBACK MASUK', $request->all());

        // 1️⃣ Validasi signature
        $serverKey = config('midtrans.server_key');
        $signature = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        if ($signature !== $request->signature_key) {
            \Log::warning('❌ Signature tidak valid');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2️⃣ Cari transaksi
        $transaksi = Transaksi::where('kode_transaksi', $request->order_id)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // 3️⃣ Mapping status
        if (in_array($request->transaction_status, ['capture', 'settlement'])) {

            $transaksi->update([
                'status' => 'success',
                'metode' => $request->payment_type ?? 'midtrans',
            ]);

            if ($transaksi->jenis === 'reservasi') {
                Reservasi::where('id', $transaksi->source_id)
                    ->update(['status' => 'paid']);
            }

            if ($transaksi->jenis === 'member') {
                Member::where('id', $transaksi->source_id)
                    ->update(['status' => 'aktif']);
            }
        }

        if (in_array($request->transaction_status, ['deny', 'expire', 'cancel'])) {
            $transaksi->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Callback processed']);
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
}
