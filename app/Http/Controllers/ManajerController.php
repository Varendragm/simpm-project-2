<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\KpiTrend;
use App\Models\Mesin;
use App\Services\KpiCalculatorService;
use Illuminate\Http\Request;

class ManajerController extends Controller
{
    public function __construct(private readonly KpiCalculatorService $kpiCalculator)
    {
    }

    // Read-only — Manajer tidak melakukan input operasional.
    public function dashboard()
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $factory = $this->kpiCalculator->factorySummary();
        $mesinBermasalah = $mesinList
            ->map(function (Mesin $mesin) {
                return [$mesin, $this->kpiCalculator->machineSummary($mesin)];
            })
            ->sortByDesc(fn (array $item) => $item[1]['downtime_hours'])
            ->first();

        $stat = [
            'oee_pabrik' => round((float) $mesinList->avg('oee')),
            'availability_pabrik' => $factory['availability'],
            'downtime_bulan_ini' => $factory['downtime_hours'],
            'total_perbaikan' => $factory['repair_count'],
            'mesin_bermasalah' => $mesinBermasalah[0] ?? null,
        ];

        $trenDowntimePerMesin = KpiTrend::whereNotNull('mesin_id')
            ->where('periode_tipe', 'bulanan')
            ->with('mesin')
            ->orderBy('urutan')
            ->get();

        return view('manajer.dashboard', compact('mesinList', 'stat', 'trenDowntimePerMesin'));
    }

    public function performa(Request $request)
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $mesinAktif = $request->filled('mesin_id') ? Mesin::find($request->integer('mesin_id')) : null;
        $kpiAktif = $mesinAktif ? $this->kpiCalculator->machineSummary($mesinAktif) : null;

        return view('manajer.performa', compact('mesinList', 'mesinAktif', 'kpiAktif'));
    }

    public function maintenance()
    {
        $trenKategori = KpiTrend::whereNull('mesin_id')
            ->where('periode_tipe', 'bulanan')
            ->orderBy('urutan')
            ->get();

        $ranking = Mesin::all()
            ->map(function (Mesin $mesin) {
                $mesin->kpi_summary = $this->kpiCalculator->machineSummary($mesin);
                return $mesin;
            })
            ->sortByDesc(fn (Mesin $mesin) => $mesin->kpi_summary['downtime_hours'])
            ->values();

        return view('manajer.maintenance', compact('trenKategori', 'ranking'));
    }

    public function laporan()
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $riwayat = DamageReport::with('mesin')->latest('diselesaikan_pada')->get();

        return view('manajer.laporan', compact('mesinList', 'riwayat'));
    }

    public function profil()
    {
        return view('manajer.profil');
    }

    public function profilUpdate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:30',
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
