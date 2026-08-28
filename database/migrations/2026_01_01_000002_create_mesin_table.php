<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesin', function (Blueprint $table) {
            $table->id();
            $table->string('nama');          // Gilingan 01
            $table->string('bagian')->default('Gilingan');

            // Snapshot KPI bulan berjalan — diperbarui berkala (job/observer)
            // dari agregat damage_reports. Disimpan di sini supaya dashboard
            // cepat dibaca tanpa hitung ulang tiap request.
            $table->decimal('oee', 5, 2)->default(0);            // %
            $table->decimal('availability', 5, 2)->default(0);   // %
            $table->decimal('mttr_jam', 6, 2)->default(0);       // rata-rata jam
            $table->decimal('mtbf_jam', 6, 2)->default(0);       // rata-rata jam
            $table->decimal('downtime_bulan_ini_jam', 6, 2)->default(0);
            $table->integer('jumlah_perbaikan_bulan_ini')->default(0);
            $table->enum('status', ['Normal', 'Perlu Perhatian', 'Dalam Perbaikan'])->default('Normal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesin');
    }
};
