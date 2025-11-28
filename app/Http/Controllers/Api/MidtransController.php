<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
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
    // 2. CONTROLLER UNTUK TESTING (PASTI WORK)
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
}
