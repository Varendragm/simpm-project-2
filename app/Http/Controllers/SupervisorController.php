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

        $oeeRata = round((float) $mesinList->avg('oee'));
        $availRata = round((float) $mesinList->avg('availability'));
        $mttrRata = round((float) $mesinList->avg('mttr_jam'), 1);
        $mtbfRata = round((float) $mesinList->avg('mtbf_jam'));
        $perluPerhatian = $mesinList->whereIn('status', ['Perlu Perhatian', 'Dalam Perbaikan'])->count();

        $trenAvailability = KpiTrend::whereNull('mesin_id')
            ->where('periode_tipe', 'mingguan')
            ->orderBy('urutan')
            ->get();

        $trenDowntimePerMesin = KpiTrend::whereNotNull('mesin_id')
            ->where('periode_tipe', 'bulanan')
            ->with('mesin')
            ->orderBy('urutan')
            ->get()
            ->groupBy(fn (KpiTrend $trend) => $trend->mesin?->nama ?? 'Tidak diketahui');

        $mesinPerluPerhatian = $mesinList
            ->whereIn('status', ['Perlu Perhatian', 'Dalam Perbaikan'])
            ->take(3);

        return view('supervisor.dashboard', compact(
            'mesinList',
            'oeeRata',
            'availRata',
            'mttrRata',
            'mtbfRata',
            'perluPerhatian',
            'trenAvailability',
            'trenDowntimePerMesin',
            'mesinPerluPerhatian'
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
        $riwayat = $mesin->damageReports()
            ->latest('diselesaikan_pada')
            ->take(5)
            ->get();

        return view('supervisor.detail-mesin', compact('mesin', 'riwayat'));
    }

    public function maintenance()
    {
        $jadwal = MaintenanceSchedule::with(['mesin', 'teknisi'])
            ->orderBy('tanggal')
            ->get();

        return view('supervisor.maintenance', compact('jadwal'));
    }

    public function jadwalTambahForm()
    {
        $mesinList = Mesin::orderBy('nama')->get();
        $teknisiList = User::where('role', 'teknisi')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('supervisor.jadwal-tambah', compact('mesinList', 'teknisiList'));
    }

    public function jadwalSimpan(Request $request)
    {
        $data = $request->validate([
            'mesin_id' => 'required|exists:mesin,id',
            'jenis_pm' => 'required|string|max:255',
            'teknisi_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'interval' => 'required|in:Harian,Mingguan,Bulanan,Tidak Berulang',
            'estimasi_durasi' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:5000',
        ]);

        $teknisiValid = User::whereKey($data['teknisi_id'])
            ->where('role', 'teknisi')
            ->where('is_active', true)
            ->exists();

        if (! $teknisiValid) {
            return back()->withInput()->withErrors([
                'teknisi_id' => 'Teknisi yang dipilih tidak aktif atau tidak valid.',
            ]);
        }

        $tanggal = \Illuminate\Support\Carbon::parse($data['tanggal']);
        $status = $tanggal->isToday() ? 'Jatuh Tempo Hari Ini' : 'Terjadwal';

        MaintenanceSchedule::create([
            ...$data,
            'dibuat_oleh' => $request->user()->id,
            'status' => $status,
        ]);

        return redirect()->route('supervisor.maintenance')
            ->with('success', 'Jadwal maintenance preventif berhasil disimpan.');
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
        $trenKategori = KpiTrend::whereNull('mesin_id')
            ->where('periode_tipe', 'bulanan')
            ->orderBy('urutan')
            ->get();

        $trenDowntimeKumulatif = KpiTrend::whereNull('mesin_id')
            ->where('periode_tipe', 'bulanan')
            ->orderBy('urutan')
            ->get();

        $komponenSering = DamageReport::whereNotNull('komponen_diganti')
            ->where('komponen_diganti', '!=', '')
            ->selectRaw('komponen_diganti, kategori, count(*) as jumlah')
            ->groupBy('komponen_diganti', 'kategori')
            ->orderByDesc('jumlah')
            ->take(3)
            ->get();

        return view('supervisor.laporan', compact(
            'trenKategori',
            'trenDowntimeKumulatif',
            'komponenSering'
        ));
    }

    public function profil()
    {
        return view('supervisor.profil');
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
