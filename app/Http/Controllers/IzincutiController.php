<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzincutiController extends Controller
{
    public function create()
    {
        $mastercuti = DB::table('master_cuti')->orderBy('kode_cuti')->get();
        return view('izincuti.create', compact('mastercuti'));
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $kode_cuti = $request->kode_cuti;
        $status = "c";
        $keterangan = $request->keterangan;

        $bulan = date("m", strtotime($tgl_izin_dari));
        $tahun = date("Y", strtotime($tgl_izin_dari));
        $thn = substr($tahun, 2, 2);

        $quick_token = bin2hex(random_bytes(20));

        DB::beginTransaction();
        try {
            // Hitung Jumlah Hari Yang Di Ajukan
            $jmlhari = hitunghari($tgl_izin_dari, $tgl_izin_sampai);

            // Cek Jumlah Maksimal Cuti (lockForUpdate)
            $cut = DB::table('master_cuti')
                ->where('kode_cuti', $kode_cuti)
                ->lockForUpdate()
                ->first();

            $jmlmaxcuti = $cut ? $cut->jml_hari : 0;

            // Cek Jml Cuti Yang Sudah Digunakan Pada Tahun Aktif
            $cutidigunakan = DB::table('presensi')
                ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
                ->where('status', 'c')
                ->where('nik', $nik)
                ->lockForUpdate()
                ->count();

            // Sisa Cuti
            $sisacuti = $jmlmaxcuti - $cutidigunakan;

            // 1. Cek Sudah Absen / Belum
            $cekpresensi = DB::table('presensi')
                ->whereBetween('tgl_presensi', [$tgl_izin_dari, $tgl_izin_sampai])
                ->where('nik', $nik)
                ->lockForUpdate()
                ->get();

            // 2. Cek Sudah Di Ajukan / Belum (Overlap Check)
            $cekpengajuan = DB::table('pengajuan_izin')
                ->where('nik', $nik)
                ->where(function($query) use ($tgl_izin_dari, $tgl_izin_sampai) {
                    $query->whereBetween('tgl_izin_dari', [$tgl_izin_dari, $tgl_izin_sampai])
                          ->orWhereBetween('tgl_izin_sampai', [$tgl_izin_dari, $tgl_izin_sampai])
                          ->orWhere(function($q) use ($tgl_izin_dari, $tgl_izin_sampai) {
                              $q->where('tgl_izin_dari', '<=', $tgl_izin_dari)
                                ->where('tgl_izin_sampai', '>=', $tgl_izin_sampai);
                          });
                })
                ->where('status_approved', '!=', '2')
                ->lockForUpdate()
                ->exists();

            if ($jmlhari > $sisacuti) {
                DB::rollBack();
                return redirect('/presensi/izin')->with(['error' => 'Jumlah Hari Melebihi Batas Maksimal Jumlah Hari Cuti dalam 1 Tahun, Sisa Cuti Anda adalah ' . $sisacuti . ' Hari']);
            }

            if ($cekpresensi->count() > 0) {
                DB::rollBack();
                $blacklistdate = "";
                foreach ($cekpresensi as $d) {
                    $blacklistdate .= date('d-m-Y', strtotime($d->tgl_presensi)) . ",";
                }
                return redirect('/presensi/izin')->with(['error' => 'Tidak Bisa Melakukan Pengajuan Pada Tanggal ' . $blacklistdate . 'Karena Ada tanggal yang sudah digunkan / sudah melakukan Presensi, Silahkan Ganti Periode Tanggal Pengajuan']);
            }

            if ($cekpengajuan) {
                DB::rollBack();
                return redirect('/presensi/izin')->with(['error' => 'Tidak Bisa Melakukan Pengajuan Pada Tanggal Tersebut Karena Ada tanggal yang sudah digunakan Sebelumnya !']);
            }

            // Generate kode_izin di dalam transaksi untuk menghindari race condition
            $lastizin = DB::table('pengajuan_izin')
                ->whereRaw('MONTH(tgl_izin_dari)="' . $bulan . '"')
                ->whereRaw('YEAR(tgl_izin_dari)="' . $tahun . '"')
                ->orderBy('kode_izin', 'desc')
                ->lockForUpdate()
                ->first();

            $lastkodeizin = $lastizin != null ? $lastizin->kode_izin : "";
            $format = "IZ" . $bulan . $thn;
            $kode_izin = buatkode($lastkodeizin, $format, 3);

            $data = [
                'kode_izin' => $kode_izin,
                'nik' => $nik,
                'tgl_izin_dari' => $tgl_izin_dari,
                'tgl_izin_sampai' => $tgl_izin_sampai,
                'kode_cuti' => $kode_cuti,
                'status' => $status,
                'keterangan' => $keterangan,
                'quick_approve_token' => $quick_token
            ];

            DB::table('pengajuan_izin')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
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
                    $quickLink = url("/quick-approve/izin/{$quick_token}?adm={$admNum}");

                    $message = "*NOTIFIKASI PENGAJUAN CUTI BARU*\n\n"
                        . "Terdapat pengajuan cuti baru dari guru:\n"
                        . "Nama: " . $nama_lengkap . "\n"
                        . "Unit: " . $nama_cabang . "\n"
                        . "Periode: " . date('d-m-Y', strtotime($tgl_izin_dari)) . " s/d " . date('d-m-Y', strtotime($tgl_izin_sampai)) . "\n"
                        . "Keterangan: " . $keterangan . "\n\n"
                        . "Mohon untuk segera memeriksa dan memproses pengajuan ini di panel administrator SiGuru.\n"
                        . "Terima kasih.\n\n"
                        . "----------------------------------\n"
                        . "*AKSI PERSETUJUAN CEPAT (WA)*\n\n"
                        . "👉 *Detail & Persetujuan:*\n" . $quickLink;

                    sendWaNotification(trim($target), $message);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Gagal mengirim notifikasi baru pengajuan cuti WA: " . $e->getMessage());
        }
        return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function edit($kode_izin)
    {
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $mastercuti = DB::table('master_cuti')->orderBy('kode_cuti')->get();
        return view('izincuti.edit', compact('mastercuti', 'dataizin'));
    }


    public function update($kode_izin, Request $request)
    {
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $keterangan = $request->keterangan;
        $kode_cuti = $request->kode_cuti;
        try {
            //code...
            $data = [
                'tgl_izin_dari' => $tgl_izin_dari,
                'tgl_izin_sampai' => $tgl_izin_sampai,
                'kode_cuti' => $kode_cuti,
                'keterangan' => $keterangan
            ];

            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update($data);
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Diupdate']);
        }
    }

    public function getmaxcuti(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $kode_cuti = $request->kode_cuti;
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tahun_cuti = date('Y', strtotime($tgl_izin_dari));
        $cuti = DB::table('master_cuti')->where('kode_cuti', $kode_cuti)->first();

        if ($kode_cuti == "C01") {
            $cuti_digunakan = DB::table('presensi')
                ->join('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
                ->where('presensi.status', 'c')
                ->where('kode_cuti', 'C01')
                ->whereRaw('YEAR(tgl_presensi)="' . $tahun_cuti . '"')
                ->where('presensi.nik', $nik)
                ->count();
            $max_cuti = $cuti->jml_hari - $cuti_digunakan;
        } else {
            $max_cuti = $cuti->jml_hari;
        }

        return $max_cuti;
    }
}
