<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                 "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    // Default nilai bulan dan tahun saat pertama load
    $bulanini = $request->bulan ?? date('m');
    $tahunini = $request->tahun ?? date('Y');

    // Validasi jika form disubmit
    if ($request->has('submit')) {
        if (empty($request->bulan) || empty($request->tahun)) {
            return back()->with('error', 'Harap pilih bulan dan tahun terlebih dahulu');
        }
        $bulanini = $request->bulan;
        $tahunini = $request->tahun;
    }

    $nik = Auth::guard('karyawan')->user()->nik;
    
    // Data karyawan
    $karyawan = DB::table('karyawan')
        ->where('nik', $nik)
        ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        ->first();

    // Data presensi (hambil jika bulan dan tahun ada)
    $presensi = [];
    if ($karyawan && !empty($bulanini) && !empty($tahunini)) {
        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->orderBy('tgl_presensi')
            ->get();
    }

    return view('guru.laporanguru', [
        'namabulan' => $namabulan,
        'bulanini' => $bulanini,
        'tahunini' => $tahunini,
        'karyawan' => $karyawan,
        'presensi' => $presensi
    ]);
}

    public function cetak(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric'
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                     "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Data karyawan
        $karyawan = DB::table('karyawan')
            ->where('nik', $nik)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan');
        }

        // Data presensi
        $presensi = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang', 'master_cuti.nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        return view('guru.cetaklaporanguru', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }
}