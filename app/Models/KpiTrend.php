<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiTrend extends Model
{
    protected $table = 'kpi_trends';
    protected $fillable = [
        'mesin_id', 'periode_tipe', 'label_periode', 'urutan', 'oee', 'availability',
        'downtime_jam', 'perbaikan_mekanik', 'perbaikan_elektrik', 'perbaikan_instrumentasi',
    ];

    public function mesin() { return $this->belongsTo(Mesin::class); }
}
