<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaksi;
use App\Services\PricingService;

class CheckoutController extends Controller
{
    /**
     * 🔹 HITUNG HARGA SAJA (TANPA CREATE TRANSAKSI)
     */
    public function price(Request $request, PricingService $pricing)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = auth()->user();

        $hargaFinal = $pricing->getFinalPrice(
            $user,
            $request->kelas_id
        );

        return response()->json([
            'harga' => $hargaFinal
        ]);
    }

    /**
     * 🔹 CONFIRM CHECKOUT (CREATE TRANSAKSI)
     */
    public function confirm(Request $request, PricingService $pricing)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'metode'   => 'required|string',
            'jenis'    => 'required|in:member,reservasi',
            'source_id'=> 'required|numeric',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        try {
            // 🔥 HARGA DIHITUNG ULANG (ANTI MANIPULASI)
            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $request->kelas_id
            );

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
                'user_id'        => $user->id,
                'jenis'          => $request->jenis,
                'source_id'      => $request->source_id,
                'jumlah'         => $hargaFinal,
                'metode'         => $request->metode,
                'status'         => 'pending', // ⛔ JANGAN paid
            ]);

            DB::commit();

            return response()->json([
                'message'   => 'Transaksi dibuat',
                'transaksi' => $transaksi,
                'harga'     => $hargaFinal,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
