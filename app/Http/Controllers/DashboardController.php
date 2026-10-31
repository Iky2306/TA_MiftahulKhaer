<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\RankingService;

class DashboardController extends Controller
{
    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; //1 atau Januari
        $tahunini = date("Y"); // 2023
        $nik = Auth::guard('karyawan')->user()->nik;
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;

        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $presensihariini = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="c",1,0)) as jmlcuti,
            0 as jmlterlambat

            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();


        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();

        // Read active theme
        $configTema = DB::table('konfigurasi_app')->where('key', 'tema_dashboard')->first();
        $tema = $configTema ? $configTema->value : 'default';


        // Fetch Active Announcements
        $pengumuman = DB::table('pengumuman')
            ->where('status', 1)
            ->orderBy('tgl_pengumuman', 'desc')
            ->get();

        // Fetch Top Ranking Teachers & User Specific Rank
        $rankingData = $this->rankingService->getLeaderboardWithUserRank($nik, 10);
        $topTeachers = $rankingData['topTeachers'];
        $userRank = $rankingData['userRank'];
        $isPeriodActive = $rankingData['isPeriodActive'] ?? true;
        $periodMessage = $rankingData['periodMessage'] ?? '';

        // Tipe Jabatan untuk menu dinamis
        $kode_dept_auth = Auth::guard('karyawan')->user()->kode_dept;
        $tipe_jabatan = 'guru';
        if ($kode_dept_auth === 'PPT') {
            $tipe_jabatan = 'petugas_piket';
        } elseif ($kode_dept_auth === '2') {
            $tipe_jabatan = 'wali_kelas';
        }

        // Ringkasan piket untuk dashboard (hanya jika petugas piket)
        $ringkasanPiket = null;
        $topPiket = collect();
        $piketUserRank = null;
        if ($tipe_jabatan === 'petugas_piket') {
            $namaHari = $this->getNamaHari(date('D', strtotime($hariini)));

            // Ambil semua guru/karyawan di cabang yang sama (exclude petugas piket)
            $semuaGuru = DB::table('karyawan')
                ->where('kode_cabang', $kode_cabang)
                ->where('nik', '!=', $nik)
                ->where('kode_dept', '!=', 'PPT')
                ->where('jabatan', '!=', 'Petugas Piket')
                ->get();

            $presensiHariIni = DB::table('presensi')
                ->where('tgl_presensi', $hariini)
                ->whereIn('nik', $semuaGuru->pluck('nik'))
                ->get()
                ->keyBy('nik');

            $izinHariIni = DB::table('pengajuan_izin')
                ->whereDate('tgl_izin_dari', '<=', $hariini)
                ->whereDate('tgl_izin_sampai', '>=', $hariini)
                ->where('status_approved', 1)
                ->whereIn('nik', $semuaGuru->pluck('nik'))
                ->get()
                ->keyBy('nik');

            $countTotal = 0;
            $countHadir = 0;
            $countBelum = 0;

            foreach ($semuaGuru as $guru) {
                // Cek hari libur
                if (cekHariLiburPegawai($guru->nik, $hariini)) {
                    continue;
                }

                // Cek jadwal kerja hari ini
                $jadwal = $this->getJadwalHariIni($guru->nik, $guru->kode_dept, $guru->kode_cabang, $namaHari);
                if (!$jadwal) {
                    continue; // Tidak ada jadwal hari ini → skip
                }

                $countTotal++;

                // Cek status kehadiran
                $presensi = $presensiHariIni->get($guru->nik);
                $izin = $izinHariIni->get($guru->nik);

                if ($presensi) {
                    $countHadir++;
                } elseif ($izin) {
                    // Izin/Sakit/Cuti tidak terhitung "belum hadir"
                } else {
                    $countBelum++;
                }
            }

            $ringkasanPiket = (object) [
                'total' => $countTotal,
                'hadir' => $countHadir,
                'belum' => $countBelum,
            ];

            // Fetch Piket Leaderboard
            $piketRankingData = $this->rankingService->getPiketLeaderboardWithUserRank($nik, 10);
            $topPiket = $piketRankingData['topPiket'];
            $piketUserRank = $piketRankingData['userRank'];
        }

        // Check if current user is Wali Kelas
        $kelasWali = DB::table('master_kelas')->where('wali_kelas_nik', $nik)->get();
        $isWaliKelas = $kelasWali->isNotEmpty();

        return view('dashboard.dashboard', compact('presensihariini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'cabang', 'departemen', 'tema', 'pengumuman', 'topTeachers', 'userRank', 'tipe_jabatan', 'ringkasanPiket', 'topPiket', 'piketUserRank', 'isPeriodActive', 'periodMessage', 'isWaliKelas', 'kelasWali'));
    }

    public function dashboardadmin()
    {


        $hariini = date("Y-m-d");

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="c",1,0)) as jmlcuti,
            0 as jmlterlambat

            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $hariini)
            ->first();

        // Fetch Top Ranking Teachers & Period Status
        $rankingData = $this->rankingService->getLeaderboardWithUserRank('ADMIN', 10);
        $topTeachers = $rankingData['topTeachers'];
        $isPeriodActive = $rankingData['isPeriodActive'] ?? true;
        $periodMessage = $rankingData['periodMessage'] ?? '';

        // Fetch Top Ranking Piket
        $topPiket = $this->rankingService->getTopPiket(10);

        return view('dashboard.dashboardadmin', compact('rekappresensi', 'topTeachers', 'topPiket', 'isPeriodActive', 'periodMessage'));
    }

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
}
