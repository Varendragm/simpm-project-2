<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\KpiTrend;
use App\Models\MaintenanceSchedule;
use App\Models\Mesin;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $mesinList = Mesin::orderByDesc('downtime_bulan_ini_jam')->get();

        $oeeRata = round($mesinList->avg('oee'));
        $availRata = round($mesinList->avg('availability'));
        $mttrRata = round($mesinList->avg('mttr_jam'), 1);
        $mtbfRata = round($mesinList->avg('mtbf_jam'));
        $perluPerhatian = $mesinList->whereIn('status', ['Perlu Perhatian', 'Dalam Perbaikan'])->count();

        $trenAvailability = KpiTrend::whereNull('mesin_id')->where('periode_tipe', 'mingguan')->orderBy('urutan')->get();
        $trenDowntimePerMesin = KpiTrend::whereNotNull('mesin_id')->where('periode_tipe', 'bulanan')->with('mesin')->orderBy('urutan')->get()->groupBy('mesin.nama');

        $mesinPerluPerhatian = $mesinList->whereIn('status', ['Perlu Perhatian', 'Dalam Perbaikan'])->take(3);

        return view('supervisor.dashboard', compact(
            'mesinList', 'oeeRata', 'availRata', 'mttrRata', 'mtbfRata',
            'perluPerhatian', 'trenAvailability', 'trenDowntimePerMesin', 'mesinPerluPerhatian'
        ));
    }

    public function monitoring(Request $request)
    {
        $query = Mesin::query();

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        $mesinList = $query->orderBy('nama')->get();

        return view('supervisor.monitoring', compact('mesinList'));
    }

    public function detailMesin(Mesin $mesin)
    {
        $riwayat = $mesin->damageReports()->latest('diselesaikan_pada')->take(5)->get();

        return view('supervisor.detail-mesin', compact('mesin', 'riwayat'));
    }

    public function maintenance()
    {
        $jadwal = MaintenanceSchedule::with(['mesin', 'teknisi'])->orderBy('tanggal')->get();

        return view('supervisor.maintenance', compact('jadwal'));
    }

    public function jadwalTambahForm()
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $teknisiList = User::where('role', 'teknisi')->where('is_active', true)->get();

        return view('supervisor.jadwal-tambah', compact('mesinList', 'teknisiList'));
    }

    public function jadwalSimpan(Request $request)
    {
        $data = $request->validate([
            'mesin_id' => 'required|exists:mesin,id',
            'jenis_pm' => 'required|string',
            'teknisi_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'interval' => 'required|in:Harian,Mingguan,Bulanan,Tidak Berulang',
            'estimasi_durasi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $status = \Illuminate\Support\Carbon::parse($data['tanggal'])->isToday() ? 'Jatuh Tempo Hari Ini' : 'Terjadwal';

        MaintenanceSchedule::create([
            ...$data,
            'dibuat_oleh' => $request->user()->id,
            'status' => $status,
        ]);

        return redirect()->route('supervisor.maintenance')->with('success', 'Jadwal maintenance preventif berhasil disimpan.');
    }

    public function riwayat(Request $request)
    {
        $query = DamageReport::with('mesin')->latest('diselesaikan_pada');

        if ($request->filled('mesin_id')) {
            $query->where('mesin_id', $request->mesin_id);
        }
        if ($request->filled('kategori') && $request->kategori !== 'Semua Kategori') {
            $query->where('kategori', $request->kategori);
        }

        $riwayat = $query->get();
        $mesinList = Mesin::orderBy('nama')->get();

        return view('supervisor.riwayat', compact('riwayat', 'mesinList'));
    }

    public function laporan()
    {
        $trenKategori = KpiTrend::whereNull('mesin_id')->where('periode_tipe', 'bulanan')->orderBy('urutan')->get();
        $trenDowntimeKumulatif = KpiTrend::whereNull('mesin_id')->where('periode_tipe', 'bulanan')->orderBy('urutan')->get();
        $komponenSering = DamageReport::selectRaw('komponen_diganti, kategori, count(*) as jumlah')
            ->groupBy('komponen_diganti', 'kategori')
            ->orderByDesc('jumlah')
            ->take(3)
            ->get();

        return view('supervisor.laporan', compact('trenKategori', 'trenDowntimeKumulatif', 'komponenSering'));
    }

    public function profil()
    {
        return view('supervisor.profil');
    }

    public function profilUpdate(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'no_hp' => 'nullable|string']);
        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
