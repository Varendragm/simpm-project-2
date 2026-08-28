<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin');
            $table->foreignId('teknisi_id')->constrained('users');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
            $table->string('jenis_pm'); // "Pemeriksaan gearbox & getaran"
            $table->enum('interval', ['Harian', 'Mingguan', 'Bulanan', 'Tidak Berulang']);
            $table->date('tanggal');
            $table->string('estimasi_durasi')->nullable(); // "45 menit"
            $table->text('catatan')->nullable();
            $table->enum('status', ['Terjadwal', 'Jatuh Tempo Hari Ini', 'Selesai'])->default('Terjadwal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
