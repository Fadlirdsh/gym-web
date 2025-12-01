<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'jenis_transaksi' => 'nullable|string',
            'channel' => 'nullable|string',
            'status' => 'nullable|string',
            'nilai' => 'required|integer',
            'source_id' => 'nullable|integer',
            'jenis' => 'nullable|string', // member / reservasi
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi' => $request->order_id,
            'user_id'        => $request->user_id ?? auth()->id(),
            'jenis'          => $request->jenis ?? 'pembayaran',
            'source_id'      => $request->source_id,
            'jumlah'         => $request->nilai,
            'metode'         => $request->channel ?? 'midtrans',
            'status'         => $request->status ?? 'pending',
        ]);

        // 🔥 JIKA PEMBAYARAN SUKSES — UPDATE STATUS MEMBER
        if (($request->status === 'success' || $request->status === 'settlement')
            && $request->jenis === 'member'
        ) {
            Member::where('id', $request->source_id)
                ->update(['status' => 'aktif']);
        }

        // 🔥 JIKA PEMBAYARAN UNTUK RESERVASI
        if (($request->status === 'success' || $request->status === 'settlement')
            && $request->jenis === 'reservasi'
        ) {
            Reservasi::where('id', $request->source_id)
                ->update(['status' => 'approved']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi disimpan',
            'data' => $transaksi,
            'status' => $transaksi->status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TRANSAKSI (dipanggil setelah klik "Bayar Sekarang")
    |--------------------------------------------------------------------------
    |
    | - jenis: "member" atau "reservasi"
    | - source_id: id member atau id reservasi
    | - jumlah: harga total
    | - metode: midtrans
    |
    */

    public function create(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'jenis'     => 'required|in:member,reservasi',
            'source_id' => 'required|integer',
            'jumlah'    => 'required|integer|min:1000',
        ]);

        // buat kode transaksi
        $kode = "TRX-" . strtoupper(Str::random(10));

        $transaksi = Transaksi::create([
            'kode_transaksi' => $kode,
            'user_id'        => $request->user_id,
            'jenis'          => $request->jenis,
            'source_id'      => $request->source_id,
            'jumlah'         => $request->jumlah,
            'metode'         => 'midtrans',
            'status'         => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data'    => $transaksi
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CALLBACK MIDTRANS
    |--------------------------------------------------------------------------
    |
    | Midtrans akan mengirim notifikasi ke endpoint ini.
    | Pastikan URL callback terdaftar di dashboard Midtrans.
    |
    */

    public function callback(Request $request)
    {
        $notif = $request->all();
        $kode = $notif['order_id'] ?? null;

        if (!$kode) return response()->json(['message' => 'Invalid callback'], 400);

        $transaksi = Transaksi::where('kode_transaksi', $kode)->first();
        if (!$transaksi) return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $status = $notif['transaction_status'] ?? 'pending';

        if ($status === 'capture' || $status === 'settlement') {
            // Update status transaksi
            $transaksi->update(['status' => 'success']);

            if ($transaksi->jenis === 'member') {
                // Ambil member
                $member = Member::find($transaksi->source_id);

                if (!$member) {
                    // Jika belum ada member, buat otomatis
                    $member = Member::create([
                        'user_id' => $transaksi->user_id,
                        'tipe_kelas' => $notif['tipe_kelas'] ?? 'Pilates Group',
                        'harga' => $transaksi->jumlah,
                        'token_total' => $notif['maks_kelas'] ?? 10,
                        'token_terpakai' => 0,
                        'token_sisa' => $notif['maks_kelas'] ?? 10,
                        'tanggal_mulai' => now(),
                        'tanggal_berakhir' => now()->addMonth(),
                        'status' => 'aktif',
                    ]);

                    // Update source_id transaksi
                    $transaksi->update(['source_id' => $member->id]);
                } else {
                    // Jika member sudah ada, cukup aktifkan
                    $member->update([
                        'status' => 'aktif',
                        'tanggal_mulai' => now(),
                        'tanggal_berakhir' => now()->addMonth(),
                        'token_total' => $member->maks_kelas,
                        'token_terpakai' => 0,
                        'token_sisa' => $member->maks_kelas,
                    ]);
                }
            }

            if ($transaksi->jenis === 'reservasi') {
                Reservasi::where('id', $transaksi->source_id)
                    ->update(['status' => 'approved']);
            }
        }

        if (in_array($status, ['deny', 'expire', 'cancel'])) {
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
        return Transaksi::with('user')->orderBy('id', 'desc')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL TRANSAKSI
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        return Transaksi::with(['user'])->findOrFail($id);
    }
    public function sync()
    {
        // Ambil semua transaksi yang ada order_id
        $transaksis = Transaksi::whereNotNull('order_id')->get();

        if ($transaksis->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada transaksi untuk disinkronkan'
            ], 404);
        }

        $serverKey = config('midtrans.server_key');

        foreach ($transaksis as $trx) {
            try {
                $url = "https://api.sandbox.midtrans.com/v2/" . $trx->order_id . "/status";

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    ]
                ]);

                $status = json_decode($response->getBody(), true);

                // Update status transaksi di database
                $trx->update([
                    'status'          => $status['transaction_status'] ?? $trx->status,
                    'payment_type'    => $status['payment_type'] ?? $trx->payment_type,
                    'gross_amount'    => $status['gross_amount'] ?? $trx->gross_amount,
                    'transaction_time' => $status['transaction_time'] ?? $trx->transaction_time,
                ]);
            } catch (\Exception $e) {
                // Abaikan error transaksi tertentu agar loop tetap jalan
                continue;
            }
        }

        return response()->json([
            'message' => 'Sync transaksi berhasil',
            'data'    => Transaksi::latest()->get()
        ]);
    }
}
