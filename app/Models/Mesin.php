<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    protected $table = 'mesin';

    protected $fillable = [
        'nama', 'bagian', 'oee', 'availability', 'mttr_jam', 'mtbf_jam',
        'downtime_bulan_ini_jam', 'jumlah_perbaikan_bulan_ini', 'status',
    ];

    protected $casts = [
        'oee' => 'float',
        'availability' => 'float',
        'mttr_jam' => 'float',
        'mtbf_jam' => 'float',
        'downtime_bulan_ini_jam' => 'float',
        'jumlah_perbaikan_bulan_ini' => 'integer',
    ];

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class);
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function trends()
    {
        return $this->hasMany(KpiTrend::class);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Normal' => 'b-green',
            'Perlu Perhatian' => 'b-red',
            'Dalam Perbaikan' => 'b-amber',
            default => 'b-gray',
        };
    }
}
