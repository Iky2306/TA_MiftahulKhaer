<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PiketController extends Controller
{
    /**
     * Helper: Ambil nama hari dalam Bahasa Indonesia
     */
    private function getNamaHari($dayCode)
    {
        $map = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu',
        ];
        return $map[$dayCode] ?? $dayCode;
    }

    /**
     * Cek apakah guru punya jadwal kerja pada hari tertentu.
     * Return: object jam_kerja jika ada jadwal, null jika tidak.
     * Priority: Jadwal personal → Jadwal departemen/cabang
     */
    private function getJadwalHariIni($nik, $kode_dept, $kode_cabang, $namaHari)
    {
        // 1. Cek jadwal personal
        $hasPersonal = DB::table('konfigurasi_jamkerja')
            ->where('nik', $nik)
            ->exists();

        if ($hasPersonal) {
            return DB::table('konfigurasi_jamkerja')
                ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('hari', $namaHari)
                ->first();
        }

        // 2. Fallback: jadwal departemen/cabang
        return DB::table('konfigurasi_jk_dept_detail')
            ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('konfigurasi_jk_dept.kode_dept', $kode_dept)
            ->where('konfigurasi_jk_dept.kode_cabang', $kode_cabang)
            ->where('konfigurasi_jk_dept_detail.hari', $namaHari)
            ->first();
    }

    /**
     * Monitoring Kehadiran Guru Hari Ini
     *
     * - Scoped by kode_cabang petugas piket yang login
     * - Filter: hanya guru yang punya jadwal hari ini
     * - Cek status: hadir / belum hadir / izin / sakit / cuti / libur
     */
    public function monitoring()
    {
        $piket = Auth::guard('karyawan')->user();
        $kode_cabang = $piket->kode_cabang;
        $hariIni = date('Y-m-d');
        $namaHari = $this->getNamaHari(date('D', strtotime($hariIni)));

        $cabang = DB::table('cabang')
            ->where('kode_cabang', $kode_cabang)
            ->first();

        // Cek apakah petugas piket yang sedang login memiliki jadwal piket hari ini
        $jadwalPiketHariIni = $this->getJadwalHariIni($piket->nik, $piket->kode_dept, $piket->kode_cabang, $namaHari);
        $liburPiketHariIni  = cekHariLiburPegawai($piket->nik, $hariIni);
        $piketPunyaJadwal   = ($jadwalPiketHariIni !== null && $liburPiketHariIni === null);

        // Ambil semua guru/karyawan di cabang yang sama (exclude petugas piket)
        $semuaGuru = DB::table('karyawan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('karyawan.*', 'departemen.nama_dept')
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('karyawan.nik', '!=', $piket->nik)
            ->where('karyawan.kode_dept', '!=', 'PPT')
            ->where('karyawan.jabatan', '!=', 'Petugas Piket')
            ->orderBy('karyawan.nama_lengkap')
            ->get();

        // Ambil log WA hari ini (batch query)
        $waLogs = DB::table('piket_wa_log')
            ->where('tanggal', $hariIni)
            ->get()
            ->keyBy('nik_guru');

        // Ambil semua presensi hari ini di cabang (batch query)
        $presensiHariIni = DB::table('presensi')
            ->where('tgl_presensi', $hariIni)
            ->whereIn('nik', $semuaGuru->pluck('nik'))
            ->get()
            ->keyBy('nik');

        // Ambil semua izin yang berlaku hari ini (batch query)
        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tgl_izin_dari', '<=', $hariIni)
            ->whereDate('tgl_izin_sampai', '>=', $hariIni)
            ->where('status_approved', 1)
            ->whereIn('nik', $semuaGuru->pluck('nik'))
            ->get()
            ->keyBy('nik');

        $guruHariIni = collect();

        foreach ($semuaGuru as $guru) {
            // Cek hari libur
            $cekLibur = cekHariLiburPegawai($guru->nik, $hariIni);
            if ($cekLibur) {
                $guru->status = 'libur';
                $guru->keterangan_status = $cekLibur->keterangan ?? 'Hari Libur';
                $guru->jam_in = null;
                $guru->jam_out = null;
                $guru->jam_kerja = null;
                $guru->wa_sudah_dikirim = false;
                $guru->wa_jam_kirim = null;
                // Guru libur tetap ditampilkan tapi tidak perlu di-monitor
                continue;
            }

            // Cek jadwal kerja hari ini
            $jadwal = $this->getJadwalHariIni($guru->nik, $guru->kode_dept, $guru->kode_cabang, $namaHari);
            if (!$jadwal) {
                continue; // Tidak ada jadwal hari ini → skip
            }

            $guru->jam_kerja = $jadwal;
            $guru->jam_in = null;
            $guru->jam_out = null;
            $guru->keterangan_status = null;

            // Cek status kehadiran
            $presensi = $presensiHariIni->get($guru->nik);
            $izin = $izinHariIni->get($guru->nik);

            $isHadir = $presensi && ($presensi->status === 'h' || (empty($presensi->status) && $presensi->jam_in !== null));
            $isIzin  = ($presensi && in_array($presensi->status, ['i', 's', 'c'])) || $izin;

            if ($isHadir) {
                $guru->status = 'hadir';
                $guru->jam_in = $presensi->jam_in;
                $guru->jam_out = $presensi->jam_out;
            } elseif ($isIzin) {
                $guru->status = ($presensi && in_array($presensi->status, ['i', 's', 'c'])) ? $presensi->status : $izin->status;
                $guru->keterangan_status = $izin->keterangan ?? null;
            } else {
                $guru->status = 'belum_hadir';
            }

            // Cek log WA
            $waLog = $waLogs->get($guru->nik);
            $guru->wa_sudah_dikirim = $waLog ? true : false;
            $guru->wa_jam_kirim = $waLog ? date('H:i', strtotime($waLog->created_at)) : null;

            $guruHariIni->push($guru);
        }

        // Hitung ringkasan
        $ringkasan = (object) [
            'total'       => $guruHariIni->count(),
            'hadir'       => $guruHariIni->where('status', 'hadir')->count(),
            'belum_hadir' => $guruHariIni->where('status', 'belum_hadir')->count(),
            'izin'        => $guruHariIni->where('status', 'i')->count(),
            'sakit'       => $guruHariIni->where('status', 's')->count(),
            'cuti'        => $guruHariIni->where('status', 'c')->count(),
        ];

        return view('piket.monitoring', compact(
            'guruHariIni', 'ringkasan', 'hariIni', 'namaHari', 'cabang', 'piketPunyaJadwal'
        ));
    }

    /**
     * AJAX: Data monitoring untuk auto-refresh
     */
    public function monitoringData()
    {
        $piket = Auth::guard('karyawan')->user();
        $kode_cabang = $piket->kode_cabang;
        $hariIni = date('Y-m-d');
        $namaHari = $this->getNamaHari(date('D', strtotime($hariIni)));

        // Cekapakah petugas piket yang sedang login memiliki jadwal piket hari ini
        $jadwalPiketHariIni = $this->getJadwalHariIni($piket->nik, $piket->kode_dept, $piket->kode_cabang, $namaHari);
        $liburPiketHariIni  = cekHariLiburPegawai($piket->nik, $hariIni);
        $piketPunyaJadwal   = ($jadwalPiketHariIni !== null && $liburPiketHariIni === null);

        $semuaGuru = DB::table('karyawan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('karyawan.*', 'departemen.nama_dept')
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('karyawan.nik', '!=', $piket->nik)
            ->where('karyawan.kode_dept', '!=', 'PPT')
            ->where('karyawan.jabatan', '!=', 'Petugas Piket')
            ->orderBy('karyawan.nama_lengkap')
            ->get();

        $waLogs = DB::table('piket_wa_log')
            ->where('tanggal', $hariIni)
            ->get()
            ->keyBy('nik_guru');

        $presensiHariIni = DB::table('presensi')
            ->where('tgl_presensi', $hariIni)
            ->whereIn('nik', $semuaGuru->pluck('nik'))
            ->get()
            ->keyBy('nik');

        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tgl_izin_dari', '<=', $hariIni)
            ->whereDate('tgl_izin_sampai', '>=', $hariIni)
            ->where('status_approved', 1)
            ->whereIn('nik', $semuaGuru->pluck('nik'))
            ->get()
            ->keyBy('nik');

        $result = [];
        $counts = ['hadir' => 0, 'belum_hadir' => 0, 'izin' => 0, 'sakit' => 0, 'cuti' => 0];

        foreach ($semuaGuru as $guru) {
            if (cekHariLiburPegawai($guru->nik, $hariIni)) {
                continue;
            }

            $jadwal = $this->getJadwalHariIni($guru->nik, $guru->kode_dept, $guru->kode_cabang, $namaHari);
            if (!$jadwal) continue;

            $presensi = $presensiHariIni->get($guru->nik);
            $izin = $izinHariIni->get($guru->nik);
            $waLog = $waLogs->get($guru->nik);

            $status = 'belum_hadir';
            $jam_in = null;
            $jam_out = null;
            $keterangan = null;

            $isHadir = $presensi && ($presensi->status === 'h' || (empty($presensi->status) && $presensi->jam_in !== null));
            $isIzin  = ($presensi && in_array($presensi->status, ['i', 's', 'c'])) || $izin;

            if ($isHadir) {
                $status = 'hadir';
                $jam_in = $presensi->jam_in;
                $jam_out = $presensi->jam_out;
                $counts['hadir']++;
            } elseif ($isIzin) {
                $status = ($presensi && in_array($presensi->status, ['i', 's', 'c'])) ? $presensi->status : $izin->status;
                $keterangan = $izin->keterangan ?? null;
                if ($status === 'i') $counts['izin']++;
                elseif ($status === 's') $counts['sakit']++;
                elseif ($status === 'c') $counts['cuti']++;
            } else {
                $counts['belum_hadir']++;
            }

            $result[] = [
                'nik'              => $guru->nik,
                'status'           => $status,
                'jam_in'           => $jam_in,
                'jam_out'          => $jam_out,
                'keterangan'       => $keterangan,
                'wa_sudah_dikirim' => $waLog ? true : false,
                'wa_jam_kirim'     => $waLog ? date('H:i', strtotime($waLog->created_at)) : null,
            ];
        }

        $counts['total'] = count($result);

        return response()->json([
            'guru'               => $result,
            'ringkasan'          => $counts,
            'piket_punya_jadwal' => $piketPunyaJadwal,
        ]);
    }

    /**
     * AJAX: Kirim WA Reminder ke guru yang belum hadir
     */
    public function kirimWaReminder(Request $request)
    {
        $piket = Auth::guard('karyawan')->user();
        $nikGuru = $request->nik_guru;
        $hariIni = date('Y-m-d');
        $namaHari = $this->getNamaHari(date('D', strtotime($hariIni)));

        // Cek apakah petugas piket yang login memiliki jadwal piket hari ini
        $jadwalPiketHariIni = $this->getJadwalHariIni($piket->nik, $piket->kode_dept, $piket->kode_cabang, $namaHari);
        $liburPiketHariIni  = cekHariLiburPegawai($piket->nik, $hariIni);
        $piketPunyaJadwal   = ($jadwalPiketHariIni !== null && $liburPiketHariIni === null);

        if (!$piketPunyaJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal piket hari ini! Anda tidak dapat mengklik atau mengirim pesan konfirmasi WA.',
            ]);
        }

        // Anti-spam: cek apakah sudah dikirim hari ini
        $exists = DB::table('piket_wa_log')
            ->where('nik_guru', $nikGuru)
            ->where('tanggal', $hariIni)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'WA reminder sudah dikirim hari ini untuk guru ini.',
            ]);
        }

        // Ambil data guru
        $guru = DB::table('karyawan')->where('nik', $nikGuru)->first();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
        }

        // Cek nomor HP valid
        $noHpFormatted = formatPhoneNumber($guru->no_hp);
        if (!$noHpFormatted) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP guru tidak valid atau belum dicantumkan.',
            ]);
        }

        $cabang = DB::table('cabang')
            ->where('kode_cabang', $piket->kode_cabang)
            ->first();

        // Compose message
        $tanggalFormatted = date('d-m-Y', strtotime($hariIni));
        $pesan = "Assalamu'alaikum *{$guru->nama_lengkap}*,\n\n"
               . "Ini pesan dari Petugas Piket *{$piket->nama_lengkap}* ({$cabang->nama_cabang}).\n\n"
               . "Kami belum menerima konfirmasi kehadiran Anda hari ini ({$tanggalFormatted}).\n"
               . "Mohon segera konfirmasi kehadiran atau informasikan jika berhalangan.\n\n"
               . "Terima kasih.\n"
               . "_— Siguru Piket System_";

        // Kirim via Fonnte
        $response = sendWaNotification($guru->no_hp, $pesan);

        // Simpan log
        DB::table('piket_wa_log')->insert([
            'nik_piket'  => $piket->nik,
            'nik_guru'   => $nikGuru,
            'tanggal'    => $hariIni,
            'pesan'      => $pesan,
            'status'     => $response ? 'success' : 'failed',
            'response'   => is_string($response) ? $response : null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => (bool) $response,
            'message' => $response
                ? 'WA reminder berhasil dikirim ke ' . $guru->nama_lengkap
                : 'Gagal mengirim WA. Periksa konfigurasi Fonnte atau nomor HP guru.',
            'jam_kirim' => date('H:i'),
        ]);
    }
}
