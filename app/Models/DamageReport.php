<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Merepresentasikan data MILIK Project 1 (SIPPM).
 * SIMPM (Project 2) hanya membaca (read-only) — jangan pernah
 * insert/update/delete tabel ini dari sisi SIMPM.
 */
class DamageReport extends Model
{
    protected $table = 'damage_reports';

    protected $fillable = [
        'no_laporan', 'mesin_id', 'kategori', 'komponen_diganti',
        'tindakan', 'teknisi_nama', 'downtime_menit', 'diselesaikan_pada',
    ];

    protected $casts = [
        'diselesaikan_pada' => 'date',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    public function kategoriBadgeClass(): string
    {
        return match ($this->kategori) {
            'Mekanik' => 'b-amber',
            'Elektrik' => 'b-blue',
            'Instrumentasi' => 'b-purple',
            default => 'b-gray',
        };
    }
}
