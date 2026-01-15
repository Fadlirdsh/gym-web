<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberToken;
use App\Models\Reservasi;
use App\Models\QrCode;
use App\Models\TokenPackage;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionProcessor
{
    public static function process($transaksi)
    {
        DB::transaction(function () use ($transaksi) {

            // 🔒 AMBIL ULANG TRANSAKSI DENGAN LOCK
            $trx = Transaksi::where('id', $transaksi->id)
                ->lockForUpdate()
                ->first();

            // ⛔ IDEMPOTENT FINAL
            if ($trx->is_processed) {
                return;
            }

            switch ($trx->jenis) {

                case 'token':
                    self::processToken($trx);
                    break;

                case 'member':
                    self::processMember($trx);
                    break;

                case 'reservasi':
                    self::processReservasi($trx);
                    break;
            }

            // 🔐 TANDAI HAK SUDAH DIEKSEKUSI
            $trx->update(['is_processed' => true]);
        });
    }

    private static function processToken($transaksi)
    {
        $package = TokenPackage::findOrFail($transaksi->source_id);

        $member = Member::where('user_id', $transaksi->user_id)
            ->where('status', 'aktif')
            ->lockForUpdate()
            ->firstOrFail();

        $memberToken = MemberToken::firstOrCreate(
            [
                'member_id'  => $member->id,
                'tipe_kelas' => $package->tipe_kelas,
            ],
            [
                'token_total'    => 0,
                'token_terpakai' => 0,
                'token_sisa'     => 0,
            ]
        );

        $memberToken->increment('token_total', $package->jumlah_token);
        $memberToken->increment('token_sisa', $package->jumlah_token);
    }

    private static function processMember($transaksi)
    {
        $member = Member::lockForUpdate()->findOrFail($transaksi->source_id);

        // OPTIONAL: cegah reset tanggal kalau sudah aktif
        if ($member->status === 'aktif') {
            return;
        }

        $member->update([
            'status'           => 'aktif',
            'tanggal_mulai'    => now(),
            'tanggal_berakhir' => now()->addMonth(),
        ]);
    }

    private static function processReservasi($transaksi)
    {
        $reservasi = Reservasi::lockForUpdate()->findOrFail($transaksi->source_id);

        if ($reservasi->status === 'paid') {
            return;
        }

        $reservasi->update(['status' => 'paid']);

        QrCode::firstOrCreate(
            ['reservasi_id' => $reservasi->id],
            [
                'token'       => Str::uuid(),
                'expired_at'  => Carbon::parse($reservasi->tanggal)->endOfDay(),
            ]
        );
    }
}
