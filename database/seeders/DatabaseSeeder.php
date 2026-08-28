<?php

namespace Database\Seeders;

use App\Models\DamageReport;
use App\Models\KpiTrend;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceSchedule;
use App\Models\Mesin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $supervisor = User::create([
            'name' => 'Sri Handayani', 'username' => 'sri.supervisor',
            'password' => Hash::make('password'), 'role' => 'supervisor',
            'sub_role' => 'Produksi — Gilingan', 'no_hp' => '0813-2244-5566',
        ]);

        $manajer = User::create([
            'name' => 'Ir. Wahyu Prasetyo, M.T.', 'username' => 'wahyu.manajer',
            'password' => Hash::make('password'), 'role' => 'manajer',
            'sub_role' => 'Manajer Produksi', 'no_hp' => '0811-3344-5567',
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso', 'username' => 'budi.teknisi',
            'password' => Hash::make('password'), 'role' => 'teknisi',
            'sub_role' => 'Mekanik', 'no_hp' => '0814-9988-7766',
        ]);

        $rudi = User::create([
            'name' => 'Rudi Hartono', 'username' => 'rudi.teknisi',
            'password' => Hash::make('password'), 'role' => 'teknisi',
            'sub_role' => 'Elektrik', 'no_hp' => '0815-1122-3344',
        ]);

        // ===== MESIN (dengan snapshot KPI persis mockup) =====
        $g1 = Mesin::create(['nama' => 'Gilingan 01', 'oee' => 83, 'availability' => 94, 'mttr_jam' => 1.8, 'mtbf_jam' => 40, 'downtime_bulan_ini_jam' => 4.0, 'jumlah_perbaikan_bulan_ini' => 2, 'status' => 'Normal']);
        $g2 = Mesin::create(['nama' => 'Gilingan 02', 'oee' => 58, 'availability' => 71, 'mttr_jam' => 2.9, 'mtbf_jam' => 18, 'downtime_bulan_ini_jam' => 10.2, 'jumlah_perbaikan_bulan_ini' => 5, 'status' => 'Perlu Perhatian']);
        $g3 = Mesin::create(['nama' => 'Gilingan 03', 'oee' => 88, 'availability' => 96, 'mttr_jam' => 1.5, 'mtbf_jam' => 52, 'downtime_bulan_ini_jam' => 2.5, 'jumlah_perbaikan_bulan_ini' => 1, 'status' => 'Normal']);
        $g4 = Mesin::create(['nama' => 'Gilingan 04', 'oee' => 69, 'availability' => 80, 'mttr_jam' => 2.4, 'mtbf_jam' => 22, 'downtime_bulan_ini_jam' => 7.5, 'jumlah_perbaikan_bulan_ini' => 3, 'status' => 'Dalam Perbaikan']);

        // ===== DAMAGE REPORTS (sinkron dari SIPPM) =====
        $reports = [
            ['BR-2026-006', $g2, 'Mekanik', 'Baut sambungan roll gilingan', 'Pengencangan baut', 'Budi Santoso', 60, '2026-07-30'],
            ['BR-2026-008', $g1, 'Mekanik', 'Bearing poros utama', 'Penggantian bearing', 'Budi Santoso', 120, '2026-08-05'],
            ['BR-2026-010', $g2, 'Elektrik', 'Kontaktor motor utama', 'Penggantian kontaktor', 'Rudi Hartono', 150, '2026-08-06'],
            ['BR-2026-011', $g2, 'Instrumentasi', 'Sensor getaran', 'Pengencangan sensor', 'Budi Santoso', 40, '2026-08-10'],
            ['BR-2026-013', $g1, 'Elektrik', 'Kabel motor penggerak', 'Penggantian kabel', 'Rudi Hartono', 70, '2026-08-13'],
            ['BR-2026-014', $g2, 'Mekanik', 'Bearing 6205 sisi kanan poros', 'Penggantian bearing', 'Budi Santoso', 95, '2026-08-14'],
        ];
        foreach ($reports as [$no, $mesin, $kategori, $komponen, $tindakan, $teknisi, $downtime, $tanggal]) {
            DamageReport::create([
                'no_laporan' => $no, 'mesin_id' => $mesin->id, 'kategori' => $kategori,
                'komponen_diganti' => $komponen, 'tindakan' => $tindakan, 'teknisi_nama' => $teknisi,
                'downtime_menit' => $downtime, 'diselesaikan_pada' => $tanggal,
            ]);
        }

        // ===== JADWAL PM =====
        $s1 = MaintenanceSchedule::create(['mesin_id' => $g2->id, 'teknisi_id' => $budi->id, 'dibuat_oleh' => $supervisor->id, 'jenis_pm' => 'Pemeriksaan gearbox & getaran', 'interval' => 'Mingguan', 'tanggal' => now()->toDateString(), 'estimasi_durasi' => '45 menit', 'status' => 'Jatuh Tempo Hari Ini']);
        MaintenanceSchedule::create(['mesin_id' => $g1->id, 'teknisi_id' => $budi->id, 'dibuat_oleh' => $supervisor->id, 'jenis_pm' => 'Pelumasan bearing', 'interval' => 'Mingguan', 'tanggal' => '2026-08-20', 'estimasi_durasi' => '30 menit', 'status' => 'Terjadwal']);
        MaintenanceSchedule::create(['mesin_id' => $g3->id, 'teknisi_id' => $budi->id, 'dibuat_oleh' => $supervisor->id, 'jenis_pm' => 'Pemeriksaan panel & kontaktor', 'interval' => 'Bulanan', 'tanggal' => '2026-08-25', 'estimasi_durasi' => '60 menit', 'status' => 'Terjadwal']);
        MaintenanceSchedule::create(['mesin_id' => $g4->id, 'teknisi_id' => $rudi->id, 'dibuat_oleh' => $supervisor->id, 'jenis_pm' => 'Kalibrasi sensor instrumentasi', 'interval' => 'Bulanan', 'tanggal' => '2026-08-15', 'estimasi_durasi' => '40 menit', 'status' => 'Selesai']);

        // Checklist untuk jadwal yang jatuh tempo hari ini
        $checklistItems = [
            ['Periksa level oli gearbox', true],
            ['Periksa suara & getaran abnormal', true],
            ['Periksa kekencangan baut dudukan', false],
            ['Catat suhu operasional gearbox', false],
        ];
        foreach ($checklistItems as $i => [$item, $done]) {
            MaintenanceChecklist::create(['schedule_id' => $s1->id, 'item' => $item, 'is_done' => $done, 'urutan' => $i]);
        }

        // ===== KPI TRENDS (data seri untuk grafik — fiktif tapi konsisten) =====
        // Tren availability mingguan (plant-wide)
        $mingguan = [82, 70, 94, 58, 46, 54]; // dikonversi ke label persen di view sbg availability
        foreach (['Mgg 1','Mgg 2','Mgg 3','Mgg 4','Mgg 5','Mgg 6'] as $i => $label) {
            KpiTrend::create(['mesin_id' => null, 'periode_tipe' => 'mingguan', 'label_periode' => $label, 'urutan' => $i, 'availability' => $mingguan[$i]]);
        }

        // Tren downtime bulanan per mesin (6 bulan)
        $bulan = ['Mar','Apr','Mei','Jun','Jul','Agu'];
        $downtimePerMesin = [
            'Gilingan 01' => [3.5, 4.6, 3.4, 5.6, 4.6, 5.7],
            'Gilingan 02' => [5.9, 4.6, 7.4, 5.0, 9.0, 7.6],
            'Gilingan 03' => [2.9, 3.7, 3.1, 4.5, 3.7, 5.3],
            'Gilingan 04' => [1.8, 2.6, 2.1, 3.4, 2.6, 3.9],
        ];
        $mesinMap = ['Gilingan 01' => $g1, 'Gilingan 02' => $g2, 'Gilingan 03' => $g3, 'Gilingan 04' => $g4];
        foreach ($downtimePerMesin as $nama => $vals) {
            foreach ($bulan as $i => $label) {
                KpiTrend::create(['mesin_id' => $mesinMap[$nama]->id, 'periode_tipe' => 'bulanan', 'label_periode' => $label, 'urutan' => $i, 'downtime_jam' => $vals[$i]]);
            }
        }

        // Tren jumlah perbaikan per kategori per bulan (plant-wide) + downtime kumulatif
        $kategori = [
            'mekanik' => [5, 4, 4, 3, 4, 3],
            'elektrik' => [2, 4, 3, 3, 3, 3],
            'instrumentasi' => [2, 1, 2, 2, 2, 2],
        ];
        $kumulatif = [6.1, 10.9, 15.9, 22.9, 27.6, 34.4];
        foreach ($bulan as $i => $label) {
            KpiTrend::create([
                'mesin_id' => null, 'periode_tipe' => 'bulanan', 'label_periode' => $label, 'urutan' => $i,
                'perbaikan_mekanik' => $kategori['mekanik'][$i],
                'perbaikan_elektrik' => $kategori['elektrik'][$i],
                'perbaikan_instrumentasi' => $kategori['instrumentasi'][$i],
                'downtime_jam' => $kumulatif[$i],
            ]);
        }
    }
}
