<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JurnalMengajar;
use App\Models\MasterKelas;
use App\Models\MasterMapelKelas;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dari_tanggal = $request->dari_tanggal ?? date('Y-m-01');
        $sampai_tanggal = $request->sampai_tanggal ?? date('Y-m-d');

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        // Get Jurnal Mengajar
        $jurnal = DB::table('jurnal_mengajar')
            ->select('jurnal_mengajar.*', 'master_kelas.nama_kelas')
            ->leftJoin('master_kelas', 'jurnal_mengajar.kode_kelas', '=', 'master_kelas.kode_kelas')
            ->where('jurnal_mengajar.nik', $nik)
            ->whereBetween('jurnal_mengajar.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'desc')
            ->get();

        return view('jurnal.index', compact('dari_tanggal', 'sampai_tanggal', 'namabulan', 'karyawan', 'jurnal'));
    }

    private function checkAksesJurnal()
    {
        $user = Auth::guard('karyawan')->user();
        $nik = $user->nik;
        $tgl_hari_ini = date('Y-m-d');
        $namahari = $this->gethari(date('D', strtotime($tgl_hari_ini)));

        // 1. Cek Absen Masuk Hari Ini
        $presensi_hari_ini = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tgl_presensi', $tgl_hari_ini)
            ->whereNotNull('jam_in')
            ->first();

        if (!$presensi_hari_ini) {
            return [
                'status'  => false,
                'message' => 'Anda belum melakukan Absen Masuk hari ini. Silakan melakukan presensi/absen masuk terlebih dahulu sebelum mengisi E-Jurnal.'
            ];
        }

        // 2. Cek Jadwal Mengajar / Jam Kerja Hari Ini
        // a. Cek Jam Kerja By Date
        $jamkerja = DB::table('konfigurasi_jamkerja_by_date')
            ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->where('tanggal', $tgl_hari_ini)
            ->first();

        if (!$jamkerja) {
            // b. Cek Jam Kerja Harian Per-Orang
            $jamkerja = DB::table('konfigurasi_jamkerja')
                ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('hari', $namahari)
                ->first();

            if (!$jamkerja) {
                // c. Cek Jam Kerja Departemen Detail
                $jamkerja = DB::table('konfigurasi_jk_dept_detail')
                    ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
                    ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('kode_dept', $user->kode_dept)
                    ->where('kode_cabang', $user->kode_cabang)
                    ->where('hari', $namahari)
                    ->first();
            }
        }

        if (!$jamkerja) {
            return [
                'status'  => false,
                'message' => 'Anda tidak memiliki jadwal mengajar pada hari ' . $namahari . '. E-Jurnal hanya dapat diisi ketika ada jadwal mengajar.'
            ];
        }

        return [
            'status'  => true,
            'message' => 'OK'
        ];
    }

    private function gethari($hari)
    {
        switch ($hari) {
            case 'Sun': return "Minggu";
            case 'Mon': return "Senin";
            case 'Tue': return "Selasa";
            case 'Wed': return "Rabu";
            case 'Thu': return "Kamis";
            case 'Fri': return "Jumat";
            case 'Sat': return "Sabtu";
            default: return "";
        }
    }

    public function create()
    {
        $akses = $this->checkAksesJurnal();
        if (!$akses['status']) {
            return redirect('/guru/jurnal')->with(['error' => $akses['message']]);
        }

        return view('jurnal.create');
    }

    public function scanProcess(Request $request)
    {
        $akses = $this->checkAksesJurnal();
        if (!$akses['status']) {
            return redirect('/guru/jurnal')->with(['error' => $akses['message']]);
        }

        $token = $request->qr_token;
        if (!$token) {
            return redirect('/guru/jurnal')->with(['error' => 'QR Code Tidak Valid.']);
        }

        $kelas = MasterKelas::where('qr_token', $token)->first();

        if (!$kelas) {
            return redirect('/guru/jurnal')->with(['error' => 'QR Code Kelas Tidak Dikenali / Salah Lokasi. Pindai QR yang Resmi.']);
        }

        // Ambil mapel untuk dropdown
        $mapels = MasterMapelKelas::where('kode_kelas', $kelas->kode_kelas)->get();

        return view('jurnal.form_qr', compact('kelas', 'mapels'));
    }

    public function show($id)
    {
        $jurnal = JurnalMengajar::with('master_kelas')->findOrFail($id);
        if ($jurnal->nik != Auth::guard('karyawan')->user()->nik) {
            return redirect('/guru/jurnal')->with(['error' => 'Akses ditolak']);
        }

        return view('jurnal.show', compact('jurnal'));
    }

    public function store(Request $request)
    {
        $akses = $this->checkAksesJurnal();
        if (!$akses['status']) {
            return redirect('/guru/jurnal')->with(['error' => $akses['message']]);
        }

        $request->validate([
            'kode_kelas' => 'required',
            'nama_mapel' => 'required',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required',
            'materi'     => 'required',
            'foto'       => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;
        $tanggal = date('Y-m-d');

        // Validasi Anti Duplikat Scan
        $cek_duplikat = JurnalMengajar::where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->where('kode_kelas', $request->kode_kelas)
            ->where('nama_mapel', $request->nama_mapel)
            ->first();

        if ($cek_duplikat) {
            return redirect('/guru/jurnal')->with(['error' => 'Anda sudah menyimpan jurnal untuk Mapel ini di ruangan ini hari ini.']);
        }

        $data = [
            'nik'         => $nik,
            'tanggal'     => $tanggal,
            'kode_kelas'  => $request->kode_kelas,
            'nama_mapel'  => $request->nama_mapel,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'materi'      => $request->materi,
        ];

        if ($request->hasFile('foto')) {
            $foto = $nik . "-" . time() . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "public/uploads/jurnal/";
            $request->file('foto')->storeAs($folderPath, $foto);
            $data['foto'] = $foto;
        }

        try {
            JurnalMengajar::create($data);
            return redirect('/guru/jurnal')->with(['success' => 'Jurnal Berhasil Disimpan & Dikunci.']);
        } catch (\Exception $e) {
            return redirect('/guru/jurnal')->with(['error' => 'Gagal menyimpan Jurnal: ' . $e->getMessage()]);
        }
    }

    public function cetakPdf(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dari_tanggal = $request->dari_tanggal ?? date('Y-m-01');
        $sampai_tanggal = $request->sampai_tanggal ?? date('Y-m-d');

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                     "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $jurnal = DB::table('jurnal_mengajar')
            ->select('jurnal_mengajar.*', 'master_kelas.nama_kelas')
            ->leftJoin('master_kelas', 'jurnal_mengajar.kode_kelas', '=', 'master_kelas.kode_kelas')
            ->where('jurnal_mengajar.nik', $nik)
            ->whereBetween('jurnal_mengajar.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal')->orderBy('jam_mulai')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('presensi.cetaklaporanjurnal_pdf', compact(
            'dari_tanggal', 'sampai_tanggal', 'namabulan', 'karyawan', 'jurnal'
        ));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Jurnal_{$karyawan->nama_lengkap}_{$dari_tanggal}_sampai_{$sampai_tanggal}.pdf");
    }
}
