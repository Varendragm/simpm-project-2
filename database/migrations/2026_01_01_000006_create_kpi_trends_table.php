<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Menyimpan seri data untuk grafik tren (mingguan/bulanan).
| mesin_id NULL = agregat seluruh pabrik (dipakai grafik Manajer/Supervisor
| tingkat pabrik); diisi = tren per mesin (dipakai grafik per mesin).
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_trends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->nullable()->constrained('mesin')->nullOnDelete();
            $table->enum('periode_tipe', ['mingguan', 'bulanan']);
            $table->string('label_periode'); // "Mgg 1", "Agu"
            $table->integer('urutan'); // untuk pengurutan sumbu-x
            $table->decimal('oee', 5, 2)->nullable();
            $table->decimal('availability', 5, 2)->nullable();
            $table->decimal('downtime_jam', 6, 2)->nullable();
            $table->integer('perbaikan_mekanik')->nullable();
            $table->integer('perbaikan_elektrik')->nullable();
            $table->integer('perbaikan_instrumentasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_trends');
    }
};
