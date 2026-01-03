<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Ambil dari config/midtrans.php
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    // ==============================
    // 1. CREATE TRANSACTION UMUM
    // ==============================
    public function createTransaction(Request $request)
    {
        $params = [
            'transaction_details' => [
                'order_id'     => 'ORDER-' . time(),
                'gross_amount' => $request->amount,
            ],
            'customer_details' => [
                'first_name' => $request->name,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'token' => $snapToken,
        ]);
    }

    // ==============================
    // 2. CONTROLLER UNTUK TESTING
    // ==============================
    public function testTransaction()
    {
        $params = [
            'transaction_details' => [
                'order_id'     => rand(),
                'gross_amount' => 10000,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'token' => $snapToken,
        ]);
    }

    // ==============================
    // 3. TOKEN UNTUK BOOKING KELAS
    // ==============================
    public function getSnapToken(Request $request)
    {
        // 🔥 Log setiap request yang masuk dari front-end
        Log::info("MIDTRANS REQUEST:", $request->all());

        // Validasi wajib
        $request->validate([
            'paket_id' => 'required|integer',
            'harga' => 'required|integer',
            'tipe_kelas' => 'required|string',
            'maks_kelas' => 'required|integer',
        ]);

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => uniqid("ORD-"),
                    'gross_amount' => $request->harga,
                ],
                'item_details' => [
                    [
                        'id' => $request->paket_id,
                        'price' => $request->harga,
                        'quantity' => 1,
                        'name' => $request->tipe_kelas,
                    ]
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name ?? "User",
                ]
            ];

            // 🔥 Log parameter final ke Midtrans
            Log::info("MIDTRANS PARAMS:", $params);

            // Ambil snap token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snapToken' => $snapToken
            ]);
        } catch (\Exception $e) {
            // 🔥 Log error detail
            Log::error("MIDTRANS ERROR", [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
