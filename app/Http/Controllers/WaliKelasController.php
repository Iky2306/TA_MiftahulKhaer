<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MasterKelas;
use App\Models\MasterSiswa;
use App\Exports\DataSiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class WaliKelasController extends Controller
{
    /**
     * Halaman Utama Hub Sub-Menu Kelasku (Wali Kelas)
     */
    public function index(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        // Ambil daftar kelas yang diampu oleh wali kelas ini
        $kelasDiampu = MasterKelas::where('wali_kelas_nik', $nik)->get();

        if ($kelasDiampu->isEmpty()) {
            return view('walikelas.not_assigned');
        }

        // Kelas aktif
        $kode_kelas_selected = $request->get('kode_kelas', $kelasDiampu->first()->kode_kelas);
        $kelasAktif = $kelasDiampu->where('kode_kelas', $kode_kelas_selected)->first() ?? $kelasDiampu->first();

        $bulan = date('m');
        $tahun = date('Y');

        // Stats singkat bulan ini
        $totalJurnalBulanIni = DB::table('jurnal_mengajar')
            ->where('kode_kelas', $kelasAktif->kode_kelas)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();

        $totalGuruMengajar = DB::table('jurnal_mengajar')
            ->where('kode_kelas', $kelasAktif->kode_kelas)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->distinct('nik')
            ->count('nik');

        return view('walikelas.index', compact(
            'kelasDiampu',
            'kelasAktif',
            'totalJurnalBulanIni',
            'totalGuruMengajar'
        ));
    }

    /**
     * Halaman Monitoring E-Jurnal Mengajar Kelas
     */
    public function jurnal(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        $kelasDiampu = MasterKelas::where('wali_kelas_nik', $nik)->get();

        if ($kelasDiampu->isEmpty()) {
            return view('walikelas.not_assigned');
        }

        $kode_kelas_selected = $request->get('kode_kelas', $kelasDiampu->first()->kode_kelas);
        $kelasAktif = $kelasDiampu->where('kode_kelas', $kode_kelas_selected)->first() ?? $kelasDiampu->first();

        // Filter Bulan & Tahun (default bulan & tahun saat ini)
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Query Jurnal Mengajar untuk kelas aktif
        $queryJurnal = DB::table('jurnal_mengajar')
            ->join('karyawan', 'jurnal_mengajar.nik', '=', 'karyawan.nik')
            ->select(
                'jurnal_mengajar.*',
                'karyawan.nama_lengkap as nama_guru',
                'karyawan.foto as foto_guru'
            )
            ->where('jurnal_mengajar.kode_kelas', $kelasAktif->kode_kelas)
            ->whereMonth('jurnal_mengajar.tanggal', sprintf('%02d', $bulan))
            ->whereYear('jurnal_mengajar.tanggal', $tahun);

        if ($request->has('nama_mapel') && !empty($request->nama_mapel)) {
            $queryJurnal->where('jurnal_mengajar.nama_mapel', $request->nama_mapel);
        }

        if ($request->has('nik_guru') && !empty($request->nik_guru)) {
            $queryJurnal->where('jurnal_mengajar.nik', $request->nik_guru);
        }

        $jurnals = $queryJurnal->orderBy('jurnal_mengajar.tanggal', 'desc')
            ->orderBy('jurnal_mengajar.jam_mulai', 'desc')
            ->get();

        // Data pendukung untuk filter & statistik
        $daftarMapel = DB::table('jurnal_mengajar')
            ->where('kode_kelas', $kelasAktif->kode_kelas)
            ->distinct()
            ->pluck('nama_mapel');

        $daftarGuru = DB::table('jurnal_mengajar')
            ->join('karyawan', 'jurnal_mengajar.nik', '=', 'karyawan.nik')
            ->where('jurnal_mengajar.kode_kelas', $kelasAktif->kode_kelas)
            ->select('karyawan.nik', 'karyawan.nama_lengkap')
            ->distinct()
            ->get();

        $stats = [
            'total_jurnal' => $jurnals->count(),
            'total_guru' => $jurnals->pluck('nik')->unique()->count(),
            'total_mapel' => $jurnals->pluck('nama_mapel')->unique()->count(),
        ];

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('walikelas.jurnal', compact(
            'kelasDiampu',
            'kelasAktif',
            'jurnals',
            'daftarMapel',
            'daftarGuru',
            'stats',
            'bulan',
            'tahun',
            'namabulan'
        ));
    }

    /**
     * Halaman Submenu Data Siswa Kelas (Wali Kelas)
     */
    public function siswa(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        $kelasDiampu = MasterKelas::where('wali_kelas_nik', $nik)->get();

        if ($kelasDiampu->isEmpty()) {
            return view('walikelas.not_assigned');
        }

        $kode_kelas_selected = $request->get('kode_kelas', $kelasDiampu->first()->kode_kelas);
        $kelasAktif = $kelasDiampu->where('kode_kelas', $kode_kelas_selected)->first() ?? $kelasDiampu->first();

        $querySiswa = MasterSiswa::where('kode_kelas', $kelasAktif->kode_kelas)->where('status', 'aktif');

        if ($request->filled('search')) {
            $querySiswa->where('nama_siswa', 'like', '%' . $request->search . '%');
        }

        $siswas = $querySiswa->orderBy('nama_siswa')->get();

        $namaCabang = DB::table('cabang')
            ->where('kode_cabang', $kelasAktif->kode_cabang)
            ->value('nama_cabang') ?? $kelasAktif->kode_cabang;

        return view('walikelas.siswa', compact(
            'kelasDiampu',
            'kelasAktif',
            'siswas',
            'namaCabang'
        ));
    }

    /**
     * Export PDF Data Siswa Wali Kelas dengan Kop Sekolah resmi
     */
    public function exportSiswaPdf(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $kelasDiampu = MasterKelas::where('wali_kelas_nik', $nik)->get();

        if ($kelasDiampu->isEmpty()) {
            return redirect('/guru/walikelas/monitoring');
        }

        $kode_kelas_selected = $request->get('kode_kelas', $kelasDiampu->first()->kode_kelas);
        $kelasAktif = $kelasDiampu->where('kode_kelas', $kode_kelas_selected)->first() ?? $kelasDiampu->first();

        $siswas = MasterSiswa::where('kode_kelas', $kelasAktif->kode_kelas)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();

        $namaCabang = DB::table('cabang')
            ->where('kode_cabang', $kelasAktif->kode_cabang)
            ->value('nama_cabang') ?? $kelasAktif->kode_cabang;

        $pdf = Pdf::loadView('walikelas.cetaksiswa_pdf', compact('kelasAktif', 'siswas', 'namaCabang'));
        $pdf->setPaper('a4', 'portrait');

        $namaFile = "Data_Siswa_Kelas_" . str_replace(' ', '_', $kelasAktif->nama_kelas) . ".pdf";
        return $pdf->download($namaFile);
    }

    /**
     * Export Excel Data Siswa Wali Kelas (.xlsx)
     */
    public function exportSiswaExcel(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $kelasDiampu = MasterKelas::where('wali_kelas_nik', $nik)->get();

        if ($kelasDiampu->isEmpty()) {
            return redirect('/guru/walikelas/monitoring');
        }

        $kode_kelas_selected = $request->get('kode_kelas', $kelasDiampu->first()->kode_kelas);
        $kelasAktif = $kelasDiampu->where('kode_kelas', $kode_kelas_selected)->first() ?? $kelasDiampu->first();

        $namaFile = "Data_Siswa_Kelas_" . str_replace(' ', '_', $kelasAktif->nama_kelas) . ".xlsx";

        return Excel::download(new DataSiswaExport($kelasAktif->kode_kelas), $namaFile);
    }
}
