<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabel bersama dengan Project 1 (SIPPM)
|--------------------------------------------------------------------------
| damage_reports SEHARUSNYA dimiliki & dimigrasikan oleh Project 1.
| SIMPM (Project 2) hanya MEMBACA (read-only) — ditampilkan di layar
| "Riwayat Maintenance" dengan label eksplisit "tersinkron dari SIPPM".
|
| Migration ini disediakan supaya SIMPM tetap bisa dijalankan MANDIRI
| untuk demo/pengembangan sebelum kedua project digabung satu database.
| Begitu digabung, HAPUS migration ini di sisi SIMPM dan cukup pakai
| Model App\Models\DamageReport untuk membaca tabel milik Project 1.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('no_laporan')->unique(); // BR-2026-014
            $table->foreignId('mesin_id')->constrained('mesin');
            $table->enum('kategori', ['Mekanik', 'Elektrik', 'Instrumentasi']);
            $table->string('komponen_diganti')->nullable();
            $table->string('tindakan')->nullable();
            $table->string('teknisi_nama');
            $table->integer('downtime_menit');
            $table->date('diselesaikan_pada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
