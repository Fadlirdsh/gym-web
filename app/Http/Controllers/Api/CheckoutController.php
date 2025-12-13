<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KuponPengguna;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Proses checkout dengan kupon
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'total_harga'   => 'required|numeric|min:1',
            'metode'        => 'required|string',
            'jenis'         => 'required|in:member,reservasi',
            'source_id'     => 'required|numeric',
            'kupon_id'      => 'nullable|exists:kupon_pengguna,id', // opsional
        ]);

        DB::beginTransaction();

        try {
            $diskon = 0;
            $hargaAkhir = $request->total_harga;

            // 🔍 Ambil kupon jika ada
            $kupon = null;
            if ($request->kupon_id) {
                $kupon = KuponPengguna::where('id', $request->kupon_id)
                    ->where('user_id', $request->user_id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();

                if (!$kupon) {
                    return response()->json([
                        'message' => 'Kupon tidak valid atau sudah dipakai'
                    ], 422);
                }

                if (!$kupon->masih_berlaku) {
                    return response()->json([
                        'message' => 'Kupon sudah tidak berlaku'
                    ], 422);
                }

                // 💸 Hitung diskon
                if ($kupon->persentase_diskon) {
                    $diskon = ($request->total_harga * $kupon->persentase_diskon) / 100;
                    $hargaAkhir = max(0, $request->total_harga - $diskon);
                }

                // 📝 Update kupon jadi sudah dipakai
                $kupon->update([
                    'status' => 'used',
                    'harga_setelah_diskon' => $hargaAkhir
                ]);
            }

            // 💾 Simpan transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
                'user_id'        => $request->user_id,
                'jenis'          => $request->jenis,
                'source_id'      => $request->source_id,
                'jumlah'         => $hargaAkhir,
                'metode'         => $request->metode,
                'status'         => 'paid',
            ]);

            DB::commit();

            return response()->json([
                'message'       => 'Checkout berhasil',
                'transaksi'     => $transaksi,
                'diskon'        => $diskon,
                'total'         => $hargaAkhir,
                'kupon'         => $kupon ? $kupon->kode_kupon : null
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
