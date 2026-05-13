<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meja;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class GenerateMejaQr extends Command
{
    protected $signature = 'meja:generate-qr';
    protected $description = 'Generate QR otomatis untuk semua meja';

    public function handle()
    {
        $mejas = Meja::all();

        foreach ($mejas as $meja) {

            // kalau belum punya UUID QR, buat
            if (!$meja->qr_uuid) {
                $meja->qr_uuid = Str::uuid();
                $meja->save();
            }

            // URL QR (sesuaikan domain kamu nanti)
            $url = url('/table/' . $meja->qr_uuid);

            // generate QR image
            $qrImage = QrCode::format('png')
                ->size(300)
                ->generate($url);

            // simpan ke storage
            $fileName = 'qr/meja_' . $meja->no_meja . '.png';

            Storage::disk('public')->put($fileName, $qrImage);

            $this->info("QR generated for meja {$meja->no_meja}");
        }

        $this->info('SEMUA QR MEJA BERHASIL DIGENERATE!');
    }
}