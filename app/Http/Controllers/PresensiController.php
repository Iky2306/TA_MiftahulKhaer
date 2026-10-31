<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pengajuanizin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{

    public function gethari($hari)
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
    public function create($kode_jam_kerja)
    {

    // ✅ Tambahkan pengecekan izin absen
        // $kode_jam_kerja = $kode_jam_kerja != "null" ? Crypt::decrypt($kode_jam_kerja) : $kode_jam_kerja;
        //     $nik = Auth::guard('karyawan')->user()->nik;
        //     $allowed = [
        //         'PST251001','PST251002','PST251003','PST251004','PST251005',
        //         'PST251006','PST251007','PST251008','PST251009','PST251010',
        //         'PST251011','PST251012','PST251013',
        //     ];

        //     if (!in_array($nik, $allowed)) {
        //         return view('presensi.maintenance');
        //     }
    // 🚧 End pengecekan izin

        $kode_jam_kerja = $kode_jam_kerja != "null" ? Crypt::decrypt($kode_jam_kerja) : $kode_jam_kerja;
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date("Y-m-d");
        $jamsekarang = date("H:i");
        $tgl_sebelumnya = date('Y-m-d', strtotime("-1 days", strtotime($hariini)));
        $cekpresensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();

        $ceklintashari_presensi = $cekpresensi_sebelumnya != null  ? $cekpresensi_sebelumnya->lintashari : 0;

        if ($ceklintashari_presensi == 1) {
            if ($jamsekarang < "08:00") {
                $hariini = $tgl_sebelumnya;
            }
        }
        $namahari = $this->gethari(date('D', strtotime($hariini)));

        $ceklibur = cekHariLiburPegawai($nik, $hariini);
        if ($ceklibur) {
            return view('presensi.notiflibur', ['keterangan' => $ceklibur->keterangan]);
        }

        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $presensi = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik);
        $cek = $presensi->count();
        $datapresensi = $presensi->first();
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();

        if ($kode_jam_kerja == "null") {
            //Cek Jam Kerja By Date
            $jamkerja = DB::table('konfigurasi_jamkerja_by_date')
                ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('tanggal', $hariini)
                ->first();

            //Jika Tidak Memiliki Jam Kerja By Date
            if ($jamkerja == null) {
                //Cek Jam Kerja harian / Jam Kerja Khusus / Jam Kerja Per Orangannya
                $jamkerja = DB::table('konfigurasi_jamkerja')
                    ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('nik', $nik)->where('hari', $namahari)->first();

                // Jika Jam Kerja Harian Kosong
                if ($jamkerja == null) {
                    $jamkerja = DB::table('konfigurasi_jk_dept_detail')
                        ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
                        ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                        ->where('kode_dept', $kode_dept)
                        ->where('kode_cabang', $kode_cabang)
                        ->where('hari', $namahari)->first();
                }
            }
        } else {
            $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        }






        if ($datapresensi != null && $datapresensi->status != "h") {
            return view('presensi.notifizin');
        } else if ($jamkerja == null) {
            return view('presensi.notifjadwal');
        } else {
            $karyawan = Auth::guard('karyawan')->user();
            return view('presensi.create', compact('cek', 'lok_kantor', 'jamkerja', 'hariini', 'kode_jam_kerja', 'karyawan'));
        }
    }

    public function store(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nik = Auth::guard('karyawan')->user()->nik;
        $status_location = Auth::guard('karyawan')->user()->status_location;
        $hariini = date("Y-m-d");
        $jamsekarang = date("H:i");
        $tgl_sebelumnya = date('Y-m-d', strtotime("-1 days", strtotime($hariini)));
        $cekpresensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();

        $ceklintashari_presensi = $cekpresensi_sebelumnya != null  ? $cekpresensi_sebelumnya->lintashari : 0;

        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $tgl_presensi = $ceklintashari_presensi == 1 && $jamsekarang < "08:00" ? $tgl_sebelumnya : date("Y-m-d");

        $ceklibur = cekHariLiburPegawai($nik, $tgl_presensi);
        if ($ceklibur) {
            echo "error|Maaf, Hari ini adalah Hari Libur (" . $ceklibur->keterangan . ")|in";
            return;
        }

        $jam = date("H:i:s");
        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $lok = explode(",", $lok_kantor->lokasi_cabang);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        //Cek Jam Kerja Karyawan
        $namahari = $this->gethari(date('D', strtotime($tgl_presensi)));

        if ($kode_jam_kerja == "null") {
            //Cek Jam Kerja By Date
            $jamkerja = DB::table('konfigurasi_jamkerja_by_date')
                ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('tanggal', $tgl_presensi)
                ->first();

            //Jika Tidak Memiliki Jam Kerja By Date
            if ($jamkerja == null) {
                //Cek Jam Kerja harian / Jam Kerja Khusus / Jam Kerja Per Orangannya
                $jamkerja = DB::table('konfigurasi_jamkerja')
                    ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('nik', $nik)->where('hari', $namahari)->first();

                // Jika Jam Kerja Harian Kosong
                if ($jamkerja == null) {
                    $jamkerja = DB::table('konfigurasi_jk_dept_detail')
                        ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
                        ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                        ->where('kode_dept', $kode_dept)
                        ->where('kode_cabang', $kode_cabang)
                        ->where('hari', $namahari)->first();
                }
            }
        } else {
            $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        }



        $presensi = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik);
        $cek = $presensi->count();
        $datapresensi = $presensi->first();
        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        $tgl_pulang = $jamkerja->lintashari == 1 ? date('Y-m-d', strtotime("+ 1 days", strtotime($tgl_presensi))) : $tgl_presensi;
        $jam_pulang = $hariini . " " . $jam;
        $jamkerja_pulang = $tgl_pulang . " " . $jamkerja->jam_pulang;
        $datakaryawan = DB::table('karyawan')->where('nik', $nik)->first();
        $no_hp = $datakaryawan->no_hp;
        if ($status_location == 1 && $radius > $lok_kantor->radius_cabang) {
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter dari Kantor|radius";
        } else {
            if ($cek > 0) {
                // Absen Pulang: Boleh kapan saja setelah sudah absen masuk
                if (!empty($datapresensi->jam_out)) {
                    echo "error|Anda Sudah Melakukan Absen Pulang Sebelumnya ! |out";
                } else {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $fileName,
                        'lokasi_out' => $lokasi
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
                    if ($update) {
                        echo "success|Terimkasih, Hati Hati Di Jalan|out";
                        Storage::put($file, $image_base64);

                        /*
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://wagateway.pedasalami.com/send-message',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 3, // Set a timeout to prevent long blocking
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('message' => 'Terimakasih Sudah Melakukan Absen Pulang, Anda Melakukan Absen Pada Jam ' . $jam, 'number' => $no_hp, 'file_dikirim' => ''),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        //echo $response;
                        */
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi admin|out";
                    }
                }
            } else {
                if ($jam < $jamkerja->jam_masuk) {
                    echo "error|Maaf Belum Waktunya Melakukan Absensi, Absen Masuk Dibuka Jam " . $jamkerja->jam_masuk . "|in";
                } else if ($jam > $jamkerja->jam_pulang) {
                    echo "error|Maaf Waktu Untuk Absen Masuk Sudah Habis (Batas: " . $jamkerja->jam_pulang . ")|in";
                } else {
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => 'h'
                    ];
                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo "success|Terimkasih, selamat bekerja|in";

                        /*
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://wagateway.pedasalami.com/send-message',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 3, // Set a timeout to prevent long blocking
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('message' => 'Terimakasih Sudah Melakukan Absen Masuk, Anda Melakukan Absen Pada Jam ' . $jam, 'number' => $no_hp, 'file_dikirim' => ''),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        //echo $response;
                        */
                        Storage::put($file, $image_base64);
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                    }
                }
            }
        }
    }

    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $request->validate([
            'foto' => 'image|mimes:png,jpg|max:2048'
        ]);
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        if ($request->hasFile('foto')) {
            $folderPath = "public/uploads/karyawan/";
            $request->file('foto')->storeAs($folderPath, $foto);
        }

        $update = DB::table('karyawan')->where('nik', $nik)->update($data);
        if ($update !== false) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['error' => 'Data gagal Di Update']);
        }
    }


    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;

        $histori = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();


        return view('presensi.gethistori', compact('histori'));
    }

    public function pengajuan()
    {
        return view('presensi.pengajuan');
    }

    public function izin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        if (!empty($request->bulan) && !empty($request->tahun)) {
            $dataizin = DB::table('pengajuan_izin')
                ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
                ->orderBy('tgl_izin_dari', 'desc')
                ->where('nik', $nik)
                ->whereRaw('MONTH(tgl_izin_dari)="' . $request->bulan . '"')
                ->whereRaw('YEAR(tgl_izin_dari)="' . $request->tahun . '"')
                ->get();
        } else {
            $dataizin = DB::table('pengajuan_izin')
                ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
                ->orderBy('tgl_izin_dari', 'desc')
                ->where('nik', $nik)->limit(5)->orderBy('tgl_izin_dari', 'desc')
                ->get();
        }

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.izin', compact('dataizin', 'namabulan'));
    }

    public function buatizin()
    {

        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->orderBy('kode_dept')->get();
        return view('presensi.monitoring', compact('cabang', 'departemen'));
    }

    public function getpresensi(Request $request)
    {
        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $user = User::find(Auth::guard('user')->user()->id);

        $tanggal = $request->tanggal;

        $query = Karyawan::query();
        $query->selectRaw(
            'karyawan.nik, nama_lengkap, karyawan.kode_dept, karyawan.kode_cabang,
        datapresensi.id,jam_in,jam_out,foto_in,foto_out,lokasi_in,lokasi_out,
        datapresensi.status,jam_masuk, nama_jam_kerja, jam_pulang, keterangan'
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                presensi.nik,presensi.id,jam_in,jam_out,foto_in,foto_out,lokasi_in,lokasi_out,presensi.status,jam_masuk, nama_jam_kerja, jam_pulang, keterangan
                FROM presensi
                LEFT JOIN  jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
                WHERE tgl_presensi = '$tanggal'
            ) datapresensi"),
            function ($join) {
                $join->on('karyawan.nik', '=', 'datapresensi.nik');
            }
        );

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        if ($user->hasRole('staff')) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }
        $query->orderBy('nama_lengkap');
        $presensi = $query->get();

        // Implementasi Filter berdasarkan Jadwal Kerja
        $presensi = $presensi->filter(function ($item) use ($tanggal) {
            // Cek apakah karyawan memiliki jadwal pada tanggal tersebut
            $jamkerja = $this->getJamKerja($item->nik, $tanggal, $item->kode_dept, $item->kode_cabang);
            return $jamkerja != null;
        });

        return view('presensi.getpresensi', compact('presensi', 'tanggal'));
    }

    private function getJamKerja($nik, $tanggal, $kode_dept, $kode_cabang)
    {
        $namahari = $this->gethari(date('D', strtotime($tanggal)));

        // 1. Cek Jam Kerja By Date (Khusus tanggal tertentu)
        $jamkerja = DB::table('konfigurasi_jamkerja_by_date')
            ->join('jam_kerja', 'konfigurasi_jamkerja_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->first();

        // 2. Jika tidak ada, Cek Jam Kerja Harian (Perorangan)
        if ($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jamkerja')
                ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)
                ->where('hari', $namahari)
                ->first();
        }

        // 3. Jika tidak ada, Cek Jam Kerja Default Departemen/Cabang
        if ($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jk_dept_detail')
                ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
                ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('kode_dept', $kode_dept)
                ->where('kode_cabang', $kode_cabang)
                ->where('hari', $namahari)
                ->first();
        }

        return $jamkerja;
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }


    public function laporan()
    {
        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $user = User::find(Auth::guard('user')->user()->id);

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        if ($user->hasRole('staff')) {
            $karyawan = DB::table('karyawan')
                ->where('kode_cabang', $kode_cabang)
                ->orderBy('nama_lengkap')->get();
        } else if ($user->hasRole('administrator')) {
            $karyawan = DB::table('karyawan')
                ->orderBy('nama_lengkap')->get();
        }

        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
{
    // Validasi input wajib
    $request->validate([
        'nik' => 'required',
        'bulan' => 'required|numeric|between:1,12',
        'tahun' => 'required|numeric'
    ]);

    $nik = $request->nik;
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                 "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    // Pastikan bulan valid
    if($bulan < 1 || $bulan > 12) {
        return back()->with('error', 'Bulan tidak valid');
    }

    $karyawan = DB::table('karyawan')->where('nik', $nik)
        ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        ->first();

    if(!$karyawan) {
        return back()->with('error', 'Data karyawan tidak ditemukan');
    }

    $presensi = DB::table('presensi')
        ->select('presensi.*', 'keterangan', 'jam_kerja.*')
        ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
        ->where('presensi.nik', $nik)
        ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
        ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
        ->orderBy('tgl_presensi')
        ->get();

    $presensi_array = $presensi->toArray();
    $start_date = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    $current = $start_date;
    while (strtotime($current) <= strtotime($end_date)) {
        $exists = false;
        foreach ($presensi_array as $p) {
            if ($p->tgl_presensi === $current) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $ceklibur = cekHariLiburPegawai($nik, $current);
            if ($ceklibur) {
                $dummy = new \stdClass();
                $dummy->tgl_presensi = $current;
                $dummy->status = 'libur';
                $dummy->keterangan = $ceklibur->keterangan;
                $dummy->jam_in = '-';
                $dummy->jam_out = '-';
                $dummy->jam_masuk = null;
                $dummy->jam_pulang = null;
                $presensi_array[] = $dummy;
            }
        }
        $current = date("Y-m-d", strtotime("+1 day", strtotime($current)));
    }

    usort($presensi_array, function($a, $b) {
        return strcmp($a->tgl_presensi, $b->tgl_presensi);
    });

    $presensi = collect($presensi_array);

    if (isset($_POST['exportpdf'])) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('presensi.cetaklaporan_pdf', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_Presensi_{$karyawan->nama_lengkap}_{$bulan}_{$tahun}.pdf");
    }
    
    return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
}

public function laporanjurnal()
{
    $kode_cabang = Auth::guard('user')->user()->kode_cabang;
    $user = User::find(Auth::guard('user')->user()->id);

    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    if ($user->hasRole('staff')) {
        $karyawan = DB::table('karyawan')
            ->where('kode_dept', 'LIKE', '%GRU%') // Filter specifically for teachers
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('nama_lengkap')->get();
    } else if ($user->hasRole('administrator')) {
        $karyawan = DB::table('karyawan')
            ->where('kode_dept', 'LIKE', '%GRU%') // Filter specifically for teachers
            ->orderBy('nama_lengkap')->get();
    }

    return view('presensi.laporanjurnal', compact('namabulan', 'karyawan'));
}

public function cetaklaporanjurnal(Request $request)
{
    $request->validate([
        'nik'            => 'required',
        'dari_tanggal'   => 'required|date',
        'sampai_tanggal' => 'required|date'
    ]);

    $nik            = $request->nik;
    $dari_tanggal   = $request->dari_tanggal;
    $sampai_tanggal = $request->sampai_tanggal;

    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                 "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    $karyawan = DB::table('karyawan')->where('nik', $nik)
        ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        ->first();

    if (!$karyawan) {
        return back()->with('error', 'Data guru tidak ditemukan');
    }

    $jurnal = DB::table('jurnal_mengajar')
        ->select('jurnal_mengajar.*', 'master_kelas.nama_kelas')
        ->leftJoin('master_kelas', 'jurnal_mengajar.kode_kelas', '=', 'master_kelas.kode_kelas')
        ->where('jurnal_mengajar.nik', $nik)
        ->whereBetween('jurnal_mengajar.tanggal', [$dari_tanggal, $sampai_tanggal])
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();

    if ($request->has('exportpdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('presensi.cetaklaporanjurnal_pdf',
            compact('dari_tanggal', 'sampai_tanggal', 'namabulan', 'karyawan', 'jurnal'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download("Laporan_Jurnal_{$karyawan->nama_lengkap}_{$dari_tanggal}_sampai_{$sampai_tanggal}.pdf");
    }

    return view('presensi.cetaklaporanjurnal',
        compact('dari_tanggal', 'sampai_tanggal', 'namabulan', 'karyawan', 'jurnal'));
}

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $departemen = DB::table('departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('presensi.rekap', compact('namabulan', 'departemen', 'cabang'));
    }

    public function cetakrekap(Request $request)
{
    // Validasi input
    $request->validate([
        'bulan' => 'required|numeric|between:1,12',
        'tahun' => 'required|numeric',
        'jenis_laporan' => 'nullable|in:1,2'
    ]);

    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $kode_dept = $request->kode_dept;
    $kode_cabang = $request->kode_cabang;
    
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                 "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    // Validasi bulan
    if($bulan < 1 || $bulan > 12) {
        return back()->with('error', 'Bulan tidak valid');
    }

    $dari = "$tahun-$bulan-01";
    $sampai = date("Y-m-t", strtotime($dari));
    
    $datalibur = getkaryawanlibur($dari, $sampai);
    $harilibur = DB::table('harilibur')->whereBetween('tanggal_libur', [$dari, $sampai])->get();

    // Generate dynamic columns for each date
    $select_date = "";
    $field_date = "";
    $rangetanggal = [];
    $i = 1;
    
    $current_date = $dari;
    while (strtotime($current_date) <= strtotime($sampai)) {
        $rangetanggal[] = $current_date;

        $select_date .= "MAX(IF(tgl_presensi = '$current_date',
            CONCAT(
                IFNULL(jam_in,'NA'),'|',
                IFNULL(jam_out,'NA'),'|',
                IFNULL(presensi.status,'NA'),'|',
                IFNULL(nama_jam_kerja,'NA'),'|',
                IFNULL(jam_masuk,'NA'),'|',
                IFNULL(jam_pulang,'NA'),'|',
                IFNULL(presensi.kode_izin,'NA'),'|',
                IFNULL(keterangan,'NA'),'|',
                IFNULL(total_jam,'NA'),'|',
                IFNULL(lintashari,'NA'),'|',
                IFNULL(awal_jam_istirahat,'NA'),'|',
                IFNULL(akhir_jam_istirahat,'NA'),'|'
            ),NULL)) as tgl_" . $i . ",";

        $field_date .= "tgl_" . $i . ",";
        $i++;
        $current_date = date("Y-m-d", strtotime("+1 day", strtotime($current_date)));
    }

    // Pad array untuk bulan dengan kurang dari 31 hari
    $jmlhari = count($rangetanggal);
    $rangetanggal = array_pad($rangetanggal, 31, null);

    // Query data karyawan dengan presensi
    $query = Karyawan::query();
    $query->selectRaw("$field_date karyawan.nik, nama_lengkap, jabatan, karyawan.kode_dept, karyawan.kode_cabang")
          ->leftJoin(
              DB::raw("(SELECT $select_date presensi.nik FROM presensi
                      LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                      LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
                      WHERE tgl_presensi BETWEEN '$dari' AND '$sampai'
                      GROUP BY nik) presensi"),
              'karyawan.nik', '=', 'presensi.nik'
          );

    if (!empty($kode_dept)) {
        $query->where('kode_dept', $kode_dept);
    }
    if (!empty($kode_cabang)) {
        $query->where('kode_cabang', $kode_cabang);
    }

    $rekap = $query->orderBy('nama_lengkap')->get();

    // Map Konfigurasi Jam Kerja untuk pengecekan jadwal mengajar
    $konfig_by_date = DB::table('konfigurasi_jamkerja_by_date')
        ->whereBetween('tanggal', [$dari, $sampai])
        ->get()
        ->keyBy(function ($item) {
            return $item->nik . '_' . $item->tanggal;
        });

    $konfig_harian = DB::table('konfigurasi_jamkerja')
        ->get()
        ->keyBy(function ($item) {
            return $item->nik . '_' . $item->hari;
        });

    $konfig_dept = DB::table('konfigurasi_jk_dept_detail')
        ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
        ->get()
        ->keyBy(function ($item) {
            return $item->kode_dept . '_' . $item->kode_cabang . '_' . $item->hari;
        });

    // Export Excel
    if ($request->has('exportexcel')) {
        $time = date("d-M-Y H:i:s");
        return response()->view('presensi.cetakrekap', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namabulan' => $namabulan,
            'rekap' => $rekap,
            'rangetanggal' => $rangetanggal,
            'jmlhari' => $jmlhari,
            'datalibur' => $datalibur,
            'harilibur' => $harilibur,
            'konfig_by_date' => $konfig_by_date,
            'konfig_harian' => $konfig_harian,
            'konfig_dept' => $konfig_dept
        ])->header('Content-Type', 'application/vnd.ms-excel')
          ->header('Content-Disposition', "attachment; filename=Rekap_Presensi_$time.xls");
    }

    // Tampilan normal
    $view = ($request->jenis_laporan == 2) ? 'presensi.cetakrekap_detail' : 'presensi.cetakrekap';
    
    return view($view, compact('bulan', 'tahun', 'namabulan', 'rekap', 
                            'rangetanggal', 'jmlhari', 'datalibur', 'harilibur',
                            'konfig_by_date', 'konfig_harian', 'konfig_dept'));
}

    public function izinsakit(Request $request)
    {

        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $user = User::find(Auth::guard('user')->user()->id);

        $query = Pengajuanizin::query();
        $query->select(
            'kode_izin',
            'tgl_izin_dari',
            'tgl_izin_sampai',
            'pengajuan_izin.nik',
            'nama_lengkap',
            'jabatan',
            'status',
            'status_approved',
            'keterangan',
            'karyawan.kode_cabang',
            'karyawan.kode_dept',
            'doc_sid'
        );
        $query->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin_dari', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', $request->nik);
        }

        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }

        if ($user->hasRole('staff')) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $query->orderBy('tgl_izin_dari', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->orderBy('kode_dept')->get();

        // Query Konfirmasi Kehadiran untuk tab kedua
        $qk = DB::table('konfirmasi_kehadiran')
            ->join('karyawan', 'konfirmasi_kehadiran.nik', '=', 'karyawan.nik')
            ->select('konfirmasi_kehadiran.*', 'karyawan.nama_lengkap', 'karyawan.jabatan');

        if ($request->filled('dari') && $request->tab === 'konfirmasi') {
            $qk->where('konfirmasi_kehadiran.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai') && $request->tab === 'konfirmasi') {
            $qk->where('konfirmasi_kehadiran.tanggal', '<=', $request->sampai);
        }
        if ($request->filled('nama') && $request->tab === 'konfirmasi') {
            $qk->where('karyawan.nama_lengkap', 'like', '%' . $request->nama . '%');
        }
        if ($request->filled('status_konfirmasi')) {
            $qk->where('konfirmasi_kehadiran.status', $request->status_konfirmasi);
        }
        $qk->orderBy('konfirmasi_kehadiran.created_at', 'desc');
        $konfirmasi = $qk->paginate(10);
        $konfirmasi->appends($request->all());

        return view('presensi.izinsakit', compact('izinsakit', 'cabang', 'departemen', 'konfirmasi'));

    }

    public function approveizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $kode_izin = $request->kode_izin_form;
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $nik = $dataizin->nik;
        $tgl_dari = $dataizin->tgl_izin_dari;
        $tgl_sampai = $dataizin->tgl_izin_sampai;
        $status = $dataizin->status;
        DB::beginTransaction();
        try {
            if ($status_approved == 1) {
                while (strtotime($tgl_dari) <= strtotime($tgl_sampai)) {

                    DB::table('presensi')->insert([
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_dari,
                        'status' => $status,
                        'kode_izin' => $kode_izin
                    ]);
                    $tgl_dari = date("Y-m-d", strtotime("+1 days", strtotime($tgl_dari)));
                }
            }


            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                'status_approved' => $status_approved
            ]);
            DB::commit();

            // Kirim notifikasi WA status ke Guru/Karyawan
            try {
                $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
                if ($karyawan && !empty($karyawan->no_hp)) {
                    $jenisMap = ['i' => 'Izin', 's' => 'Sakit', 'c' => 'Cuti'];
                    $jenisText = $jenisMap[$status] ?? 'Pengajuan';
                    $statusText = $status_approved == 1 ? 'DISETUJUI' : 'DITOLAK';
                    
                    $message = "*NOTIFIKASI STATUS PENGAJUAN*\n\n"
                        . "Halo " . $karyawan->nama_lengkap . ",\n"
                        . "Pengajuan " . $jenisText . " Anda telah selesai diproses oleh Administrator:\n\n"
                        . "Jenis: " . $jenisText . "\n"
                        . "Periode: " . date('d-m-Y', strtotime($dataizin->tgl_izin_dari)) . " s/d " . date('d-m-Y', strtotime($dataizin->tgl_izin_sampai)) . "\n"
                        . "Status: " . $statusText . "\n\n"
                        . "Terima kasih atas konfirmasi Anda.";
                    
                    sendWaNotification($karyawan->no_hp, $message);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal mengirim notifikasi status izin WA: " . $e->getMessage());
            }

            return Redirect::back()->with(['success' => 'Data Berhasil Diproses']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diproses']);
        }


        // $update = DB::table('pengajuan_izin')->where('id', $kode_izin)->update([
        //     'status_approved' => $status_approved
        // ]);
        // if ($update) {
        //     return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        // } else {
        //     return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        // }
    }

    public function batalkanizinsakit($kode_izin)
    {


        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                'status_approved' => 0
            ]);
            DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Batalkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal DI Batalkan']);
        }

        $update = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
            'status_approved' => 0
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')->where('nik', $nik)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }


    public function storefrommachine()
    {
        $original_data  = file_get_contents('php://input');
        $decoded_data   = json_decode($original_data, true);
        $encoded_data   = json_encode($decoded_data);

        $data           = $decoded_data['data'];
        $pin            = $data['pin'];

        DB::table('presensi')->insert([
            'nik' => $pin,
            'tgl_presensi' => '2023-05-03'
        ]);
    }

    public function showact($kode_izin)
    {
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        return view('presensi.showact', compact('dataizin'));
    }

    public function deleteizin($kode_izin)
    {
        $cekdataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $doc_sid = $cekdataizin->doc_sid;

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();
            // dd($doc_sid);
            if ($doc_sid != null) {

                Storage::delete('/public/uploads/sid/' . $doc_sid);
            }
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Dihapus']);
            //throw $th;
        }
    }

    public function koreksipresensi(Request $request)
    {
        $nik = $request->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $tanggal)->first();
        $jamkerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('presensi.koreksipresensi', compact('karyawan', 'tanggal', 'jamkerja', 'presensi'));
    }


    public function storekoreksipresensi(Request $request)
    {
        $status = $request->status;
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $jam_in = $status == "a" ? NULL : $request->jam_in;
        $jam_out = $status == "a" ? NULL : $request->jam_out;
        $kode_jam_kerja = $status == "a" ? NULL : $request->kode_jam_kerja;

        try {

            $cekpresensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $tanggal)->count();
            if ($cekpresensi > 0) {
                DB::table('presensi')
                    ->where('nik', $nik)
                    ->where('tgl_presensi', $tanggal)
                    ->update([
                        'jam_in' => $jam_in,
                        'jam_out' => $jam_out,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status' => $status
                    ]);
            } else {
                DB::table('presensi')->insert([
                    'nik' => $nik,
                    'tgl_presensi' => $tanggal,
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'status' => $status
                ]);
            }


            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function pilihjamkerja()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date('Y-m-d');
        $cekpresensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();
        if (!empty($cekpresensi)) {
            $kode_jam_kerja = Crypt::encrypt($cekpresensi->kode_jam_kerja);
            return redirect('/presensi/' . $kode_jam_kerja . '/create');
        }
        $jamkerja = DB::table('jam_kerja')->get();
        return view('presensi.pilihjamkerja', compact('jamkerja'));
    }


    // public function scanqr()
    // {
    //     $hariini = date("Y-m-d");
    //     $namahari = $this->gethari();
    //     $nik = Auth::guard('karyawan')->user()->nik;
    //     $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
    //     $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
    //     $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
    //     $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
    //     $jamkerja = DB::table('konfigurasi_jamkerja')
    //         ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
    //         ->where('nik', $nik)->where('hari', $namahari)->first();

    //     if ($jamkerja == null) {
    //         $jamkerja = DB::table('konfigurasi_jk_dept_detail')
    //             ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
    //             ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
    //             ->where('kode_dept', $kode_dept)
    //             ->where('kode_cabang', $kode_cabang)
    //             ->where('hari', $namahari)->first();
    //     }
    //     return view('presensi.scanqr', compact('cek', 'lok_kantor', 'jamkerja'));
    // }


    // public function storeqr(Request $request)
    // {

    //     $nik = Auth::guard('karyawan')->user()->nik;
    //     $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
    //     $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
    //     $qrcode = $request->qrcode;
    //     $tgl_presensi = date("Y-m-d");
    //     $jam = date("H:i:s");
    //     $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
    //     $lok = explode(",", $lok_kantor->lokasi_cabang);
    //     $latitudekantor = $lok[0];
    //     $longitudekantor = $lok[1];
    //     $lokasi = $request->lokasi;
    //     $lokasiuser = explode(",", $lokasi);
    //     $latitudeuser = $lokasiuser[0];
    //     $longitudeuser = $lokasiuser[1];

    //     $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
    //     $radius = round($jarak["meters"]);

    //     //Cek Jam Kerja Karyawan
    //     $namahari = $this->gethari();
    //     $jamkerja = DB::table('konfigurasi_jamkerja')
    //         ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
    //         ->where('nik', $nik)->where('hari', $namahari)->first();

    //     if ($jamkerja == null) {
    //         $jamkerja = DB::table('konfigurasi_jk_dept_detail')
    //             ->join('konfigurasi_jk_dept', 'konfigurasi_jk_dept_detail.kode_jk_dept', '=', 'konfigurasi_jk_dept.kode_jk_dept')
    //             ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
    //             ->where('kode_dept', $kode_dept)
    //             ->where('kode_cabang', $kode_cabang)
    //             ->where('hari', $namahari)->first();
    //     }

    //     $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->count();


    //     // echo $cek;
    //     // die;
    //     if ($cek > 0) {
    //         $ket = "out";
    //     } else {
    //         $ket = "in";
    //     }

    //     $image = $request->image;
    //     if (!empty($image)) {
    //         $folderPath = "public/uploads/absensi/";
    //         $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
    //         $image_parts = explode(";base64", $image);
    //         $image_base64 = base64_decode($image_parts[1]);
    //         $fileName = $formatName . ".png";
    //         $file = $folderPath . $fileName;
    //     } else {
    //         $fileName = NULL;
    //     }

    //     $datakaryawan = DB::table('karyawan')->where('nik', $nik)->first();
    //     $no_hp = $datakaryawan->no_hp;

    //     $kode_validasi = "adamadifa";
    //     if ($qrcode != $kode_validasi) {
    //         echo "error|Kode QR Tidak Valid|validasiqr";
    //     } else {
    //         if ($radius > $lok_kantor->radius_cabang) {
    //             echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter dari Kantor|radius";
    //         } else {
    //             if ($cek > 0) {
    //                 if ($jam < $jamkerja->jam_pulang) {
    //                     echo "error|Maaf Belum Waktunya Pulang |out";
    //                 } else {
    //                     $data_pulang = [
    //                         'jam_out' => $jam,
    //                         'foto_out' => $fileName,
    //                         'lokasi_out' => $lokasi
    //                     ];
    //                     $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
    //                     if ($update) {
    //                         echo "success|Terimkasih, Hati Hati Di Jalan|out";
    //                     } else {
    //                         echo "error|Maaf Gagal absen, Hubungi Tim It|out";
    //                     }
    //                 }
    //             } else {
    //                 if ($jam < $jamkerja->awal_jam_masuk) {
    //                     echo "error|Maaf Belum Waktunya Melakuan Presensi|in";
    //                 } else if ($jam > $jamkerja->akhir_jam_masuk) {
    //                     echo "error|Maaf Waktu Untuk Presensi Sudah Habis |in";
    //                 } else {
    //                     $data = [
    //                         'nik' => $nik,
    //                         'tgl_presensi' => $tgl_presensi,
    //                         'jam_in' => $jam,
    //                         'foto_in' => $fileName,
    //                         'lokasi_in' => $lokasi,
    //                         'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
    //                         'status' => 'h'
    //                     ];
    //                     $simpan = DB::table('presensi')->insert($data);
    //                     if ($simpan) {
    //                         echo "success|Terimkasih, Selamat Bekerja|in";
    //                     } else {
    //                         echo "error|Maaf Gagal absen, Hubungi Tim It|in";
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }
    public function slipgaji(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $bulan = $request->bulan ?? date("m");
        $tahun = $request->tahun ?? date("Y");
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        // Cek apakah Guru
        $guru = DB::table('guru')
            ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
            ->leftJoin('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
            ->where('guru.karyawan_nik', $nik)
            ->select(
                'guru.*', 'karyawan.nama_lengkap',
                DB::raw('COALESCE(setting_gaji_guru.gaji_pokok, 0) as rate_gaji_pokok'),
                DB::raw('COALESCE(setting_gaji_guru.transport, 0) as rate_transport'),
                DB::raw('COALESCE(setting_gaji_guru.simpanan_koperasi, 0) as simpanan_koperasi'),
                DB::raw('COALESCE(setting_gaji_guru.dana_sosial, 0) as dana_sosial')
            )
            ->first();

        $type = 'guru';
        $data = null;

        if ($guru) {
            $hadir = DB::table('presensi')
                ->where('nik', $nik)
                ->whereBetween('tgl_presensi', [$dari, $sampai])
                ->where('status', 'h')
                ->count();
            
            $data = $guru;
            $data->hadir = $hadir;
            $data->jml_gaji_pokok = $guru->jam_mengajar * $guru->rate_gaji_pokok;
            $data->jml_transport = $hadir * $guru->rate_transport;
            $data->total_earnings = $data->jml_gaji_pokok + $data->jml_transport + $guru->tunjangan_jabatan;
            $data->total_deductions = $guru->dana_sosial + $guru->simpanan_koperasi + $guru->potongan;
            $data->total_gaji = $data->total_earnings - $data->total_deductions;
        } else {
            // Cek apakah Pengabdian
            $pengabdian = DB::table('pengabdian')
                ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
                ->where('pengabdian.nik', $nik)
                ->first();

            if ($pengabdian) {
                $type = 'pengabdian';
                $hadir = DB::table('presensi')
                    ->where('nik', $nik)
                    ->whereBetween('tgl_presensi', [$dari, $sampai])
                    ->where('status', 'h')
                    ->count();

                $data = $pengabdian;
                $data->hadir = $hadir;
                $data->jml_honorarium = $pengabdian->honorarium_wkt * $pengabdian->honorarium_stn;
                $data->jml_kehadiran = $hadir * $pengabdian->kehadiran_stn;
                $data->total_earnings = ($pengabdian->tunjangan_jabatan ?? 0) + ($pengabdian->tunjangan_pengabdian ?? 0) + ($pengabdian->tunjangan_lainnya ?? 0) + $data->jml_honorarium + $data->jml_kehadiran;
                $data->total_deductions = ($pengabdian->potongan_dansos ?? 0) + ($pengabdian->potongan_fpmk ?? 0) + ($pengabdian->potongan_lainnya ?? 0);
                $data->total_gaji = $data->total_earnings - $data->total_deductions;
            } else {
                return \Illuminate\Support\Facades\Redirect::back()->with(['error' => 'Data Payroll tidak ditemukan untuk NIK ini.']);
            }
        }

        return view('presensi.slipgaji', compact('type', 'data', 'bulan', 'tahun', 'namabulan', 'nik'));
    }

    public function downloadSlipGaji(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $bulan = $request->bulan ?? date("m");
        $tahun = $request->tahun ?? date("Y");
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        // Cek apakah Guru
        $guru = DB::table('guru')
            ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
            ->leftJoin('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
            ->where('guru.karyawan_nik', $nik)
            ->select(
                'guru.*', 'karyawan.nama_lengkap',
                DB::raw('COALESCE(setting_gaji_guru.gaji_pokok, 0) as rate_gaji_pokok'),
                DB::raw('COALESCE(setting_gaji_guru.transport, 0) as rate_transport'),
                DB::raw('COALESCE(setting_gaji_guru.simpanan_koperasi, 0) as simpanan_koperasi'),
                DB::raw('COALESCE(setting_gaji_guru.dana_sosial, 0) as dana_sosial')
            )
            ->first();

        if ($guru) {
            $type = 'guru';
            $hadir = DB::table('presensi')
                ->where('nik', $nik)
                ->whereBetween('tgl_presensi', [$dari, $sampai])
                ->where('status', 'h')
                ->count();
            
            $data = $guru;
            $data->hadir = $hadir;
            $data->jml_gaji_pokok = $guru->jam_mengajar * $guru->rate_gaji_pokok;
            $data->jml_transport = $hadir * $guru->rate_transport;
            $data->total_earnings = $data->jml_gaji_pokok + $data->jml_transport + $guru->tunjangan_jabatan;
            $data->total_deductions = $guru->dana_sosial + $guru->simpanan_koperasi + $guru->potongan;
            $data->total_gaji = $data->total_earnings - $data->total_deductions;
        } else {
            // Cek apakah Pengabdian
            $pengabdian = DB::table('pengabdian')
                ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
                ->where('pengabdian.nik', $nik)
                ->first();

            if ($pengabdian) {
                $type = 'pengabdian';
                $hadir = DB::table('presensi')
                    ->where('nik', $nik)
                    ->whereBetween('tgl_presensi', [$dari, $sampai])
                    ->where('status', 'h')
                    ->count();

                $data = $pengabdian;
                $data->hadir = $hadir;
                $data->jml_honorarium = $pengabdian->honorarium_wkt * $pengabdian->honorarium_stn;
                $data->jml_kehadiran = $hadir * $pengabdian->kehadiran_stn;
                $data->total_earnings = ($pengabdian->tunjangan_jabatan ?? 0) + ($pengabdian->tunjangan_pengabdian ?? 0) + ($pengabdian->tunjangan_lainnya ?? 0) + $data->jml_honorarium + $data->jml_kehadiran;
                $data->total_deductions = ($pengabdian->potongan_dansos ?? 0) + ($pengabdian->potongan_fpmk ?? 0) + ($pengabdian->potongan_lainnya ?? 0);
                $data->total_gaji = $data->total_earnings - $data->total_deductions;
            } else {
                return \Illuminate\Support\Facades\Redirect::back()->with(['error' => 'Data Payroll tidak ditemukan untuk NIK ini.']);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('presensi.slipgaji_pdf', compact('type', 'data', 'bulan', 'tahun', 'namabulan', 'nik'))
               ->setPaper([0, 0, 595, 450], 'portrait');

        $nama_lengkap = Auth::guard('karyawan')->user()->nama_lengkap;
        $filename = "SLIP_GAJI_{$nama_lengkap}_{$bulan}_{$tahun}.pdf";
        return $pdf->download($filename);
    }


    public function storeFaceDescriptor(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $descriptor = $request->descriptor;

        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        // Batas waktu update (1 jam)
        if ($karyawan->face_updated_at != null) {
            $lastUpdate = \Carbon\Carbon::parse($karyawan->face_updated_at);
            $nextUpdate = $lastUpdate->addHour();
            if ($nextUpdate->gt(now())) {
                $minutes = now()->diffInMinutes($nextUpdate);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda baru bisa memperbarui data wajah ' . $minutes . ' menit lagi.'
                ]);
            }
        }

        $update = DB::table('karyawan')->where('nik', $nik)->update([
            'face_descriptor' => $descriptor,
            'face_updated_at' => now()
        ]);

        if ($update !== false) {
            return response()->json([
                'status' => 'success',
                'message' => 'Face ID berhasil didaftarkan'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendaftarkan Face ID'
            ]);
        }
    }

    public function confirmQuickApproveIzin($token)
    {
        $dataizin = DB::table('pengajuan_izin')->where('quick_approve_token', $token)->first();
        if (!$dataizin) {
            return $this->showQuickApproveResultPage(false, 'Tautan Tidak Valid', 'Tautan persetujuan tidak ditemukan atau tidak valid.');
        }

        $karyawan = DB::table('karyawan')->where('nik', $dataizin->nik)->first();
        $nama_guru = $karyawan ? $karyawan->nama_lengkap : 'Guru';
        $jabatan = $karyawan ? $karyawan->jabatan : '-';
        
        $kode_cabang = $karyawan ? $karyawan->kode_cabang : '';
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $nama_cabang = $cabang ? $cabang->nama_cabang : '-';

        // Check if already processed
        if ($dataizin->status_approved != '0') {
            $statusText = $dataizin->status_approved == 1 ? 'DISETUJUI' : 'DITOLAK';
            $processedBy = $dataizin->approved_by ?? 'Admin';
            
            return $this->showQuickApproveResultPage(
                true, 
                'Pengajuan Sudah Diproses', 
                "Pengajuan izin/sakit/cuti dari <strong>{$nama_guru}</strong> telah diproses sebelumnya oleh <strong>{$processedBy}</strong> dengan status akhir <strong>{$statusText}</strong>.",
                'info'
            );
        }

        // Calculate total days
        $jml_hari = hitunghari($dataizin->tgl_izin_dari, $dataizin->tgl_izin_sampai);

        // Map status type
        $jenisMap = ['i' => 'Izin', 's' => 'Sakit', 'c' => 'Cuti'];
        $jenisText = $jenisMap[$dataizin->status] ?? 'Pengajuan';
        
        // Color themes based on type
        $badgeColors = [
            'i' => ['bg' => '#fef3c7', 'text' => '#d97706', 'border' => '#fcd34d'], // yellow
            's' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#fca5a5'], // red
            'c' => ['bg' => '#e0f2fe', 'text' => '#0284c7', 'border' => '#bae6fd'], // blue
        ];
        $colors = $badgeColors[$dataizin->status] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'];

        // Attachment link
        $attachmentButton = '';
        if (!empty($dataizin->doc_sid)) {
            $attachmentUrl = asset('storage/uploads/sid/' . $dataizin->doc_sid);
            $attachmentButton = "
                <div style='margin-bottom: 20px; text-align: left;'>
                    <span style='font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px; letter-spacing: 0.5px;'>Lampiran / Surat Dokter</span>
                    <a href='{$attachmentUrl}' target='_blank' style='display: inline-flex; align-items: center; text-decoration: none; background: #f8fafc; border: 1px solid #e2e8f0; color: #4f46e5; padding: 10px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all 0.2s;'>
                        <svg xmlns='http://www.w3.org/2000/svg' style='margin-right: 8px;' width='16' height='16' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M15 7l-6.5 6.5a3.5 3.5 0 0 0 5 5l6.5 -6.5a5.5 5.5 0 0 0 -7.5 -7.5l-6.5 6.5a7.5 7.5 0 0 0 10.5 10.5l6.5 -6.5'></path></svg>
                        Lihat Surat Dokter
                    </a>
                </div>";
        }

        $formUrl = url("/quick-approve/izin/{$token}" . (request()->has('adm') ? '?adm=' . request('adm') : ''));

        return "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Konfirmasi Pengajuan {$jenisText} - SiGuru</title>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    background-color: #f8fafc;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .container {
                    width: 100%;
                    max-width: 480px;
                    padding: 20px;
                }
                .card {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 10px 40px rgba(15, 23, 42, 0.04), 0 2px 4px rgba(15, 23, 42, 0.02);
                    padding: 32px 24px;
                    border: 1px solid #e2e8f0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 28px;
                }
                .brand-logo {
                    font-size: 13px;
                    font-weight: 700;
                    color: #4f46e5;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    margin-bottom: 8px;
                    display: block;
                }
                .header-title {
                    font-size: 20px;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0;
                }
                .badge-type {
                    display: inline-block;
                    background-color: {$colors['bg']};
                    color: {$colors['text']};
                    border: 1px solid {$colors['border']};
                    padding: 4px 12px;
                    border-radius: 9999px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    margin-top: 8px;
                }
                .teacher-info {
                    display: flex;
                    align-items: center;
                    background-color: #f8fafc;
                    border-radius: 12px;
                    padding: 16px;
                    margin-bottom: 24px;
                    border: 1px solid #f1f5f9;
                }
                .avatar {
                    background-color: #e0e7ff;
                    color: #4f46e5;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-weight: 700;
                    font-size: 16px;
                    margin-right: 14px;
                }
                .teacher-details {
                    flex-grow: 1;
                }
                .teacher-name {
                    font-size: 15px;
                    font-weight: 600;
                    color: #1e293b;
                    margin: 0 0 2px 0;
                }
                .teacher-sub {
                    font-size: 12px;
                    color: #64748b;
                    margin: 0;
                }
                .detail-section {
                    margin-bottom: 20px;
                    text-align: left;
                }
                .detail-label {
                    font-size: 11px;
                    font-weight: 600;
                    color: #94a3b8;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                    display: block;
                }
                .detail-value {
                    font-size: 14px;
                    font-weight: 500;
                    color: #334155;
                    margin: 0;
                }
                .detail-reason {
                    background: #fafafb;
                    border-left: 3px solid #e2e8f0;
                    padding: 12px 16px;
                    font-style: italic;
                    font-size: 13px;
                    color: #475569;
                    margin-top: 4px;
                    border-radius: 0 8px 8px 0;
                }
                .actions {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                    margin-top: 32px;
                }
                .btn {
                    padding: 14px;
                    border-radius: 12px;
                    font-size: 14px;
                    font-weight: 600;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s;
                    border: none;
                    outline: none;
                }
                .btn-approve {
                    background-color: #10b981;
                    color: white;
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
                }
                .btn-approve:hover {
                    background-color: #059669;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
                }
                .btn-reject {
                    background-color: #ef4444;
                    color: white;
                    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
                }
                .btn-reject:hover {
                    background-color: #dc2626;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25);
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='card'>
                    <div class='header'>
                        <span class='brand-logo'>SiGuru Attendance</span>
                        <h2 class='header-title'>Detail Pengajuan</h2>
                        <span class='badge-type'>{$jenisText}</span>
                    </div>

                    <div class='teacher-info'>
                        <div class='avatar'>" . strtoupper(substr($nama_guru, 0, 1)) . "</div>
                        <div class='teacher-details'>
                            <h3 class='teacher-name'>{$nama_guru}</h3>
                            <p class='teacher-sub'>{$jabatan} &bull; {$nama_cabang}</p>
                        </div>
                    </div>

                    <div class='detail-section'>
                        <span class='detail-label'>Periode Izin</span>
                        <p class='detail-value'>" . date('d M Y', strtotime($dataizin->tgl_izin_dari)) . " s/d " . date('d M Y', strtotime($dataizin->tgl_izin_sampai)) . " <strong>({$jml_hari} Hari)</strong></p>
                    </div>

                    <div class='detail-section'>
                        <span class='detail-label'>Alasan Keterangan</span>
                        <div class='detail-reason'>\"{$dataizin->keterangan}\"</div>
                    </div>

                    {$attachmentButton}

                    <form action='{$formUrl}' method='POST'>
                        <div class='actions'>
                            <button type='submit' name='status' value='1' class='btn btn-approve'>Setujui</button>
                            <button type='submit' name='status' value='2' class='btn btn-reject'>Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </body>
        </html>";
    }

    public function quickApproveIzin(Request $request, $token)
    {
        $status = $request->status;
        $dataizin = DB::table('pengajuan_izin')->where('quick_approve_token', $token)->first();
        if (!$dataizin) {
            return $this->showQuickApproveResultPage(false, 'Tautan Tidak Valid', 'Tautan persetujuan tidak ditemukan atau tidak valid.');
        }

        $karyawan = DB::table('karyawan')->where('nik', $dataizin->nik)->first();
        $nama_guru = $karyawan ? $karyawan->nama_lengkap : 'Guru';

        // Check if already processed
        if ($dataizin->status_approved != '0') {
            $statusText = $dataizin->status_approved == 1 ? 'DISETUJUI' : 'DITOLAK';
            $processedBy = $dataizin->approved_by ?? 'Admin';
            
            return $this->showQuickApproveResultPage(
                true, 
                'Pengajuan Sudah Diproses', 
                "Pengajuan izin/sakit/cuti dari <strong>{$nama_guru}</strong> telah diproses sebelumnya oleh <strong>{$processedBy}</strong> dengan status akhir <strong>{$statusText}</strong>.",
                'info'
            );
        }

        // Fetch dynamic admin name from wa_gateway_admin_names
        $namesConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admin_names')->first();
        $names = [];
        if ($namesConfig) {
            $names = strpos($namesConfig->value, '|') !== false 
                ? explode('|', $namesConfig->value) 
                : explode(',', $namesConfig->value);
        }
        $admIndex = (int)request('adm', 1) - 1;
        $admName = isset($names[$admIndex]) ? trim($names[$admIndex]) : 'Admin';

        // Process the approval
        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('quick_approve_token', $token)->update([
                'status_approved' => $status,
                'approved_by'     => $admName . ' (via WhatsApp)'
            ]);
            DB::commit();

            // Send WA Notification to Teacher
            try {
                if ($karyawan && !empty($karyawan->no_hp)) {
                    $jenisMap = ['i' => 'Izin', 's' => 'Sakit', 'c' => 'Cuti'];
                    $jenisText = $jenisMap[$dataizin->status] ?? 'Pengajuan';
                    $statusText = $status == 1 ? 'DISETUJUI' : 'DITOLAK';
                    
                    $message = "*NOTIFIKASI STATUS PENGAJUAN*\n\n"
                        . "Halo " . $nama_guru . ",\n"
                        . "Pengajuan " . $jenisText . " Anda telah selesai diproses oleh *{$admName}*:\n\n"
                        . "Jenis: " . $jenisText . "\n"
                        . "Periode: " . date('d-m-Y', strtotime($dataizin->tgl_izin_dari)) . " s/d " . date('d-m-Y', strtotime($dataizin->tgl_izin_sampai)) . "\n"
                        . "Status: " . $statusText . "\n\n"
                        . "Terima kasih atas konfirmasi Anda.";
                    
                    sendWaNotification($karyawan->no_hp, $message);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal mengirim notifikasi status izin WA: " . $e->getMessage());
            }

            $finalStatusText = $status == 1 ? 'DISETUJUI' : 'DITOLAK';
            return $this->showQuickApproveResultPage(
                true, 
                'Berhasil Diproses!', 
                "Pengajuan izin/sakit/cuti dari <strong>{$nama_guru}</strong> telah berhasil <strong>{$finalStatusText}</strong>!",
                'success'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->showQuickApproveResultPage(false, 'Gagal Diproses', 'Detail Error: ' . $e->getMessage());
        }
    }

    public function confirmQuickApproveKehadiran($token)
    {
        $pengajuan = DB::table('konfirmasi_kehadiran')->where('quick_approve_token', $token)->first();
        if (!$pengajuan) {
            return $this->showQuickApproveResultPage(false, 'Tautan Tidak Valid', 'Tautan persetujuan tidak ditemukan atau tidak valid.');
        }

        $karyawan = DB::table('karyawan')->where('nik', $pengajuan->nik)->first();
        $nama_guru = $karyawan ? $karyawan->nama_lengkap : 'Guru';
        $jabatan = $karyawan ? $karyawan->jabatan : '-';
        
        $kode_cabang = $karyawan ? $karyawan->kode_cabang : '';
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $nama_cabang = $cabang ? $cabang->nama_cabang : '-';

        // Check if already processed
        if ($pengajuan->status !== 'pending') {
            $statusText = $pengajuan->status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
            $processedBy = $pengajuan->approved_by ?? 'Admin';
            $processedAt = $pengajuan->updated_at ? date('d-m-Y H:i', strtotime($pengajuan->updated_at)) : '-';
            
            return $this->showQuickApproveResultPage(
                true, 
                'Pengajuan Sudah Diproses', 
                "Pengajuan konfirmasi kehadiran dari <strong>{$nama_guru}</strong> telah diproses sebelumnya oleh <strong>{$processedBy}</strong> pada <strong>{$processedAt} WIB</strong> dengan status akhir <strong>{$statusText}</strong>.",
                'info'
            );
        }

        // Attachment link
        $attachmentButton = '';
        if (!empty($pengajuan->bukti)) {
            $attachmentUrl = asset('storage/uploads/konfirmasi_kehadiran/' . $pengajuan->bukti);
            $attachmentButton = "
                <div style='margin-bottom: 20px; text-align: left;'>
                    <span style='font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px; letter-spacing: 0.5px;'>Bukti Pendukung</span>
                    <a href='{$attachmentUrl}' target='_blank' style='display: inline-flex; align-items: center; text-decoration: none; background: #f8fafc; border: 1px solid #e2e8f0; color: #4f46e5; padding: 10px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all 0.2s;'>
                        <svg xmlns='http://www.w3.org/2000/svg' style='margin-right: 8px;' width='16' height='16' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M15 7l-6.5 6.5a3.5 3.5 0 0 0 5 5l6.5 -6.5a5.5 5.5 0 0 0 -7.5 -7.5l-6.5 6.5a7.5 7.5 0 0 0 10.5 10.5l6.5 -6.5'></path></svg>
                        Lihat Dokumen Bukti
                    </a>
                </div>";
        }

        $formUrl = url("/quick-approve/kehadiran/{$token}" . (request()->has('adm') ? '?adm=' . request('adm') : ''));

        return "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Konfirmasi Kehadiran - SiGuru</title>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    background-color: #f8fafc;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .container {
                    width: 100%;
                    max-width: 480px;
                    padding: 20px;
                }
                .card {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 10px 40px rgba(15, 23, 42, 0.04), 0 2px 4px rgba(15, 23, 42, 0.02);
                    padding: 32px 24px;
                    border: 1px solid #e2e8f0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 28px;
                }
                .brand-logo {
                    font-size: 13px;
                    font-weight: 700;
                    color: #4f46e5;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    margin-bottom: 8px;
                    display: block;
                }
                .header-title {
                    font-size: 20px;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0;
                }
                .badge-type {
                    display: inline-block;
                    background-color: #e0f2fe;
                    color: #0369a1;
                    border: 1px solid #bae6fd;
                    padding: 4px 12px;
                    border-radius: 9999px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    margin-top: 8px;
                }
                .teacher-info {
                    display: flex;
                    align-items: center;
                    background-color: #f8fafc;
                    border-radius: 12px;
                    padding: 16px;
                    margin-bottom: 24px;
                    border: 1px solid #f1f5f9;
                }
                .avatar {
                    background-color: #e0e7ff;
                    color: #4f46e5;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-weight: 700;
                    font-size: 16px;
                    margin-right: 14px;
                }
                .teacher-details {
                    flex-grow: 1;
                }
                .teacher-name {
                    font-size: 15px;
                    font-weight: 600;
                    color: #1e293b;
                    margin: 0 0 2px 0;
                }
                .teacher-sub {
                    font-size: 12px;
                    color: #64748b;
                    margin: 0;
                }
                .detail-section {
                    margin-bottom: 20px;
                    text-align: left;
                }
                .detail-label {
                    font-size: 11px;
                    font-weight: 600;
                    color: #94a3b8;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                    display: block;
                }
                .detail-value {
                    font-size: 14px;
                    font-weight: 500;
                    color: #334155;
                    margin: 0;
                }
                .detail-reason {
                    background: #fafafb;
                    border-left: 3px solid #e2e8f0;
                    padding: 12px 16px;
                    font-style: italic;
                    font-size: 13px;
                    color: #475569;
                    margin-top: 4px;
                    border-radius: 0 8px 8px 0;
                }
                .actions {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                    margin-top: 32px;
                }
                .btn {
                    padding: 14px;
                    border-radius: 12px;
                    font-size: 14px;
                    font-weight: 600;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s;
                    border: none;
                    outline: none;
                }
                .btn-approve {
                    background-color: #10b981;
                    color: white;
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
                }
                .btn-approve:hover {
                    background-color: #059669;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
                }
                .btn-reject {
                    background-color: #ef4444;
                    color: white;
                    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
                }
                .btn-reject:hover {
                    background-color: #dc2626;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25);
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='card'>
                    <div class='header'>
                        <span class='brand-logo'>SiGuru Attendance</span>
                        <h2 class='header-title'>Detail Pengajuan</h2>
                        <span class='badge-type'>Konfirmasi Kehadiran</span>
                    </div>

                    <div class='teacher-info'>
                        <div class='avatar'>" . strtoupper(substr($nama_guru, 0, 1)) . "</div>
                        <div class='teacher-details'>
                            <h3 class='teacher-name'>{$nama_guru}</h3>
                            <p class='teacher-sub'>{$jabatan} &bull; {$nama_cabang}</p>
                        </div>
                    </div>

                    <div class='detail-section'>
                        <span class='detail-label'>Tanggal Kehadiran</span>
                        <p class='detail-value'>" . date('d M Y', strtotime($pengajuan->tanggal)) . "</p>
                    </div>

                    <div class='detail-section'>
                        <span class='detail-label'>Waktu Presensi</span>
                        <p class='detail-value'>{$pengajuan->jam_masuk} s/d {$pengajuan->jam_keluar}</p>
                    </div>

                    <div class='detail-section'>
                        <span class='detail-label'>Alasan Keterangan</span>
                        <div class='detail-reason'>\"{$pengajuan->alasan}\"</div>
                    </div>

                    {$attachmentButton}

                    <form action='{$formUrl}' method='POST'>
                        <div class='actions'>
                            <button type='submit' name='status' value='approved' class='btn btn-approve'>Setujui</button>
                            <button type='submit' name='status' value='rejected' class='btn btn-reject'>Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </body>
        </html>";
    }

    public function quickApproveKehadiran(Request $request, $token)
    {
        $status = $request->status;
        if ($status !== 'approved' && $status !== 'rejected') {
            return $this->showQuickApproveResultPage(false, 'Status Tidak Valid', 'Aksi status persetujuan tidak valid.');
        }

        $pengajuan = DB::table('konfirmasi_kehadiran')->where('quick_approve_token', $token)->first();
        if (!$pengajuan) {
            return $this->showQuickApproveResultPage(false, 'Tautan Tidak Valid', 'Tautan persetujuan tidak ditemukan atau tidak valid.');
        }

        $karyawan = DB::table('karyawan')->where('nik', $pengajuan->nik)->first();
        $nama_guru = $karyawan ? $karyawan->nama_lengkap : 'Guru';

        // Check if already processed
        if ($pengajuan->status !== 'pending') {
            $statusText = $pengajuan->status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
            $processedBy = $pengajuan->approved_by ?? 'Admin';
            $processedAt = $pengajuan->updated_at ? date('d-m-Y H:i', strtotime($pengajuan->updated_at)) : '-';
            
            return $this->showQuickApproveResultPage(
                true, 
                'Pengajuan Sudah Diproses', 
                "Pengajuan konfirmasi kehadiran dari <strong>{$nama_guru}</strong> telah diproses sebelumnya oleh <strong>{$processedBy}</strong> pada <strong>{$processedAt} WIB</strong> dengan status akhir <strong>{$statusText}</strong>.",
                'info'
            );
        }

        // Fetch dynamic admin name from wa_gateway_admin_names
        $namesConfig = DB::table('konfigurasi_app')->where('key', 'wa_gateway_admin_names')->first();
        $names = [];
        if ($namesConfig) {
            $names = strpos($namesConfig->value, '|') !== false 
                ? explode('|', $namesConfig->value) 
                : explode(',', $namesConfig->value);
        }
        $admIndex = (int)request('adm', 1) - 1;
        $admName = isset($names[$admIndex]) ? trim($names[$admIndex]) : 'Admin';

        if ($status === 'approved') {
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

                DB::table('konfirmasi_kehadiran')->where('id', $pengajuan->id)->update([
                    'status'       => 'approved',
                    'approved_by'  => $admName . ' (via WhatsApp)',
                    'updated_at'   => now(),
                ]);

                DB::commit();

                // Send WA Notification to Teacher
                try {
                    if ($karyawan && !empty($karyawan->no_hp)) {
                        $message = "*NOTIFIKASI STATUS KONFIRMASI KEHADIRAN*\n\n"
                            . "Halo " . $nama_guru . ",\n"
                            . "Pengajuan konfirmasi kehadiran Anda telah selesai diproses oleh *{$admName}*:\n\n"
                            . "Tanggal: " . date('d-m-Y', strtotime($pengajuan->tanggal)) . "\n"
                            . "Status: DISETUJUI\n"
                            . "Catatan: Diproses via WhatsApp Quick Link\n\n"
                            . "Terima kasih atas konfirmasi Anda.";
                        
                        sendWaNotification($karyawan->no_hp, $message);
                    }
                } catch (\Exception $e) {
                    \Log::error("Gagal mengirim notifikasi status konfirmasi kehadiran WA: " . $e->getMessage());
                }

                return $this->showQuickApproveResultPage(
                    true, 
                    'Berhasil Disetujui!', 
                    "Pengajuan konfirmasi kehadiran dari <strong>{$nama_guru}</strong> telah berhasil <strong>DISETUJUI</strong> dan data kehadiran telah dicatat.",
                    'success'
                );
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->showQuickApproveResultPage(false, 'Gagal Memproses', 'Detail Error: ' . $e->getMessage());
            }
        } else {
            // Reject
            try {
                DB::table('konfirmasi_kehadiran')->where('id', $pengajuan->id)->update([
                    'status'        => 'rejected',
                    'approved_by'   => $admName . ' (via WhatsApp)',
                    'updated_at'    => now(),
                ]);

                // Send WA Notification to Teacher
                try {
                    if ($karyawan && !empty($karyawan->no_hp)) {
                        $message = "*NOTIFIKASI STATUS KONFIRMASI KEHADIRAN*\n\n"
                            . "Halo " . $nama_guru . ",\n"
                            . "Pengajuan konfirmasi kehadiran Anda telah selesai diproses oleh *{$admName}*:\n\n"
                            . "Tanggal: " . date('d-m-Y', strtotime($pengajuan->tanggal)) . "\n"
                            . "Status: DITOLAK\n"
                            . "Catatan: Diproses via WhatsApp Quick Link\n\n"
                            . "Terima kasih atas konfirmasi Anda.";
                        
                        sendWaNotification($karyawan->no_hp, $message);
                    }
                } catch (\Exception $e) {
                    \Log::error("Gagal mengirim notifikasi status konfirmasi kehadiran WA: " . $e->getMessage());
                }

                return $this->showQuickApproveResultPage(
                    true, 
                    'Berhasil Ditolak!', 
                    "Pengajuan konfirmasi kehadiran dari <strong>{$nama_guru}</strong> telah <strong>DITOLAK</strong>.",
                    'success'
                );
            } catch (\Exception $e) {
                return $this->showQuickApproveResultPage(false, 'Gagal Memproses', 'Detail Error: ' . $e->getMessage());
            }
        }
    }

    private function showQuickApproveResultPage($status, $title, $description, $type = 'error')
    {
        $themeColor = '#d63939'; // red for error
        $bgColor = '#fef2f2';
        $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="#d63939" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';

        if ($status && $type === 'success') {
            $themeColor = '#2fb344'; // green
            $bgColor = '#f0fdf4';
            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="#2fb344" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><path d="M9 12l2 2l4 -4"></path></svg>';
        } elseif ($status && $type === 'info') {
            $themeColor = '#4299e1'; // blue
            $bgColor = '#eff6ff';
            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="#4299e1" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
        }

        return "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title} - SiGuru</title>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    background-color: #f4f6fa;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .card {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                    max-width: 450px;
                    width: 90%;
                    padding: 32px 24px;
                    text-align: center;
                    border-top: 5px solid {$themeColor};
                }
                .icon-container {
                    background-color: {$bgColor};
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 0 auto 24px;
                }
                .title {
                    font-size: 20px;
                    font-weight: 700;
                    color: #1e293b;
                    margin-bottom: 12px;
                }
                .description {
                    font-size: 14px;
                    color: #64748b;
                    line-height: 1.6;
                    margin-bottom: 24px;
                }
                .brand {
                    font-size: 12px;
                    font-weight: 600;
                    color: #94a3b8;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='icon-container'>
                    {$iconSvg}
                </div>
                <div class='title'>{$title}</div>
                <div class='description'>{$description}</div>
                <div class='brand'>SiGuru Attendance Portal</div>
            </div>
        </body>
        </html>
        ";
    }

    public function rekapabsensiguru(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                     "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $presensi = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . sprintf('%02d', $bulan) . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $presensi_array = $presensi->toArray();
        $start_date = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $current = $start_date;
        while (strtotime($current) <= strtotime($end_date)) {
            $exists = false;
            foreach ($presensi_array as $p) {
                if ($p->tgl_presensi === $current) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $ceklibur = cekHariLiburPegawai($nik, $current);
                if ($ceklibur) {
                    $dummy = new \stdClass();
                    $dummy->tgl_presensi = $current;
                    $dummy->status = 'libur';
                    $dummy->keterangan = $ceklibur->keterangan;
                    $dummy->jam_in = '-';
                    $dummy->jam_out = '-';
                    $dummy->jam_masuk = null;
                    $dummy->jam_pulang = null;
                    $presensi_array[] = $dummy;
                }
            }
            $current = date("Y-m-d", strtotime("+1 day", strtotime($current)));
        }

        usort($presensi_array, function($a, $b) {
            return strcmp($a->tgl_presensi, $b->tgl_presensi);
        });

        $presensi = collect($presensi_array);

        return view('presensi.rekapabsensiguru', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function downloadrekapabsensiguru(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                     "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $presensi = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . sprintf('%02d', $bulan) . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $presensi_array = $presensi->toArray();
        $start_date = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $current = $start_date;
        while (strtotime($current) <= strtotime($end_date)) {
            $exists = false;
            foreach ($presensi_array as $p) {
                if ($p->tgl_presensi === $current) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $ceklibur = cekHariLiburPegawai($nik, $current);
                if ($ceklibur) {
                    $dummy = new \stdClass();
                    $dummy->tgl_presensi = $current;
                    $dummy->status = 'libur';
                    $dummy->keterangan = $ceklibur->keterangan;
                    $dummy->jam_in = '-';
                    $dummy->jam_out = '-';
                    $dummy->jam_masuk = null;
                    $dummy->jam_pulang = null;
                    $presensi_array[] = $dummy;
                }
            }
            $current = date("Y-m-d", strtotime("+1 day", strtotime($current)));
        }

        usort($presensi_array, function($a, $b) {
            return strcmp($a->tgl_presensi, $b->tgl_presensi);
        });

        $presensi = collect($presensi_array);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('presensi.cetaklaporan_pdf', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Laporan_Absensi_{$karyawan->nama_lengkap}_{$bulan}_{$tahun}.pdf");
    }
}
