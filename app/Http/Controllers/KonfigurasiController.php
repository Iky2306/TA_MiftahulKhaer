<?php

namespace App\Http\Controllers;

use App\Models\Setjamkerja;
use App\Models\Setjamkerjadept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{
    public function lokasikantor()
    {
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasikantor', compact('lok_kantor'));
    }

    public function updatelokasikantor(Request $request)
    {
        $lokasi_kantor = $request->lokasi_kantor;
        $radius = $request->radius;

        $update = DB::table('konfigurasi_lokasi')->where('id', 1)->update([
            'lokasi_kantor' => $lokasi_kantor,
            'radius' => $radius
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function jamkerja()
    {
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('konfigurasi.jamkerja', compact('jam_kerja'));
    }

    public function storejamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $jam_masuk = $request->jam_masuk;
        $jam_pulang = $request->jam_pulang;
        $lintashari = $request->lintashari ?? 0;

        // Auto-calculate total_jam from jam_masuk and jam_pulang
        $masuk = strtotime($jam_masuk);
        $pulang = strtotime($jam_pulang);
        $diff = $pulang - $masuk;
        if ($diff < 0) $diff += 86400; // lintas hari
        $total_jam = round($diff / 3600, 1);

        $data = [
            'kode_jam_kerja' => $kode_jam_kerja,
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $jam_pulang,
            'status_istirahat' => 0,
            'awal_jam_istirahat' => null,
            'akhir_jam_istirahat' => null,
            'jam_pulang' => $jam_pulang,
            'total_jam' => $total_jam,
            'lintashari' => $lintashari
        ];
        try {
            DB::table('jam_kerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }


    public function editjamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        return view('konfigurasi.editjamkerja', compact('jamkerja'));
    }

    public function updatejamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $jam_masuk = $request->jam_masuk;
        $jam_pulang = $request->jam_pulang;

        // Auto-calculate total_jam from jam_masuk and jam_pulang
        $masuk = strtotime($jam_masuk);
        $pulang = strtotime($jam_pulang);
        $diff = $pulang - $masuk;
        if ($diff < 0) $diff += 86400; // lintas hari
        $total_jam = round($diff / 3600, 1);

        $data = [
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $jam_pulang,
            'jam_pulang' => $jam_pulang,
            'total_jam' => $total_jam,
        ];
        try {
            DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function deletejamkerja($kode_jam_kerja)
    {
        $hapus = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Hapus']);
        }
    }

        public function setjamkerja($nik)
{
    $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
    $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
    $cekjamkerja = DB::table('konfigurasi_jamkerja')->where('nik', $nik)->count();
    $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    // Daftar hari lengkap
    $hari_lengkap = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    
    if ($cekjamkerja > 0) {
        $setjamkerja = DB::table('konfigurasi_jamkerja')->where('nik', $nik)->get();
        
        // Gabungkan dengan hari lengkap
        $result = [];
        foreach ($hari_lengkap as $hari) {
            $found = $setjamkerja->firstWhere('hari', $hari);
            $result[] = (object)[
                'hari' => $hari,
                'kode_jam_kerja' => $found ? $found->kode_jam_kerja : null
            ];
        }
        
        return view('konfigurasi.editsetjamkerja', compact('karyawan', 'jamkerja', 'result', 'bulan'));
    } else {
        // Untuk kasus belum ada setting, buat array kosong untuk semua hari
        $result = [];
        foreach ($hari_lengkap as $hari) {
            $result[] = (object)[
                'hari' => $hari,
                'kode_jam_kerja' => null
            ];
        }
        return view('konfigurasi.editsetjamkerja', compact('karyawan', 'jamkerja', 'result', 'bulan'));
    }
}


    public function storesetjamkerja(Request $request)
{
    $nik = $request->nik;
    $hari = $request->hari;
    $kode_jam_kerja = $request->kode_jam_kerja;

    // Hanya simpan jika jam kerja dipilih
    $data = [];
    for ($i = 0; $i < count($hari); $i++) {
        if (!empty($kode_jam_kerja[$i])) {
            $data[] = [
                'nik' => $nik,
                'hari' => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
            ];
        }
    }

    DB::beginTransaction();
    try {
        // Hapus setting lama
        Setjamkerja::where('nik', $nik)->delete();
        
        // Simpan yang baru
        if (!empty($data)) {
            Setjamkerja::insert($data);
        }
        
        DB::commit();
        return redirect('/karyawan')->with(['success' => 'Jam Kerja Berhasil Di Seting']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect('/karyawan')->with(['warning' => 'Jam Kerja Gagal Di Seting']);
    }
}

public function updatesetjamkerja(Request $request)
{
    $nik = $request->nik;
    $hari = $request->hari;
    $kode_jam_kerja = $request->kode_jam_kerja;

    // Hanya simpan jika jam kerja dipilih
    $data = [];
    for ($i = 0; $i < count($hari); $i++) {
        if (!empty($kode_jam_kerja[$i])) {
            $data[] = [
                'nik' => $nik,
                'hari' => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
            ];
        }
    }

    DB::beginTransaction();
    try {
        // Hapus setting lama
        Setjamkerja::where('nik', $nik)->delete();
        
        // Simpan yang baru
        if (!empty($data)) {
            Setjamkerja::insert($data);
        }
        
        DB::commit();
        return redirect('/karyawan')->with(['success' => 'Jam Kerja Berhasil Di Update']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect('/karyawan')->with(['warning' => 'Jam Kerja Gagal Di Update']);
    }
}

    public function jamkerjadept()
    {

        $jamkerjadept = DB::table('konfigurasi_jk_dept')
            ->join('cabang', 'konfigurasi_jk_dept.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('departemen', 'konfigurasi_jk_dept.kode_dept', '=', 'departemen.kode_dept')
            ->get();
        return view('konfigurasi.jamkerjadept', compact('jamkerjadept'));
    }


    public function createjamkerjadept()
    {
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cabang = DB::table('cabang')->get();
        $departemen = DB::table('departemen')->get();
        return view('konfigurasi.createjamkerjadept', compact('jamkerja', 'cabang', 'departemen'));
    }


    public function storejamkerjadept(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_dept = $request->kode_dept;
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $kode_jk_dept = "J" . $kode_cabang . $kode_dept;

        DB::beginTransaction();
        try {
            //Menyimpan Data ke Table Konfigurasi_jk_dept
            DB::table('konfigurasi_jk_dept')->insert([
                'kode_jk_dept' => $kode_jk_dept,
                'kode_cabang' => $kode_cabang,
                'kode_dept' => $kode_dept
            ]);

            for ($i = 0; $i < count($hari); $i++) {
                $data[] = [
                    'kode_jk_dept' => $kode_jk_dept,
                    'hari' => $hari[$i],
                    'kode_jam_kerja' => $kode_jam_kerja[$i]
                ];
            }
            Setjamkerjadept::insert($data);
            DB::commit();
            return redirect('/konfigurasi/jamkerjadept')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/konfigurasi/jamkerjadept')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function editjamkerjadept($kode_jk_dept)
    {

        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cabang = DB::table('cabang')->get();
        $departemen = DB::table('departemen')->get();
        $jamkerjadept = DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->first();
        $jamkerjadept_detail = DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->get();
        return view('konfigurasi.editjamkerjadept', compact('jamkerja', 'cabang', 'departemen', 'jamkerjadept', 'jamkerjadept_detail'));
    }


    public function updatejamkerjadept($kode_jk_dept, Request $request)
    {

        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        DB::beginTransaction();
        try {

            //Hapus Data Jam kerja Sebelumnya
            DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->delete();

            for ($i = 0; $i < count($hari); $i++) {
                $data[] = [
                    'kode_jk_dept' => $kode_jk_dept,
                    'hari' => $hari[$i],
                    'kode_jam_kerja' => $kode_jam_kerja[$i]
                ];
            }
            Setjamkerjadept::insert($data);
            DB::commit();
            return redirect('/konfigurasi/jamkerjadept')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/konfigurasi/jamkerjadept')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function showjamkerjadept($kode_jk_dept)
    {
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cabang = DB::table('cabang')->get();
        $departemen = DB::table('departemen')->get();
        $jamkerjadept = DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->first();
        $jamkerjadept_detail = DB::table('konfigurasi_jk_dept_detail')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_jk_dept', $kode_jk_dept)->get();
        return view('konfigurasi.showjamkerjadept', compact('jamkerja', 'cabang', 'departemen', 'jamkerjadept', 'jamkerjadept_detail'));
    }


    public function deletejamkerjadept($kode_jk_dept)
    {
        try {
            DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function storesetjamkerjabydate(Request $request)
    {
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $kode_jam_kerja = $request->kode_jam_kerja;

        $data = [
            'nik' => $nik,
            'tanggal' => $tanggal,
            'kode_jam_kerja' => $kode_jam_kerja
        ];


        try {
            DB::table('konfigurasi_jamkerja_by_date')->insert($data);
            return 0;
        } catch (\Exception $e) {
            return 1;
            // dd($e);
        }
    }


    public function getjamkerjabydate($nik, $bulan, $tahun)
    {


        $konfigurasijamkerjabydate = DB::table('konfigurasi_jamkerja_by_date')
            ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tanggal)="' . $bulan . '"')
            ->whereRaw('YEAR(tanggal)="' . $tahun . '"')
            ->get();
        return view('konfigurasi.getjamkerjabydate', compact('konfigurasijamkerjabydate', 'nik'));
    }


    public function deletejamkerjabydate(Request $request)
    {
        $nik = $request->nik;
        $tanggal = $request->tanggal;

        try {
            DB::table('konfigurasi_jamkerja_by_date')->where('nik', $nik)->where('tanggal', $tanggal)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function temaDashboard()
    {
        $config = DB::table('konfigurasi_app')->where('key', 'tema_dashboard')->first();
        $tema = $config ? $config->value : 'default';
        return view('konfigurasi.temadashboard', compact('tema'));
    }

    public function waGateway()
    {
        $tokenConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_token')->first();
        $wa_token = $tokenConfig ? $tokenConfig->value : '';

        $adminsConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admins')->first();
        $wa_admins_raw = $adminsConfig ? $adminsConfig->value : '';
        $wa_admins = explode(',', $wa_admins_raw);

        $admin1 = isset($wa_admins[0]) ? trim($wa_admins[0]) : '';
        $admin2 = isset($wa_admins[1]) ? trim($wa_admins[1]) : '';
        $admin3 = isset($wa_admins[2]) ? trim($wa_admins[2]) : '';
        $admin4 = isset($wa_admins[3]) ? trim($wa_admins[3]) : '';

        $namesConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admin_names')->first();
        $wa_names_raw = $namesConfig ? $namesConfig->value : '';
        if (strpos($wa_names_raw, '|') !== false) {
            $wa_names = explode('|', $wa_names_raw);
        } else {
            $wa_names = explode(',', $wa_names_raw);
        }

        $name1 = isset($wa_names[0]) ? trim($wa_names[0]) : '';
        $name2 = isset($wa_names[1]) ? trim($wa_names[1]) : '';
        $name3 = isset($wa_names[2]) ? trim($wa_names[2]) : '';
        $name4 = isset($wa_names[3]) ? trim($wa_names[3]) : '';

        return view('konfigurasi.wagateway', compact('wa_token', 'admin1', 'admin2', 'admin3', 'admin4', 'name1', 'name2', 'name3', 'name4'));
    }

    public function updateTemaDashboard(Request $request)
    {
        $tema = $request->tema_dashboard;

        // Only allow valid values
        if (!in_array($tema, ['default', 'ramadan', 'merdeka'])) {
            return Redirect::back()->with(['warning' => 'Tema tidak valid']);
        }

        $exists = DB::table('konfigurasi_app')->where('key', 'tema_dashboard')->exists();

        if ($exists) {
            DB::table('konfigurasi_app')->where('key', 'tema_dashboard')->update([
                'value' => $tema,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('konfigurasi_app')->insert([
                'key' => 'tema_dashboard',
                'value' => $tema,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return Redirect::back()->with(['success' => 'Tema dashboard berhasil diubah']);
    }

    public function updateWaGateway(Request $request)
    {
        $wa_token = $request->wa_gateway_token;
        $admin1 = $request->admin_1;
        $admin2 = $request->admin_2;
        $admin3 = $request->admin_3;
        $admin4 = $request->admin_4;

        $name1 = $request->admin_name_1;
        $name2 = $request->admin_name_2;
        $name3 = $request->admin_name_3;
        $name4 = $request->admin_name_4;

        // Clean and filter empty numbers
        $numbers = [];
        $names = [];

        // We pair them up to maintain matching array indices
        if (!empty($admin1)) {
            $numbers[] = trim($admin1);
            $names[] = !empty($name1) ? trim($name1) : 'Admin Utama';
        }
        if (!empty($admin2)) {
            $numbers[] = trim($admin2);
            $names[] = !empty($name2) ? trim($name2) : 'Admin 2';
        }
        if (!empty($admin3)) {
            $numbers[] = trim($admin3);
            $names[] = !empty($name3) ? trim($name3) : 'Admin 3';
        }
        if (!empty($admin4)) {
            $numbers[] = trim($admin4);
            $names[] = !empty($name4) ? trim($name4) : 'Admin 4';
        }

        $wa_admins = implode(',', $numbers);
        $wa_names = implode('|', $names);

        try {
            // Save Token
            $existsToken = DB::table('konfigurasi_app')->where('key', 'wa_gateway_token')->exists();
            if ($existsToken) {
                DB::table('konfigurasi_app')->where('key', 'wa_gateway_token')->update([
                    'value' => $wa_token,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('konfigurasi_app')->insert([
                    'key' => 'wa_gateway_token',
                    'value' => $wa_token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Save Admin Numbers
            $existsAdmins = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admins')->exists();
            if ($existsAdmins) {
                DB::table('konfigurasi_app')->where('key', 'wa_gateway_admins')->update([
                    'value' => $wa_admins,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('konfigurasi_app')->insert([
                    'key' => 'wa_gateway_admins',
                    'value' => $wa_admins,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Save Admin Names
            $existsNames = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admin_names')->exists();
            if ($existsNames) {
                DB::table('konfigurasi_app')->where('key', 'wa_gateway_admin_names')->update([
                    'value' => $wa_names,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('konfigurasi_app')->insert([
                    'key' => 'wa_gateway_admin_names',
                    'value' => $wa_names,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return Redirect::back()->with(['success' => 'Konfigurasi WA Gateway Fonnte berhasil diperbarui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal memperbarui konfigurasi WA Gateway: ' . $e->getMessage()]);
        }
    }

    public function leaderboard()
    {
        $startConfig  = DB::table('konfigurasi_app')->where('key', 'leaderboard_start_date')->first();
        $endConfig    = DB::table('konfigurasi_app')->where('key', 'leaderboard_end_date')->first();
        $statusConfig = DB::table('konfigurasi_app')->where('key', 'leaderboard_status')->first();
        $msgConfig    = DB::table('konfigurasi_app')->where('key', 'leaderboard_message')->first();

        $tgl_mulai   = $startConfig ? $startConfig->value : '2026-08-03';
        $tgl_selesai = $endConfig ? $endConfig->value : '2027-08-02';
        $status      = $statusConfig ? $statusConfig->value : 'active';
        $pesan       = $msgConfig ? $msgConfig->value : 'Tanggal mulai perhitungan skor poin belum dimulai atau telah berakhir.';

        return view('konfigurasi.leaderboard', compact('tgl_mulai', 'tgl_selesai', 'status', 'pesan'));
    }

    public function updateLeaderboard(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $status      = $request->status;
        $pesan       = $request->pesan;

        try {
            $configs = [
                'leaderboard_start_date' => $tgl_mulai,
                'leaderboard_end_date'   => $tgl_selesai,
                'leaderboard_status'     => $status,
                'leaderboard_message'    => $pesan,
            ];

            foreach ($configs as $key => $val) {
                $exists = DB::table('konfigurasi_app')->where('key', $key)->exists();
                if ($exists) {
                    DB::table('konfigurasi_app')->where('key', $key)->update([
                        'value'      => $val,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('konfigurasi_app')->insert([
                        'key'        => $key,
                        'value'      => $val,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return Redirect::back()->with(['success' => 'Konfigurasi Poin & Leaderboard berhasil diperbarui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal memperbarui konfigurasi: ' . $e->getMessage()]);
        }
    }
}
