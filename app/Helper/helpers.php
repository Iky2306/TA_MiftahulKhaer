<?php

use Illuminate\Support\Facades\DB;

function hitungjamterlambat($jadwal_jam_masuk, $jam_presensi)
{
    $j1 = strtotime($jadwal_jam_masuk);
    $j2 = strtotime($jam_presensi);

    $diffterlambat = $j2 - $j1;

    $jamterlambat = floor($diffterlambat / (60 * 60));
    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60))) / 60);

    $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
    $mterlambat = $menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;


    $terlambat = $jterlambat . ":" . $mterlambat;
    return $terlambat;
}


function hitungjamterlambatdesimal($jam_masuk, $jam_presensi)
{
    $j1 = strtotime($jam_masuk);
    $j2 = strtotime($jam_presensi);

    $diffterlambat = $j2 - $j1;

    $jamterlambat = floor($diffterlambat / (60 * 60));
    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60))) / 60);

    $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
    $mterlambat = $menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;


    $desimalterlambat = $jamterlambat + ROUND(($menitterlambat / 60), 2);
    return  $desimalterlambat;
}



function hitunghari($tanggal_mulai, $tanggal_akhir)
{
    $tanggal_1 = date_create($tanggal_mulai);
    $tanggal_2 = date_create($tanggal_akhir); // waktu sekarang
    $diff = date_diff($tanggal_1, $tanggal_2);
    return $diff->days + 1;
}


function hitungdenda($jam_terlambat)
{
    $j_terlambat = explode(":", $jam_terlambat);
    $jam = intval($j_terlambat[0]) * 1;
    $menit = intval($j_terlambat[1]) * 1;

    /*
        Terlambat < 5 Menit = 0;
        Terlambat 5 - 9 Menit = 5000;
        Terlambat 10 - 14 Menit = 10000;
        Terlambat 15 - 59 Menit = 15000;
        Terlambat > 1 Jam = Upah Perjam ( Potongan Jam Kerja)
    */

    if ($jam < 1) {
        if ($menit >= 5 && $menit < 10) {
            $denda = 5000;
        } else if ($menit >= 10 && $menit < 15) {
            $denda = 15000;
        } else if ($menit >= 15) {
            $denda = 15000;
        } else {
            $denda = 0;
        }
    } else {
        $denda = 0;
    }

    return $denda;
}


function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
{
    /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
    string nomor baru dibawah ini harus dengan format XXX000000
    untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
    $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
    //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
    $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
    //    menyusun kunci dan nomor baru
    $kode = $kunci . $nomor_baru_plus_nol;
    return $kode;
}


function hitungjamkerja($tgl_presensi, $jam_mulai, $jam_berakhir, $max_total_jam, $lintashari, $jam_awal_istirahat, $jam_akhir_istirahat)
{

    if ($lintashari == '1') {
        $tgl_pulang = date('Y-m-d', strtotime("+1 days", strtotime($tgl_presensi)));
    } else {
        $tgl_pulang = $tgl_presensi;
    }
    $jam_mulai = $tgl_presensi . " " . $jam_mulai;
    $jam_pulang = !empty($jam_berakhir) ? $tgl_pulang . " " . $jam_berakhir : "";

    if ($jam_awal_istirahat != "NA") {
        $jam_awal_istirahat = $tgl_pulang . " " . $jam_awal_istirahat;
        $jam_akhir_istirahat = $tgl_pulang . " " . $jam_akhir_istirahat;
        if ($jam_pulang > $jam_awal_istirahat && $jam_pulang <= $jam_akhir_istirahat) {
            $jam_pulang = $jam_awal_istirahat;
        }
    }

    $j_mulai = strtotime($jam_mulai);
    $j_pulang = strtotime($jam_pulang);
    $diff = $j_pulang - $j_mulai;
    if (empty($j_pulang)) {
        $jam = 0;
        $menit = 0;
    } else {
        $jam = floor($diff / (60 * 60)); //DIkurang 1 Jam jam Istirahat
        $m = $diff - $jam * (60 * 60);
        $menit = floor($m / 60);
    }

    $menitdesimal = ROUND($menit / 60, 2);
    /*
    Jika Karyawan Pulang Setelah Jam Isitrahat Maka Total Jam Kerja dikurang 1 Jam  Jika
    Kurang dari Jam Istirahat Tidak DI kurangi 1 Jam */
    if ($jam_awal_istirahat != "NA") {
        if ($jam_pulang > $jam_akhir_istirahat) {
            $jam_istirahat = 1;
        } else {
            $jam_istirahat = 0;
        }
    } else {
        $jam_istirahat = 0;
    }

    $jamdesimal = $jam - $jam_istirahat  + $menitdesimal;
    $totaljam = $jamdesimal > $max_total_jam ? $max_total_jam : $jamdesimal;
    return  empty($jam_pulang) ? 0 : $totaljam;
}


function getKaryawanLibur($dari, $sampai)
{
    $holidays = DB::table('harilibur')
        ->where('tanggal_libur', '<=', $sampai)
        ->where('tanggal_libur_akhir', '>=', $dari)
        ->get();

    if ($holidays->isEmpty()) {
        return [];
    }

    $kode_liburs = $holidays->pluck('kode_libur')->toArray();

    $details = DB::table('harilibur_detail')
        ->whereIn('kode_libur', $kode_liburs)
        ->get()
        ->groupBy('kode_libur');

    $employees = DB::table('karyawan')->select('nik', 'kode_cabang')->get();

    $karyawanlibur = [];

    foreach ($holidays as $h) {
        $has_detail = isset($details[$h->kode_libur]) && !$details[$h->kode_libur]->isEmpty();

        // Calculate overlap dates
        $start = max(strtotime($h->tanggal_libur), strtotime($dari));
        $end = min(strtotime($h->tanggal_libur_akhir), strtotime($sampai));

        for ($t = $start; $t <= $end; $t = strtotime("+1 day", $t)) {
            $tgl = date('Y-m-d', $t);

            if ($has_detail) {
                // Individual holiday: only listed employees
                foreach ($details[$h->kode_libur] as $d) {
                    $karyawanlibur[] = [
                        'nik' => $d->nik,
                        'tanggal_libur' => $tgl,
                        'keterangan' => $h->keterangan
                    ];
                }
            } else {
                // Group holiday: location or all
                if ($h->kode_cabang === 'ALL') {
                    // Everyone
                    foreach ($employees as $e) {
                        $karyawanlibur[] = [
                            'nik' => $e->nik,
                            'tanggal_libur' => $tgl,
                            'keterangan' => $h->keterangan
                        ];
                    }
                } else {
                    // Everyone in this branch
                    foreach ($employees as $e) {
                        if ($e->kode_cabang === $h->kode_cabang) {
                            $karyawanlibur[] = [
                                'nik' => $e->nik,
                                'tanggal_libur' => $tgl,
                                'keterangan' => $h->keterangan
                            ];
                        }
                    }
                }
            }
        }
    }

    return $karyawanlibur;
}


function cekHariLiburPegawai($nik, $tanggal)
{
    $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
    if (!$karyawan) {
        return null;
    }

    $kode_cabang = $karyawan->kode_cabang;

    // Find any holiday that covers this date
    $holiday = DB::table('harilibur')
        ->where('tanggal_libur', '<=', $tanggal)
        ->where('tanggal_libur_akhir', '>=', $tanggal)
        ->where(function ($query) use ($kode_cabang) {
            $query->where('kode_cabang', $kode_cabang)
                  ->orWhere('kode_cabang', 'ALL');
        })
        ->first();

    if (!$holiday) {
        return null;
    }

    // Check if this holiday has specific details
    $has_detail = DB::table('harilibur_detail')
        ->where('kode_libur', $holiday->kode_libur)
        ->exists();

    if ($has_detail) {
        // Only employees in the detail are off
        $is_off = DB::table('harilibur_detail')
            ->where('kode_libur', $holiday->kode_libur)
            ->where('nik', $nik)
            ->exists();

        return $is_off ? $holiday : null;
    }

    // If it doesn't have details, it applies to everyone in the branch/all branches
    return $holiday;
}



function cekkaryawanlibur($array, $search_list)
{

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value) {

        // Iterate over each search condition
        foreach ($search_list as $k => $v) {

            // If the array element does not meet
            // the search condition then continue
            // to the next element
            if (!isset($value[$k]) || $value[$k] != $v) {

                // Skip two loops
                continue 2;
            }
        }

        // Append array element's key to the
        //result array
        $result[] = $value;
    }

    // Return result
    return $result;
}


function gethari($hari)
{
    // $hari = date("D");
    switch ($hari) {
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }

    return $hari_ini;
}

function formatPhoneNumber($number)
{
    // Clean all non-digit characters
    $clean = preg_replace('/[^0-9]/', '', $number);

    // Skip dummy/default numbers
    if (empty($clean) || strlen($clean) < 9 || in_array($clean, ['123456', '1234567', '12345678', '123456789', '1234567890', '12345678900'])) {
        return null;
    }

    // Convert 08... to 628...
    if (strpos($clean, '0') === 0) {
        $clean = '62' . substr($clean, 1);
    }

    return $clean;
}

function sendWaNotification($target, $message)
{
    // Retrieve token from database first, then fallback to .env
    $tokenConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_token')->first();
    $token = $tokenConfig ? $tokenConfig->value : env('FONNTE_TOKEN');

    if (empty($token)) {
        \Log::warning("Fonnte WA notification skipped: Fonnte Token is not configured in konfigurasi_app or .env");
        return false;
    }

    // Format the target phone number
    $formattedTarget = formatPhoneNumber($target);
    if (!$formattedTarget) {
        \Log::info("Fonnte WA notification skipped: Target phone number '{$target}' is invalid or dummy.");
        return false;
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20, // 20s max execution timeout to prevent request blocking
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $formattedTarget,
            'message' => $message,
            'countryCode' => '62',
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        \Log::error("Fonnte WA Send Error: " . $err);
        return false;
    }

    return $response;
}

function checkAdaJadwal($nik, $kode_dept, $kode_cabang, $tgl_presensi, $namahari, $konfig_by_date = [], $konfig_harian = [], $konfig_dept = [])
{
    // 1. Cek By Date
    $key_date = $nik . '_' . $tgl_presensi;
    if (isset($konfig_by_date[$key_date])) {
        return !empty($konfig_by_date[$key_date]->kode_jam_kerja);
    }

    // 2. Cek Harian Per Orang
    $key_harian = $nik . '_' . $namahari;
    if (isset($konfig_harian[$key_harian])) {
        return !empty($konfig_harian[$key_harian]->kode_jam_kerja);
    }

    // 3. Cek Jam Kerja Department Detail
    $key_dept = $kode_dept . '_' . $kode_cabang . '_' . $namahari;
    if (isset($konfig_dept[$key_dept])) {
        return !empty($konfig_dept[$key_dept]->kode_jam_kerja);
    }

    // Fallback DB Query jika map kosong/tidak di-pass
    $byDate = DB::table('konfigurasi_jamkerja_by_date')
        ->where('nik', $nik)
        ->where('tanggal', $tgl_presensi)
        ->first();
    if ($byDate) {
        return !empty($byDate->kode_jam_kerja);
    }

    $harian = DB::table('konfigurasi_jamkerja')
        ->where('nik', $nik)
        ->where('hari', $namahari)
        ->first();
    if ($harian) {
        return !empty($harian->kode_jam_kerja);
    }

    $dept = DB::table('konfigurasi_jk_dept_detail')
        ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
        ->where('kode_dept', $kode_dept)
        ->where('kode_cabang', $kode_cabang)
        ->where('hari', $namahari)
        ->first();
    if ($dept) {
        return !empty($dept->kode_jam_kerja);
    }

    return false;
}

