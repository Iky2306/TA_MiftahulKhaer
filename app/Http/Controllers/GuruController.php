<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setjamkerja;
use App\Models\Setjamkerjadept;
use App\Models\Setjamkerjadept_detail;
use App\Models\Karyawan;
use App\Models\Jamkerja;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class GuruController extends Controller
{
    public function jadwalMengajar()
    {
        // Ambil user yang login
        $user = Auth::guard('karyawan')->user();
        
        // Dapatkan data lengkap karyawan
        $karyawan = Karyawan::where('nik', $user->nik)
                    ->select('nik', 'kode_dept', 'kode_cabang')
                    ->first();

        // Daftar hari lengkap dalam urutan yang benar
        $harikerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        if (!$karyawan) {
            return view('guru.jadwal_mengajar', [
                'hariMengajar' => $harikerja,
                'source' => null,
                'jamKerja' => []
            ]);
        }

        $nik = $karyawan->nik;
        $kode_dept = $karyawan->kode_dept;
        $kode_cabang = $karyawan->kode_cabang;

        // Array untuk menyimpan semua hari
        $hariMengajar = $harikerja;
        $source = null;
        $jamKerja = [];

        // Cek apakah ada setting jam kerja per individu
        $settingIndividu = Setjamkerja::where('nik', $nik)->get();

        if ($settingIndividu->isNotEmpty()) {
            $source = 'individu';
            
            // Ambil detail jam kerja untuk semua hari
            foreach ($harikerja as $hari) {
                $setting = $settingIndividu->where('hari', $hari)->first();
                $jamKerja[$hari] = $setting ? Jamkerja::where('kode_jam_kerja', $setting->kode_jam_kerja)->first() : null;
            }
        } else {
            // Jika tidak ada, ambil setting per departemen
            $konfigurasi_dept = Setjamkerjadept::where('kode_cabang', $kode_cabang)
                                ->where('kode_dept', $kode_dept)
                                ->first();

            if ($konfigurasi_dept) {
                $detailDept = Setjamkerjadept_detail::where('kode_jk_dept', $konfigurasi_dept->kode_jk_dept)->get();
                $source = 'departemen';
                
                // Ambil detail jam kerja untuk semua hari
                foreach ($harikerja as $hari) {
                    $detail = $detailDept->where('hari', $hari)->first();
                    $jamKerja[$hari] = $detail ? Jamkerja::where('kode_jam_kerja', $detail->kode_jam_kerja)->first() : null;
                }
            } else {
                // Jika tidak ada konfigurasi sama sekali
                foreach ($harikerja as $hari) {
                    $jamKerja[$hari] = null;
                }
            }
        }

        return view('guru.jadwal_mengajar', [
            'hariMengajar' => $hariMengajar,
            'source' => $source,
            'jamKerja' => $jamKerja
        ]);
    }

    public function statusProfil()
    {
        $user = Auth::guard('karyawan')->user();
        $karyawan = DB::table('karyawan')->where('nik', $user->nik)->first();
        $departemen = DB::table('departemen')->where('kode_dept', $karyawan->kode_dept)->first();
        $cabang = DB::table('cabang')->where('kode_cabang', $karyawan->kode_cabang)->first();

        $harikerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $hariMengajar = $harikerja;
        $source = null;
        $jamKerja = [];
        $totalHariAktif = 0;

        $settingIndividu = Setjamkerja::where('nik', $karyawan->nik)->get();

        if ($settingIndividu->isNotEmpty()) {
            $source = 'individu';
            foreach ($harikerja as $hari) {
                $setting = $settingIndividu->where('hari', $hari)->first();
                $jk = $setting ? Jamkerja::where('kode_jam_kerja', $setting->kode_jam_kerja)->first() : null;
                $jamKerja[$hari] = $jk;
                if ($jk) {
                    $totalHariAktif++;
                }
            }
        } else {
            $konfigurasi_dept = Setjamkerjadept::where('kode_cabang', $karyawan->kode_cabang)
                                ->where('kode_dept', $karyawan->kode_dept)
                                ->first();

            if ($konfigurasi_dept) {
                $detailDept = Setjamkerjadept_detail::where('kode_jk_dept', $konfigurasi_dept->kode_jk_dept)->get();
                $source = 'departemen';
                foreach ($harikerja as $hari) {
                    $detail = $detailDept->where('hari', $hari)->first();
                    $jk = $detail ? Jamkerja::where('kode_jam_kerja', $detail->kode_jam_kerja)->first() : null;
                    $jamKerja[$hari] = $jk;
                    if ($jk) {
                        $totalHariAktif++;
                    }
                }
            } else {
                foreach ($harikerja as $hari) {
                    $jamKerja[$hari] = null;
                }
            }
        }

        // Ambil total jam mengajar dari data Payroll (tabel guru)
        $dataGuruPayroll = DB::table('guru')->where('karyawan_nik', $karyawan->nik)->first();
        $totalJamMengajar = $dataGuruPayroll ? $dataGuruPayroll->jam_mengajar : 0;

        return view('guru.status_profil', compact('karyawan', 'departemen', 'cabang', 'hariMengajar', 'source', 'jamKerja', 'totalHariAktif', 'totalJamMengajar'));
    }

    public function updateMapel(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|max:100'
        ], [
            'jabatan.required' => 'Data Guru Mapel tidak boleh kosong',
            'jabatan.max' => 'Data Guru Mapel maksimal 100 karakter'
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;

        try {
            DB::table('karyawan')->where('nik', $nik)->update([
                'jabatan' => $request->jabatan
            ]);

            return redirect()->back()->with(['success' => 'Data Guru Mapel Berhasil Diperbarui']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Gagal Diperbarui']);
        }
    }
}