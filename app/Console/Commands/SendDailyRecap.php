<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendDailyRecap extends Command
{
    protected $signature = 'send:daily-recap';
    protected $description = 'Send daily attendance summary recap via WhatsApp to Admin 4';

    /**
     * Konversi nama hari dari format 'D' ke nama Indonesia
     */
    private function getNamaHari(string $dayCode): string
    {
        $map = [
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu',
        ];
        return $map[$dayCode] ?? $dayCode;
    }

    /**
     * Cek apakah guru punya jam kerja di tanggal tertentu.
     * Menggunakan logika 3 level yang sama dengan PresensiController::getJamKerja().
     */
    private function getJamKerja(string $nik, string $tanggal, string $kode_dept, string $kode_cabang): ?object
    {
        $namaHari = $this->getNamaHari(date('D', strtotime($tanggal)));

        // Level 1: Jam kerja khusus tanggal tertentu (per orang)
        $jamkerja = DB::table('konfigurasi_jamkerja_by_date')
            ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->first();

        // Level 2: Jam kerja harian perorangan
        if ($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jamkerja')
                ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('hari', $namaHari)
                ->first();
        }

        // Level 3: Jam kerja default departemen/cabang
        if ($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jk_dept_detail')
                ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
                ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('kode_dept', $kode_dept)
                ->where('kode_cabang', $kode_cabang)
                ->where('hari', $namaHari)
                ->first();
        }

        return $jamkerja;
    }

    public function handle()
    {
        $today = date('Y-m-d');

        // A. Cek hari libur sekolah
        $isHoliday = DB::table('harilibur')
            ->where('tanggal_libur', '<=', $today)
            ->where('tanggal_libur_akhir', '>=', $today)
            ->exists();

        if ($isHoliday) {
            $this->info('Hari Libur Sekolah: Pengiriman rekap harian dibatalkan (Libur KBM).');
            return 0;
        }

        // B. Ambil SEMUA guru beserta kode_dept dan kode_cabang
        $semuaGuru = DB::table('karyawan')
            ->select('nik', 'nama_lengkap', 'kode_dept', 'kode_cabang')
            ->get();

        // C. Filter: hanya guru yang punya jam kerja hari ini
        $guruHariIni = $semuaGuru->filter(function ($guru) use ($today) {
            $jamkerja = $this->getJamKerja(
                $guru->nik,
                $today,
                $guru->kode_dept,
                $guru->kode_cabang
            );
            return $jamkerja !== null;
        });

        $totalGuru = $guruHariIni->count();

        if ($totalGuru == 0) {
            $this->info('Tidak ada guru yang memiliki jam kerja hari ini: Pengiriman rekap dibatalkan.');
            return 0;
        }

        $nikHariIni = $guruHariIni->pluck('nik')->toArray();

        // 1. Jumlah hadir (status = 'h') dari guru yang punya jam kerja hari ini
        $hadir = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('status', 'h')
            ->whereIn('nik', $nikHariIni)
            ->count();

        // D. Batalkan jika tidak ada satu pun yang hadir (libur mendadak)
        if ($hadir == 0) {
            $this->info('Tidak ada guru yang absen masuk hari ini: Pengiriman rekap harian dibatalkan (Tidak ada KBM).');
            return 0;
        }

        // 2. Jumlah terlambat (jam_in > jam_masuk)
        $terlambat = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $today)
            ->where('presensi.status', 'h')
            ->whereIn('presensi.nik', $nikHariIni)
            ->whereRaw('presensi.jam_in > jam_kerja.jam_masuk')
            ->count();

        // 3. Jumlah Sakit
        $sakit = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('status', 's')
            ->whereIn('nik', $nikHariIni)
            ->count();

        // 4. Jumlah Izin
        $izin = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('status', 'i')
            ->whereIn('nik', $nikHariIni)
            ->count();

        // 5. Jumlah Cuti
        $cuti = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('status', 'c')
            ->whereIn('nik', $nikHariIni)
            ->count();

        // 6. Jumlah Alpa: guru yang punya jam kerja tapi TIDAK ADA di tabel presensi hari ini
        $nikSudahPresensi = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->whereIn('nik', $nikHariIni)
            ->pluck('nik')
            ->toArray();

        $nikAlpa = array_diff($nikHariIni, $nikSudahPresensi);
        $alpa    = count($nikAlpa);

        // 7. Persentase kehadiran
        $persentase = $totalGuru > 0 ? round(($hadir / $totalGuru) * 100) : 0;

        // 8. Daftar Guru Izin/Sakit/Cuti (dari guru yang punya jam kerja hari ini)
        $leaves = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->where('tgl_presensi', $today)
            ->whereIn('presensi.status', ['s', 'i', 'c'])
            ->whereIn('presensi.nik', $nikHariIni)
            ->select('karyawan.nama_lengkap', 'presensi.status')
            ->get();

        $leaveLines = "";
        if ($leaves->count() > 0) {
            foreach ($leaves as $l) {
                $type = $l->status == 's' ? 'Sakit' : ($l->status == 'i' ? 'Izin' : 'Cuti');
                $leaveLines .= "- " . $l->nama_lengkap . " (" . $type . ")\n";
            }
        } else {
            $leaveLines = "- Tidak ada\n";
        }

        // 9. Daftar Guru Alpa (punya jam kerja tapi tidak ada di presensi sama sekali)
        $alphaLines = "";
        if (count($nikAlpa) > 0) {
            $alphaTeachers = $guruHariIni->whereIn('nik', $nikAlpa);
            foreach ($alphaTeachers as $a) {
                $alphaLines .= "- " . $a->nama_lengkap . "\n";
            }
        } else {
            $alphaLines = "- Tidak ada\n";
        }

        // 10. Ambil nomor HP Admin 4 (fallback ke Admin 1)
        $adminConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admins')->first();
        $targetNumber = null;
        if ($adminConfig) {
            $admins = explode(',', $adminConfig->value);
            if (isset($admins[3]) && !empty(trim($admins[3]))) {
                $targetNumber = trim($admins[3]);
            } elseif (isset($admins[0]) && !empty(trim($admins[0]))) {
                $targetNumber = trim($admins[0]);
            }
        }

        if (empty($targetNumber)) {
            $this->error('Nomor HP Admin tidak ditemukan pada konfigurasi wa_gateway_admins.');
            return 1;
        }

        // 11. Susun nama hari Indonesia
        $hariIndo = $this->getNamaHari(date('D'));

        // 12. Susun pesan WhatsApp
        $message = "*LAPORAN REKAPITULASI HARIAN PRESENSI GURU*\n"
            . "Hari/Tanggal: " . $hariIndo . ", " . date('d-m-Y') . "\n"
            . "Waktu Laporan: 17:00 WIB\n"
            . "--------------------------------------\n\n"
            . "*RINGKASAN KEHADIRAN*\n"
            . "- Guru Bertugas Hari Ini: " . $totalGuru . " Orang\n"
            . "- Hadir: " . $hadir . " Orang (Tepat Waktu: " . ($hadir - $terlambat) . ", Terlambat: " . $terlambat . ")\n"
            . "- Tidak Hadir: " . ($totalGuru - $hadir) . " Orang\n\n"
            . "*RINCIAN TIDAK HADIR*\n"
            . "- Sakit: " . $sakit . " Orang\n"
            . "- Izin: " . $izin . " Orang\n"
            . "- Cuti: " . $cuti . " Orang\n"
            . "- Tanpa Keterangan (Alpa): " . $alpa . " Orang\n\n"
            . "*DAFTAR GURU IZIN/SAKIT/CUTI*\n"
            . $leaveLines . "\n"
            . "*DAFTAR GURU ALPA*\n"
            . $alphaLines . "\n"
            . "*Persentase Kehadiran Hari Ini: " . $persentase . "%*\n\n"
            . "Laporan ini dibuat otomatis oleh sistem absensi SiGuru.";

        // 13. Kirim via sendWaNotification
        try {
            sendWaNotification($targetNumber, $message);
            $this->info('Rekap harian presensi berhasil dikirim ke Admin: ' . $targetNumber);
            return 0;
        } catch (\Exception $e) {
            $this->error('Gagal mengirim WA rekap harian: ' . $e->getMessage());
            return 1;
        }
    }
}