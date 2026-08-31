<?php

namespace App\Services;

use App\Models\DamageReport;
use App\Models\Mesin;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KpiCalculatorService
{
    public function machineSummary(Mesin $mesin, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $to = ($to ?? now())->copy()->endOfDay();
        $from = ($from ?? $to->copy()->startOfMonth())->copy()->startOfDay();

        $reports = DamageReport::where('mesin_id', $mesin->id)
            ->whereNotNull('diselesaikan_pada')
            ->whereBetween('diselesaikan_pada', [$from, $to])
            ->orderBy('diselesaikan_pada')
            ->get();

        $downtimeMinutes = (int) $reports->sum(fn (DamageReport $report) => max(0, (int) $report->downtime_menit));
        $repairCount = $reports->count();
        $periodMinutes = max(1, $from->diffInMinutes($to) + 1);
        $availability = max(0, min(100, (($periodMinutes - $downtimeMinutes) / $periodMinutes) * 100));
        $mttrHours = $repairCount > 0 ? ($downtimeMinutes / $repairCount) / 60 : 0;
        $mtbfHours = $this->calculateMtbfHours($reports, $from, $to);

        return [
            'from' => $from,
            'to' => $to,
            'repair_count' => $repairCount,
            'downtime_minutes' => $downtimeMinutes,
            'downtime_hours' => round($downtimeMinutes / 60, 1),
            'availability' => round($availability, 1),
            'mttr_hours' => round($mttrHours, 1),
            'mtbf_hours' => round($mtbfHours, 1),
            'oee' => $mesin->oee === null ? null : round((float) $mesin->oee, 1),
        ];
    }

    public function factorySummary(?Carbon $from = null, ?Carbon $to = null): array
    {
        $machines = Mesin::orderBy('nama')->get();
        $summaries = $machines->map(fn (Mesin $mesin) => $this->machineSummary($mesin, $from, $to));

        return [
            'machine_count' => $machines->count(),
            'repair_count' => (int) $summaries->sum('repair_count'),
            'downtime_hours' => round((float) $summaries->sum('downtime_hours'), 1),
            'availability' => round((float) $summaries->avg('availability'), 1),
            'mttr_hours' => round((float) $summaries->avg('mttr_hours'), 1),
            'mtbf_hours' => round((float) $summaries->avg('mtbf_hours'), 1),
        ];
    }

    private function calculateMtbfHours(Collection $reports, Carbon $from, Carbon $to): float
    {
        if ($reports->isEmpty()) {
            return $from->diffInMinutes($to) / 60;
        }

        $failureDates = $reports
            ->map(fn (DamageReport $report) => Carbon::parse($report->diselesaikan_pada)->startOfDay())
            ->unique(fn (Carbon $date) => $date->toDateString())
            ->values();

        if ($failureDates->count() < 2) {
            return $from->diffInMinutes($to) / 60;
        }

        $intervals = collect();
        for ($i = 1; $i < $failureDates->count(); $i++) {
            $intervals->push($failureDates[$i - 1]->diffInMinutes($failureDates[$i]));
        }

        return ((float) $intervals->avg()) / 60;
    }
}
