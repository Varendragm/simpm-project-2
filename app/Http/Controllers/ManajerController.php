<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\KpiTrend;
use App\Models\Mesin;
use Illuminate\Http\Request;

class ManajerController extends Controller
{
    // Read-only — Manajer tidak melakukan input operasional.
    public function dashboard()
    {
        $mesinList = Mesin::all();

        $stat = [
            'oee_pabrik' => round($mesinList->avg('oee')),
            'availability_pabrik' => round($mesinList->avg('availability')),
            'downtime_bulan_ini' => round($mesinList->sum('downtime_bulan_ini_jam'), 1),
            'total_perbaikan' => $mesinList->sum('jumlah_perbaikan_bulan_ini'),
            'mesin_bermasalah' => $mesinList->sortByDesc('downtime_bulan_ini_jam')->first(),
        ];

        $trenDowntimePerMesin = KpiTrend::whereNotNull('mesin_id')->where('periode_tipe', 'bulanan')->with('mesin')->orderBy('urutan')->get();

        return view('manajer.dashboard', compact('mesinList', 'stat', 'trenDowntimePerMesin'));
    }

    public function performa(Request $request)
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $mesinAktif = $request->filled('mesin_id') ? Mesin::find($request->mesin_id) : null;

        return view('manajer.performa', compact('mesinList', 'mesinAktif'));
    }

    public function maintenance()
    {
        $trenKategori = KpiTrend::whereNull('mesin_id')->where('periode_tipe', 'bulanan')->orderBy('urutan')->get();
        $ranking = Mesin::orderByDesc('downtime_bulan_ini_jam')->get();

        return view('manajer.maintenance', compact('trenKategori', 'ranking'));
    }

    public function laporan()
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $riwayat = DamageReport::with('mesin')->orderByDesc('diselesaikan_pada')->get();

        return view('manajer.laporan', compact('mesinList', 'riwayat'));
    }

    public function profil()
    {
        return view('manajer.profil');
    }

    public function profilUpdate(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'no_hp' => 'nullable|string']);
        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
