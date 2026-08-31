# Integrasi SIMPM dengan SIPPM

SIMPM adalah Project 2 dan bersifat analitis. Data operasional kerusakan dan hasil perbaikan nantinya berasal dari SIPPM (Project 1).

## Prinsip

- SIPPM menjadi sumber data utama untuk laporan kerusakan.
- SIMPM tidak boleh mengubah laporan kerusakan asli milik SIPPM.
- SIMPM menggunakan data laporan untuk menghitung downtime, jumlah perbaikan, MTTR, MTBF, dan availability.
- OEE tidak dihitung dari downtime saja karena membutuhkan data tambahan mengenai performance dan quality.

## Kontrak data minimum

Setiap laporan yang masuk ke SIMPM harus memiliki:

```json
{
  "no_laporan": "LAP-001",
  "mesin_id": 1,
  "kategori": "Mekanik",
  "komponen_diganti": "Bearing",
  "tindakan": "Penggantian bearing",
  "teknisi_nama": "Nama Teknisi",
  "downtime_menit": 120,
  "diselesaikan_pada": "2026-08-31"
}
```

## Alur integrasi yang disarankan

SIPPM API -> proses sinkronisasi -> tabel `damage_reports` -> `KpiCalculatorService` -> dashboard SIMPM.

Dengan struktur ini, kode dashboard tidak perlu diubah ketika sumber data dummy diganti dengan data asli dari SIPPM.
