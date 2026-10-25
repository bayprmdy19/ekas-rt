<?php

namespace App\Console\Commands;

use App\Models\Pengaduan;
use Illuminate\Console\Command;

class AutoRatePengaduan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengaduan:auto-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menetapkan rating = 5 dan ulasan default untuk aduan berstatus selesai yang tidak dirating warga dalam 1x24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa aduan yang telah berstatus selesai lebih dari 24 jam tanpa rating...');

        $affected = Pengaduan::autoResolveExpiredRatings();

        $this->info("Berhasil memperbarui {$affected} aduan secara otomatis dengan rating 5 (Layanan tuntas).");

        return Command::SUCCESS;
    }
}
