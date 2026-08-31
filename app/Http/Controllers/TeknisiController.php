<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class TeknisiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $jadwal = MaintenanceSchedule::with('mesin')
            ->where('teknisi_id', $user->id)
            ->where('status', '!=', 'Selesai')
            ->orderBy('tanggal')
            ->get();

        $stat = [
            'minggu_ini' => MaintenanceSchedule::where('teknisi_id', $user->id)
                ->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'jatuh_tempo' => $jadwal->filter(fn (MaintenanceSchedule $schedule) => $schedule->tanggal->isToday())->count(),
            'selesai_bulan_ini' => MaintenanceSchedule::where('teknisi_id', $user->id)
                ->where('status', 'Selesai')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
        ];

        return view('teknisi.dashboard', compact('jadwal', 'stat'));
    }

    public function detailJadwal(MaintenanceSchedule $schedule)
    {
        $this->authorizeTeknisi($schedule);
        $schedule->load('checklist', 'mesin');

        return view('teknisi.detail-jadwal', compact('schedule'));
    }

    public function toggleChecklist(Request $request, MaintenanceChecklist $item)
    {
        $this->authorizeTeknisi($item->schedule);

        $item->update(['is_done' => ! $item->is_done]);
        $progres = $item->schedule->progresChecklist();

        return response()->json([
            'ok' => true,
            'is_done' => $item->is_done,
            'progres' => $progres,
        ]);
    }

    public function tandaiSelesai(Request $request, MaintenanceSchedule $schedule)
    {
        $this->authorizeTeknisi($schedule);
        $schedule->load('checklist', 'mesin');

        $progress = $schedule->progresChecklist();
        if ($progress['total'] > 0 && $progress['selesai'] < $progress['total']) {
            return back()->with('error', 'Semua checklist harus diselesaikan sebelum maintenance ditandai selesai.');
        }

        $schedule->update(['status' => 'Selesai']);

        return redirect()->route('teknisi.dashboard')
            ->with('success', "Maintenance {$schedule->mesin->nama} ditandai selesai.");
    }

    public function riwayat(Request $request)
    {
        $riwayat = DamageReport::with('mesin')
            ->where('teknisi_nama', $request->user()->name)
            ->latest('diselesaikan_pada')
            ->get();

        return view('teknisi.riwayat', compact('riwayat'));
    }

    public function performa(Request $request)
    {
        $user = $request->user();
        $laporanSaya = DamageReport::where('teknisi_nama', $user->name);
        $rataDowntime = $laporanSaya->avg('downtime_menit');

        $stat = [
            'rata_waktu' => $rataDowntime === null ? 0 : round($rataDowntime / 60, 1),
            'selesai_3bulan' => (clone $laporanSaya)
                ->where('diselesaikan_pada', '>=', now()->subMonths(3))
                ->count(),
            'mesin_sering' => (clone $laporanSaya)->distinct()->count('mesin_id'),
        ];

        return view('teknisi.performa', compact('stat'));
    }

    public function profil()
    {
        return view('teknisi.profil');
    }

    public function profilUpdate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:30',
            'sub_role' => 'nullable|string|max:100',
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    private function authorizeTeknisi(MaintenanceSchedule $schedule): void
    {
        abort_unless($schedule->teknisi_id === auth()->id(), 403);
    }
}
