<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceChecklist extends Model
{
    protected $table = 'maintenance_checklists';
    protected $fillable = ['schedule_id', 'item', 'is_done', 'urutan'];
    protected $casts = ['is_done' => 'boolean'];

    public function schedule() { return $this->belongsTo(MaintenanceSchedule::class, 'schedule_id'); }
}
