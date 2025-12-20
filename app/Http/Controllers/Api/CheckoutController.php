<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaksi;
use App\Models\Kelas;
use App\Models\Voucher;
use App\Services\PricingService;

use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Midtrans configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * 🔹 HITUNG HARGA SAJA (TANPA CREATE TRANSAKSI)
     */
    public function price(Request $request, PricingService $pricing)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'voucher_id' => 'nullable|numeric',
        ]);

        $user = auth()->user();

        $hargaFinal = $pricing->getFinalPrice(
            $user,
            $request->kelas_id,
            $request->voucher_id ?? null
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
            'voucher_id' => 'nullable|numeric',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        try {
            // 🔥 HARGA DIHITUNG ULANG (ANTI MANIPULASI)
            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $request->kelas_id,
                $request->voucher_id ?? null
            );

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
                'user_id'        => $user->id,
                'jenis'          => $request->jenis,
                'source_id'      => $request->source_id,
                'jumlah'         => $hargaFinal,
                'metode'         => $request->metode,
                'status'         => 'pending', // ⛔ jangan paid
            ]);

            DB::commit();

            return response()->json([
                'message'   => 'Transaksi dibuat',
                'transaksi' => $transaksi,
                'harga'     => $hargaFinal,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Checkout confirm error: '.$e->getMessage());

            return response()->json([
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 GENERATE MIDTRANS SNAP TOKEN
     */
    public function midtransToken(Request $request, PricingService $pricing)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'voucher_id' => 'nullable|numeric',
        ]);

        $user = auth()->user();

        try {
            // 🔹 Hitung harga final
            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $request->kelas_id,
                $request->voucher_id ?? null
            );

            $kelas = Kelas::find($request->kelas_id);

            $params = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . time(),
                    'gross_amount' => $hargaFinal,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => $kelas->id,
                        'price' => $hargaFinal,
                        'quantity' => 1,
                        'name' => $kelas->nama_kelas,
                    ]
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json(['snapToken' => $snapToken]);

        } catch (\Throwable $e) {
            \Log::error('Midtrans error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
