<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class KonfirmasiKehadiranController extends Controller
{
    // ─── Helper: sama persis dengan PresensiController ─────────────────────
    private function getNamaHari($dayCode)
    {
        $map = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu',
        ];
        return $map[$dayCode] ?? $dayCode;
    }

    /**
     * Ambil jam kerja REGULER guru (per hari — bukan date-specific override).
     * Untuk konfirmasi kehadiran, kita hanya cek jadwal mingguan reguler.
     * Priority: Personal schedule → Dept/Cabang default
     */
    private function getJamKerja($nik, $tanggal, $kode_dept, $kode_cabang)
    {
        $namahari = $this->getNamaHari(date('D', strtotime($tanggal)));

        // 1. Cek apakah guru punya jadwal PERSONAL (hari apapun)
        $cekJadwalPersonal = DB::table('konfigurasi_jamkerja')
            ->where('nik', $nik)
            ->exists();

        if ($cekJadwalPersonal) {
            // Jika punya jadwal personal, HANYA ambil dari jadwal personal
            // Jika hari yang dipilih tidak ada di jadwal personal, return null (tidak boleh fallback)
            return DB::table('konfigurasi_jamkerja')
                ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('hari', $namahari)
                ->first();
        }

        // 2. Jika TIDAK punya jadwal personal sama sekali, baru pakai default Dept/Cabang
        return DB::table('konfigurasi_jk_dept_detail')
            ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_dept', $kode_dept)
            ->where('kode_cabang', $kode_cabang)
            ->where('hari', $namahari)
            ->first();
    }

    /**
     * Kembalikan daftar hari kerja reguler guru (untuk ditampilkan di form).
     */
    private function getHariKerja($nik, $kode_dept, $kode_cabang)
    {
        // Ambil hari dari jadwal personal
        $personal = DB::table('konfigurasi_jamkerja')
            ->where('nik', $nik)
            ->pluck('hari')
            ->unique()->values()
            ->toArray();

        if (!empty($personal)) {
            return $personal;
        }

        // Jika tidak ada personal, ambil dari dept/cabang default
        return DB::table('konfigurasi_jk_dept_detail')
            ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
            ->where('kode_dept', $kode_dept)
            ->where('kode_cabang', $kode_cabang)
            ->pluck('hari')
            ->unique()->values()
            ->toArray();
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  GURU – Form pengajuan
    // ═══════════════════════════════════════════════════════════════════════

    public function create()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $hari_kerja = $this->getHariKerja($nik, $karyawan->kode_dept, $karyawan->kode_cabang);
        
        return view('konfirmasikehadiran.create', compact('hari_kerja'));
    }

    /**
     * AJAX: cek tanggal yang dipilih guru
     * Diubah: tanggal hari ini (today) diperbolehkan untuk pengajuan konfirmasi
     */
    public function cekTanggal(Request $request)
    {
        $tanggal = $request->tanggal;
        $nik = trim(Auth::guard('karyawan')->user()->nik);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        // Validasi: tanggal tidak boleh masa depan (lebih dari hari ini)
        // Hari ini (today) diperbolehkan
        $selectedDate = \Carbon\Carbon::parse($tanggal)->startOfDay();
        $today = \Carbon\Carbon::today();

        if ($selectedDate->greaterThan($today)) {
            return response()->json([
                'sudah_absen'    => false,
                'sudah_diajukan' => false,
                'jam_kerja'      => null,
                'error'          => 'Tanggal tidak boleh lebih dari hari ini.',
            ], 422);
        }

        // Cek hari libur
        $cekLibur = cekHariLiburPegawai($nik, $tanggal);
        if ($cekLibur) {
            return response()->json([
                'sudah_absen'    => false,
                'sudah_diajukan' => false,
                'jam_kerja'      => null,
                'hari_libur'     => true,
                'keterangan_libur' => $cekLibur->keterangan ?? 'Hari Libur',
            ]);
        }

        // 1. Cek tabel presensi (pencarian lebih fleksibel)
        $sudah_absen = DB::table('presensi')
            ->where('nik', 'like', '%' . $nik . '%')
            ->whereDate('tgl_presensi', $tanggal)
            ->exists();

        // 2. Cek tabel pengajuan_izin
        $sudah_izin = DB::table('pengajuan_izin')
            ->where('nik', 'like', '%' . $nik . '%')
            ->whereDate('tgl_izin_dari', '<=', $tanggal)
            ->whereDate('tgl_izin_sampai', '>=', $tanggal)
            ->where('status_approved', '!=', '2')
            ->exists();

        if ($sudah_absen || $sudah_izin) {
            return response()->json([
                'sudah_absen'    => true,
                'sudah_diajukan' => false,
                'jam_kerja'      => null
            ]);
        }

        // 3. Cek apakah sudah ada pengajuan konfirmasi
        $sudah_diajukan = DB::table('konfirmasi_kehadiran')
            ->where('nik', $nik)
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        // 4. Ambil jadwal kerja
        $jk = $this->getJamKerja($nik, $tanggal, $karyawan->kode_dept, $karyawan->kode_cabang);
        $hari_kerja = $this->getHariKerja($nik, $karyawan->kode_dept, $karyawan->kode_cabang);

        return response()->json([
            'jam_kerja'      => $jk,
            'sudah_absen'    => false,
            'sudah_diajukan' => $sudah_diajukan,
            'hari_kerja'     => $hari_kerja,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal'       => 'required|date|before_or_equal:today',  // ← diubah: sebelumnya 'before:today', sekarang 'before_or_equal:today'
            'kode_jam_kerja'=> 'required',
            'jam_masuk'     => 'required',
            'jam_keluar'    => 'required',
            'alasan'        => 'required|min:10',
            'bukti'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;

        // Validasi hari libur
        $cekLibur = cekHariLiburPegawai($nik, $request->tanggal);
        if ($cekLibur) {
            return redirect('/konfirmasikehadiran/riwayat')
                ->with('error', 'Tanggal ' . date('d-m-Y', strtotime($request->tanggal)) . ' tercatat sebagai hari libur: ' . ($cekLibur->keterangan ?? 'Hari Libur') . '. Tidak dapat mengajukan konfirmasi kehadiran.');
        }
        
        $quick_token = bin2hex(random_bytes(20));
        // Data yang akan disimpan
        $data = [
            'nik'            => $nik,
            'tanggal'        => $request->tanggal,
            'kode_jam_kerja' => $request->kode_jam_kerja,
            'jam_masuk'      => $request->jam_masuk,
            'jam_keluar'     => $request->jam_keluar,
            'alasan'         => $request->alasan,
            'status'         => 'pending',
            'quick_approve_token' => $quick_token,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        // Handle Upload Bukti
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $nama = $nik . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads/konfirmasi_kehadiran', $nama);
            $data['bukti'] = $nama;
        }

        DB::beginTransaction();
        try {
            // Cek duplikat: apakah sudah ada pengajuan untuk NIK + tanggal ini yang pending/approved
            $sudahAda = DB::table('konfirmasi_kehadiran')
                ->where('nik', $nik)
                ->where('tanggal', $request->tanggal)
                ->whereIn('status', ['pending', 'approved'])
                ->lockForUpdate()
                ->exists();
            
            if ($sudahAda) {
                DB::rollBack();
                return redirect('/konfirmasikehadiran/riwayat')
                    ->with('error', 'Anda sudah mengajukan konfirmasi kehadiran untuk tanggal ini.');
            }

            DB::table('konfirmasi_kehadiran')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save data. Please contact administrator.');
        }

        // Kirim notifikasi WA ke Admin/Kepsek (setelah commit)
        try {
            $adminsConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admins')->first();
            $adminNumbers = $adminsConfig ? $adminsConfig->value : env('FONNTE_ADMIN_NUMBERS');
            if (!empty($adminNumbers)) {
                $nama_lengkap = Auth::guard('karyawan')->user()->nama_lengkap;
                $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
                $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
                $nama_cabang = $cabang ? $cabang->nama_cabang : '-';

                $targets = explode(',', $adminNumbers);
                foreach ($targets as $index => $target) {
                    // Skip Admin 4 (khusus rekap, tidak menerima notifikasi pengajuan)
                    if ($index === 3) {
                        continue;
                    }
                    $admNum = $index + 1;
                    $quickLink = url("/quick-approve/kehadiran/{$quick_token}?adm={$admNum}");

                    $message = "*NOTIFIKASI KONFIRMASI KEHADIRAN BARU*\n\n"
                        . "Terdapat pengajuan konfirmasi kehadiran baru dari guru:\n"
                        . "Nama: " . $nama_lengkap . "\n"
                        . "Unit: " . $nama_cabang . "\n"
                        . "Tanggal: " . date('d-m-Y', strtotime($request->tanggal)) . "\n"
                        . "Jam Kerja: " . $request->jam_masuk . " s/d " . $request->jam_keluar . "\n"
                        . "Alasan: " . $request->alasan . "\n\n"
                        . "Mohon pemeriksaan dan verifikasi di panel administrator SiGuru.\n"
                        . "Terima kasih.\n\n"
                        . "----------------------------------\n"
                        . "*AKSI PERSETUJUAN CEPAT (WA)*\n\n"
                        . "👉 *Detail & Persetujuan:*\n" . $quickLink;

                    sendWaNotification(trim($target), $message);
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi baru konfirmasi kehadiran WA: " . $e->getMessage());
        }

        return redirect('/konfirmasikehadiran/riwayat')->with('success', 'Attendance confirmation request submitted successfully.');
    }

    // ─── Riwayat milik guru yang login ─────────────────────────────────────
    public function riwayat(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = DB::table('konfirmasi_kehadiran')
            ->where('nik', $nik);

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('konfirmasikehadiran.riwayat', compact('data', 'namabulan'));
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  ADMIN – List & Aksi
    // ═══════════════════════════════════════════════════════════════════════

    public function adminIndex(Request $request)
    {
        $query = DB::table('konfirmasi_kehadiran')
            ->join('karyawan', 'konfirmasi_kehadiran.nik', '=', 'karyawan.nik')
            ->select('konfirmasi_kehadiran.*', 'karyawan.nama_lengkap', 'karyawan.jabatan');

        if ($request->filled('status')) {
            $query->where('konfirmasi_kehadiran.status', $request->status);
        }
        if ($request->filled('dari')) {
            $query->where('konfirmasi_kehadiran.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('konfirmasi_kehadiran.tanggal', '<=', $request->sampai);
        }
        if ($request->filled('nama')) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $request->nama . '%');
        }

        $data = $query->orderBy('konfirmasi_kehadiran.created_at', 'desc')->paginate(20);
        $data->appends($request->all());

        return view('konfirmasikehadiran.admin_index', compact('data'));
    }

    public function approve(Request $request, $id)
    {
        $pengajuan = DB::table('konfirmasi_kehadiran')->where('id', $id)->first();
        if (!$pengajuan || $pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
        }

        // Cek duplikat presensi (race condition guard)
        $sudahAbsen = DB::table('presensi')
            ->where('nik', $pengajuan->nik)
            ->where('tgl_presensi', $pengajuan->tanggal)
            ->exists();

        DB::beginTransaction();
        try {
            if (!$sudahAbsen) {
                DB::table('presensi')->insert([
                    'nik'            => $pengajuan->nik,
                    'tgl_presensi'   => $pengajuan->tanggal,
                    'jam_in'         => $pengajuan->jam_masuk,
                    'jam_out'        => $pengajuan->jam_keluar,
                    'kode_jam_kerja' => $pengajuan->kode_jam_kerja,
                    'status'         => 'h',
                ]);
            }

            DB::table('konfirmasi_kehadiran')->where('id', $id)->update([
                'status'       => 'approved',
                'approved_by'  => Auth::guard('user')->user()->name,
                'catatan_admin'=> $request->catatan_admin ?? null,
                'updated_at'   => now(),
            ]);

            DB::commit();

            // Kirim notifikasi WA status ke Guru/Karyawan
            try {
                $karyawan = DB::table('karyawan')->where('nik', $pengajuan->nik)->first();
                if ($karyawan && !empty($karyawan->no_hp)) {
                    $catatan = $request->catatan_admin ?? '-';
                    $message = "*NOTIFIKASI STATUS KONFIRMASI KEHADIRAN*\n\n"
                        . "Halo " . $karyawan->nama_lengkap . ",\n"
                        . "Pengajuan konfirmasi kehadiran Anda telah selesai diproses oleh Administrator:\n\n"
                        . "Tanggal: " . date('d-m-Y', strtotime($pengajuan->tanggal)) . "\n"
                        . "Status: DISETUJUI\n"
                        . "Catatan: " . $catatan . "\n\n"
                        . "Terima kasih atas konfirmasi Anda.";
                    
                    sendWaNotification($karyawan->no_hp, $message);
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi status konfirmasi kehadiran WA: " . $e->getMessage());
            }

            return back()->with('success', 'Pengajuan disetujui dan presensi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $pengajuan = DB::table('konfirmasi_kehadiran')->where('id', $id)->first();
        if (!$pengajuan || $pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
        }

        DB::table('konfirmasi_kehadiran')->where('id', $id)->update([
            'status'        => 'rejected',
            'approved_by'   => Auth::guard('user')->user()->name,
            'catatan_admin' => $request->catatan_admin,
            'updated_at'    => now(),
        ]);

        // Kirim notifikasi WA status ke Guru/Karyawan
        try {
            $karyawan = DB::table('karyawan')->where('nik', $pengajuan->nik)->first();
            if ($karyawan && !empty($karyawan->no_hp)) {
                $catatan = $request->catatan_admin ?? '-';
                $message = "*NOTIFIKASI STATUS KONFIRMASI KEHADIRAN*\n\n"
                    . "Halo " . $karyawan->nama_lengkap . ",\n"
                    . "Pengajuan konfirmasi kehadiran Anda telah selesai diproses oleh Administrator:\n\n"
                    . "Tanggal: " . date('d-m-Y', strtotime($pengajuan->tanggal)) . "\n"
                    . "Status: DITOLAK\n"
                    . "Catatan: " . $catatan . "\n\n"
                    . "Terima kasih atas konfirmasi Anda.";
                
                sendWaNotification($karyawan->no_hp, $message);
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi status konfirmasi kehadiran WA: " . $e->getMessage());
        }

        return back()->with('success', 'Pengajuan ditolak.');
    }
}