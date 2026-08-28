<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'mesin_id', 'teknisi_id', 'dibuat_oleh', 'jenis_pm', 'interval',
        'tanggal', 'estimasi_durasi', 'catatan', 'status',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function mesin() { return $this->belongsTo(Mesin::class); }
    public function teknisi() { return $this->belongsTo(User::class, 'teknisi_id'); }
    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function checklist() { return $this->hasMany(MaintenanceChecklist::class, 'schedule_id')->orderBy('urutan'); }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Selesai' => 'b-green',
            'Jatuh Tempo Hari Ini' => 'b-amber',
            'Terjadwal' => 'b-blue',
        };
    }

    public function progresChecklist(): array
    {
        $total = $this->checklist()->count();
        $selesai = $this->checklist()->where('is_done', true)->count();
        return ['total' => $total, 'selesai' => $selesai, 'persen' => $total ? round($selesai / $total * 100) : 0];
    }
}
