<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Departemen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Pengabdian;


class PayrollController extends Controller
{


    /** ==================== PAYROLL GURU ==================== **/

public function setingGajiGuru()
{
    $settingGaji = DB::table('setting_gaji_guru')->get();

    return view('payroll.guru.seting_gaji', compact('settingGaji'));
}

public function simpanSetingGajiGuru(Request $request)
{
    $golongan = $request->golongan ?? [];
    $transport = $request->transport ?? [];
    $simpanan_koperasi = $request->simpanan_koperasi ?? [];
    $dana_sosial = $request->dana_sosial ?? [];
    $gaji_pokok = $request->gaji_pokok ?? [];

    // Validasi apakah golongan tidak kosong
    if (empty($golongan)) {
        return redirect()->back()->with('error', 'Data golongan tidak boleh kosong!');
    }

    foreach ($golongan as $index => $g) {
        DB::table('setting_gaji_guru')->updateOrInsert(
            ['golongan' => $g],
            [
                'transport' => $this->convertToNumber($transport[$index] ?? '0'),
                'simpanan_koperasi' => $this->convertToNumber($simpanan_koperasi[$index] ?? '0'),
                'dana_sosial' => $this->convertToNumber($dana_sosial[$index] ?? '0'),
                'gaji_pokok' => $this->convertToNumber($gaji_pokok[$index] ?? '0'),
                'updated_at' => now()
            ]
        );
    }

    return redirect()->back()->with('success', 'Pengaturan gaji per golongan berhasil disimpan!');
}

// Method convertToNumber
private function convertToNumber($value)
{
    if (empty($value) || is_null($value)) {
        return 0;
    }
    return (int) str_replace('.', '', $value);
}

public function dataJabatanGuru()
{
        $departemen = Departemen::orderBy('nama_dept')->get();
        return view('payroll.guru.jabatan', compact('departemen'));
}

public function dataGuru(Request $request)
{
    $bulanSekarang = date('m');
    $tahunSekarang = date('Y');

    $guru = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->leftJoin('setting_gaji_guru as sg', 'sg.golongan', '=', 'guru.golongan')
        ->select(
            'guru.id', 'guru.karyawan_nik', 'guru.lokasi_sekolah', 'guru.jabatan',
            'guru.golongan', 'guru.tunjangan_jabatan', 'guru.jam_mengajar', 'guru.potongan',
            'guru.created_at', 'guru.updated_at', 'karyawan.nama_lengkap',
            'sg.transport', 'sg.simpanan_koperasi', 'sg.dana_sosial', 'sg.gaji_pokok'
        )
        ->when($request->has('lokasi_filter') && $request->lokasi_filter != '', 
            fn($query) => $query->where('guru.lokasi_sekolah', $request->lokasi_filter))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($query) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $query->where('guru.lokasi_sekolah', $cabang ? $cabang->nama_cabang : '');
        })
        ->orderBy('karyawan.nama_lengkap', 'asc')
        ->get();

    $guru = $guru->map(function($g) use ($bulanSekarang, $tahunSekarang) {
        $dari = "$tahunSekarang-" . str_pad($bulanSekarang, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        
        $presensiData = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $g->karyawan_nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->select('presensi.tgl_presensi', 'presensi.status')
            ->get();
        
        $g->kehadiran = $presensiData->where('status', 'h')->pluck('tgl_presensi')->unique()->count();
        return $g;
    });

    $karyawan = Karyawan::orderBy('nama_lengkap')->get();
    $jabatan = Departemen::orderBy('nama_dept')->get();

    return view('payroll.guru.data_guru', compact('guru', 'karyawan', 'jabatan'));
}

public function storeGuru(Request $request)
{
    $request->validate([
        'karyawan_id' => 'required',
        'lokasi_sekolah' => 'required',
        'jabatan' => 'required',
        'golongan' => 'required|numeric',
        'jam_mengajar' => 'required|numeric'
    ]);

    $existingGuru = DB::table('guru')->where('karyawan_nik', $request->karyawan_id)->first();

    if ($existingGuru) {
        return redirect()->back()->with('error', 'Guru dengan username tersebut sudah terdaftar!');
    }

    DB::table('guru')->insert([
        'karyawan_nik' => $request->karyawan_id,
        'lokasi_sekolah' => $request->lokasi_sekolah,
        'jabatan' => $request->jabatan,
        'golongan' => $request->golongan,
        'tunjangan_jabatan' => str_replace(['Rp', '.', ' '], '', $request->tunjangan_jabatan) ?: 0,
        'jam_mengajar' => $request->jam_mengajar,
        'potongan' => str_replace(['Rp', '.', ' '], '', $request->potongan) ?: 0, // ✅ tambahan
        'created_at' => now()
    ]);

    return redirect()->back()->with('success', 'Data guru berhasil ditambahkan!');
}

public function editGuru($id)
{
    $guru = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->select('guru.*', 'karyawan.nama_lengkap')
        ->where('guru.id', $id)
        ->first();

    $jabatan = DB::table('departemen')->get(); // Ambil data jabatan

    return view('payroll.guru.edit', compact('guru', 'jabatan'));
}

public function updateGuru(Request $request, $id)
{
    $request->validate([
        'lokasi_sekolah'   => 'required',
        'jabatan'          => 'required',
        'golongan'         => 'required|numeric',
        'jam_mengajar'     => 'required|numeric'
    ]);

    DB::table('guru')->where('id', $id)->update([
        'lokasi_sekolah'     => $request->lokasi_sekolah,
        'jabatan'            => $request->jabatan,
        'golongan'           => $request->golongan,
        'tunjangan_jabatan'  => str_replace(['Rp', '.', ' '], '', $request->tunjangan_jabatan) ?: 0,
        'jam_mengajar'       => $request->jam_mengajar,
        'potongan'           => str_replace(['Rp', '.', ' '], '', $request->potongan) ?: 0,
        'updated_at'         => now()
    ]);

    return redirect()->route('payroll.guru.data')
                     ->with('success', 'Data guru berhasil diperbarui!');
}
public function deleteGuru($id)
    {
        DB::table('guru')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data guru berhasil dihapus!');
}

public function rekapGajiGuru(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $rekap = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->leftJoin('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
        ->select(
            'guru.id',
            'guru.karyawan_nik',
            'guru.lokasi_sekolah',
            'guru.jabatan',
            'guru.tunjangan_jabatan',
            'guru.jam_mengajar as tjm',
            'guru.golongan',
            'guru.potongan',
            'karyawan.nama_lengkap',
            DB::raw('COALESCE(setting_gaji_guru.gaji_pokok, 0) as gaji_pokok'),
            DB::raw('COALESCE(setting_gaji_guru.transport, 0) as transport'),
            DB::raw('COALESCE(setting_gaji_guru.simpanan_koperasi, 0) as simpanan_koperasi'),
            DB::raw('COALESCE(setting_gaji_guru.dana_sosial, 0) as dana_sosial')
        )
        ->when($request->lokasi, fn($query) => $query->where('guru.lokasi_sekolah', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($query) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $query->where('guru.lokasi_sekolah', $cabang ? $cabang->nama_cabang : '');
        })
        ->when($request->golongan, fn($query) => $query->where('guru.golongan', $request->golongan))
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    // SINKRONISASI: Query yang SAMA PERSIS dengan laporan rekap absensi
    $rekap = $rekap->map(function($guru) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        
        // Query PERSIS SAMA dengan PresensiController->cetakrekap()
        $presensiData = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $guru->karyawan_nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->select('presensi.tgl_presensi', 'presensi.status')
            ->get();
        
        // Hitung hanya yang status 'h' (seperti di view rekap yang dihitung sebagai kehadiran)
        $hadir = $presensiData->where('status', 'h')->pluck('tgl_presensi')->unique()->count();
        
        // Perhitungan gaji
        $jml_gaji_pokok = $guru->tjm * $guru->gaji_pokok;
        $jml_transport = $hadir * $guru->transport;
        $total_honor = $jml_gaji_pokok + $jml_transport + $guru->tunjangan_jabatan;
        $total_potongan = $guru->dana_sosial + $guru->simpanan_koperasi + $guru->potongan;
        $total_gaji = $total_honor - $total_potongan;
        
        $guru->hadir = $hadir;
        $guru->jml_gaji_pokok = $jml_gaji_pokok;
        $guru->jml_transport = $jml_transport;
        $guru->total_honor = $total_honor;
        $guru->total_potongan = $total_potongan;
        $guru->total_gaji = $total_gaji;
        
        return $guru;
    });

    $summary = [
        'total_jam_mengajar' => $rekap->sum('tjm'),
        'total_honor_mengajar' => $rekap->sum('jml_gaji_pokok'),
        'total_transport' => $rekap->sum('jml_transport'),
        'total_tunjangan' => $rekap->sum('tunjangan_jabatan'),
        'total_potongan' => $rekap->sum('total_potongan'),
        'total_gaji' => $rekap->sum('total_gaji')
    ];

    return view('payroll.guru.rekap_gaji', [
        'rekap' => $rekap,
        'summary' => $summary,
        'bulan' => $bulan,
        'tahun' => $tahun,
    ]);
}

public function cetakRekapGajiGuru(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $rekap = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->leftJoin('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
        ->select(
            'guru.id',
            'guru.karyawan_nik',
            'guru.lokasi_sekolah',
            'guru.jabatan',
            'guru.tunjangan_jabatan',
            'guru.jam_mengajar as tjm',
            'guru.golongan',
            'karyawan.nama_lengkap',
            DB::raw('COALESCE(setting_gaji_guru.gaji_pokok,0) as gaji_pokok'),
            DB::raw('COALESCE(setting_gaji_guru.transport,0) as transport'),
            DB::raw('COALESCE(setting_gaji_guru.dana_sosial,0) as dana_sosial'),
            DB::raw('COALESCE(setting_gaji_guru.simpanan_koperasi,0) as simpanan_koperasi'),
            DB::raw('COALESCE(guru.potongan,0) as potongan')
        )
        ->when($request->lokasi, fn($q) => $q->where('guru.lokasi_sekolah', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($q) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $q->where('guru.lokasi_sekolah', $cabang ? $cabang->nama_cabang : '');
        })
        ->when($request->golongan, fn($q) => $q->where('guru.golongan', $request->golongan))
        ->orderBy('karyawan.nama_lengkap', 'asc')
        ->get();

    $rekap = $rekap->map(function($guru) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        
        $presensiData = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $guru->karyawan_nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->select('presensi.tgl_presensi', 'presensi.status')
            ->get();
        
        $hadir = $presensiData->where('status', 'h')->pluck('tgl_presensi')->unique()->count();
        
        $jml_gaji_pokok = $guru->tjm * $guru->gaji_pokok;
        $jml_transport = $hadir * $guru->transport;
        $total_gaji = ($jml_gaji_pokok + $jml_transport + $guru->tunjangan_jabatan)
                    - ($guru->dana_sosial + $guru->simpanan_koperasi + $guru->potongan);
        
        $guru->hadir = $hadir;
        $guru->jml_gaji_pokok = $jml_gaji_pokok;
        $guru->jml_transport = $jml_transport;
        $guru->total_gaji = $total_gaji;
        
        return $guru;
    });

    $lokasi_name = $request->lokasi;
    if (Auth::guard('user')->user()?->hasRole('staff')) {
        $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
        $lokasi_name = $cabang ? $cabang->nama_cabang : '';
    }

    return view('payroll.guru.cetak', [
        'rekap' => $rekap,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'lokasi' => $lokasi_name,
        'golongan' => $request->golongan
    ]);
}

public function cetakSlip(Request $request)
{
    $bulan = $request->get('bulan', date('m'));
    $tahun = $request->get('tahun', date('Y'));
    
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $rekap = DB::select("
        SELECT 
            g.id AS guru_id,
            k.nama_lengkap,
            g.lokasi_sekolah,
            g.jabatan,
            g.jam_mengajar AS tjm,
            sg.gaji_pokok,
            (g.jam_mengajar * IFNULL(sg.gaji_pokok, 0)) AS jml_gaji_pokok,
            IFNULL(p.jumlah_hadir, 0) AS hadir,
            sg.transport,
            (IFNULL(p.jumlah_hadir, 0) * IFNULL(sg.transport, 0)) AS jml_transport,
            g.tunjangan_jabatan,
            g.potongan,
            sg.dana_sosial,
            sg.simpanan_koperasi,
            (
                (g.jam_mengajar * IFNULL(sg.gaji_pokok, 0)) +
                (IFNULL(p.jumlah_hadir, 0) * IFNULL(sg.transport, 0)) +
                IFNULL(g.tunjangan_jabatan, 0)
                -
                (IFNULL(sg.dana_sosial,0) + IFNULL(sg.simpanan_koperasi,0) + IFNULL(g.potongan,0))
            ) AS total_gaji
        FROM guru g
        JOIN karyawan k ON g.karyawan_nik = k.nik
        LEFT JOIN (
            SELECT nik, COUNT(DISTINCT tgl_presensi) as jumlah_hadir
            FROM presensi
            WHERE MONTH(tgl_presensi) = ?
            AND YEAR(tgl_presensi) = ?
            AND status = 'h'
            GROUP BY nik
        ) p ON k.nik = p.nik
        LEFT JOIN setting_gaji_guru sg ON sg.golongan = g.golongan
        WHERE 1=1
            " . ($request->lokasi ? " AND g.lokasi_sekolah = '" . $request->lokasi . "'" : "") . "
            " . (Auth::guard('user')->user()?->hasRole('staff') ? " AND g.lokasi_sekolah = (SELECT nama_cabang FROM cabang WHERE kode_cabang = '" . Auth::guard('user')->user()->kode_cabang . "')" : "") . "
            " . ($request->golongan ? " AND g.golongan = '" . $request->golongan . "'" : "") . "
        ORDER BY k.nama_lengkap ASC
    ", [$bulan, $tahun]);

    return view('payroll.guru.cetak_slip_all', [
        'rekap' => $rekap,
        'bulan' => (int)$bulan,
        'tahun' => (int)$tahun,
        'bulanIndo' => $bulanIndo,
        'namaBulan' => $bulanIndo[(int)$bulan] ?? 'Tidak Valid',
        'lokasi' => $request->lokasi,
        'golongan' => $request->golongan
    ]);
}

public function downloadPdf(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $namaBulan = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET',
        4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
        7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER',
        10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
    ];

    $rekap = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->leftJoin('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
        ->select(
            'guru.id', 'guru.karyawan_nik', 'guru.lokasi_sekolah', 'guru.jabatan',
            'guru.tunjangan_jabatan', 'guru.jam_mengajar as tjm', 'guru.golongan',
            'guru.potongan', 'karyawan.nama_lengkap',
            DB::raw('COALESCE(setting_gaji_guru.gaji_pokok, 0) as gaji_pokok'),
            DB::raw('COALESCE(setting_gaji_guru.transport, 0) as transport'),
            DB::raw('COALESCE(setting_gaji_guru.simpanan_koperasi, 0) as simpanan_koperasi'),
            DB::raw('COALESCE(setting_gaji_guru.dana_sosial, 0) as dana_sosial')
        )
        ->when($request->lokasi, fn($q) => $q->where('guru.lokasi_sekolah', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'),
            fn($q) => $q->where('guru.lokasi_sekolah', Auth::guard('user')->user()->kode_cabang))
        ->when($request->golongan, fn($q) => $q->where('guru.golongan', $request->golongan))
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    $rekap = $rekap->map(function($guru) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        
        $presensiData = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $guru->karyawan_nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->select('presensi.tgl_presensi', 'presensi.status')
            ->get();
        
        $hadir = $presensiData->where('status', 'h')->pluck('tgl_presensi')->unique()->count();
        
        $jml_gaji_pokok = $guru->tjm * $guru->gaji_pokok;
        $jml_transport = $hadir * $guru->transport;
        $total_gaji = ($jml_gaji_pokok + $jml_transport + $guru->tunjangan_jabatan)
                    - ($guru->dana_sosial + $guru->simpanan_koperasi + $guru->potongan);
        
        $guru->hadir = $hadir;
        $guru->jml_gaji_pokok = $jml_gaji_pokok;
        $guru->jml_transport = $jml_transport;
        $guru->total_gaji = $total_gaji;
        
        return $guru;
    });

    $lokasi_name = $request->lokasi;
    if (Auth::guard('user')->user()?->hasRole('staff')) {
        $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
        $lokasi_name = $cabang ? $cabang->nama_cabang : '';
    }

    $pdf = PDF::loadView('payroll.guru.cetak_pdf', [
        'rekap' => $rekap,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'lokasi' => $lokasi_name,
        'golongan' => $request->golongan,
        'namaBulan' => $namaBulan[$bulan] ?? ''
    ])->setPaper('a4', 'landscape');

    $filename = "REKAP HONORIUM GURU PERIODE {$namaBulan[$bulan]} $tahun.pdf";
    return $pdf->download($filename);
}

public function downloadExcel(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $namaBulan = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET',
        4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
        7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER',
        10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
    ];

    $rekap = DB::table('guru')
        ->join('karyawan', 'guru.karyawan_nik', '=', 'karyawan.nik')
        ->join('setting_gaji_guru', 'guru.golongan', '=', 'setting_gaji_guru.golongan')
        ->select(
            'guru.id', 'guru.karyawan_nik', 'guru.lokasi_sekolah', 'guru.jabatan',
            'guru.tunjangan_jabatan', 'guru.jam_mengajar as tjm', 'guru.golongan',
            'guru.potongan', 'karyawan.nama_lengkap',
            'setting_gaji_guru.gaji_pokok', 'setting_gaji_guru.transport',
            'setting_gaji_guru.dana_sosial'
        )
        ->when($request->lokasi, fn($q) => $q->where('guru.lokasi_sekolah', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'),
            fn($q) => $q->where('guru.lokasi_sekolah', Auth::guard('user')->user()->kode_cabang))
        ->when($request->golongan, fn($q) => $q->where('guru.golongan', $request->golongan))
        ->get();

    $rekap = $rekap->map(function($guru) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        
        $presensiData = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $guru->karyawan_nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->select('presensi.tgl_presensi', 'presensi.status')
            ->get();
        
        $hadir = $presensiData->where('status', 'h')->pluck('tgl_presensi')->unique()->count();
        
        $jml_gaji_pokok = $guru->tjm * $guru->gaji_pokok;
        $jml_transport = $hadir * $guru->transport;
        $total_gaji = ($jml_gaji_pokok + $jml_transport + $guru->tunjangan_jabatan)
                    - ($guru->dana_sosial + ($guru->potongan ?? 0));
        
        $guru->hadir = $hadir;
        $guru->jml_gaji_pokok = $jml_gaji_pokok;
        $guru->jml_transport = $jml_transport;
        $guru->total_gaji = $total_gaji;
        
        return $guru;
    });

    $data = $rekap->map(function ($g, $i) {
        return [
            'No' => $i + 1,
            'Nama' => $g->nama_lengkap,
            'Tunjangan' => $g->tunjangan_jabatan,
            'Wkt' => $g->tjm,
            'Stn' => $g->gaji_pokok,
            'Jml' => $g->jml_gaji_pokok,
            'Wkt2' => $g->hadir,
            'Stn2' => $g->transport,
            'Jml2' => $g->jml_transport,
            'Dansos' => $g->dana_sosial,
            'Potongan' => $g->potongan,
            'Diterima' => $g->total_gaji,
            'Paraf' => $i + 1,
        ];
    });

    $filename = "REKAP HONORIUM GURU PERIODE {$namaBulan[$bulan]} $tahun.xlsx";

    return Excel::download(new class($data) implements 
        \Maatwebsite\Excel\Concerns\FromCollection, 
        \Maatwebsite\Excel\Concerns\WithHeadings,
        \Maatwebsite\Excel\Concerns\WithStyles,
        \Maatwebsite\Excel\Concerns\WithColumnWidths
    {
        private $data;
        public function __construct($data) { $this->data = $data; }
        public function collection() { return $this->data; }
        public function headings(): array {
            return [
                ['No','Nama','Tunjangan','Honorarium','','','Kehadiran','','','Dansos','Potongan','Diterima','Paraf'],
                ['','','','Wkt','Stn','Jml','Wkt','Stn','Jml','','','','']
            ];
        }
        public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
            $totalRows = count($this->data) + 2;
            $sheet->getStyle('A1:M' . $totalRows)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A1:M2')->getFont()->setBold(true);
            $sheet->getStyle('A1:M2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:M2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->mergeCells('A1:A2'); $sheet->mergeCells('B1:B2'); $sheet->mergeCells('C1:C2');
            $sheet->mergeCells('D1:F1'); $sheet->mergeCells('G1:I1');
            $sheet->mergeCells('J1:J2'); $sheet->mergeCells('K1:K2'); 
            $sheet->mergeCells('L1:L2'); $sheet->mergeCells('M1:M2');
            $sheet->getStyle('A3:A' . $totalRows)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('D3:D' . $totalRows)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('G3:G' . $totalRows)->getAlignment()->setHorizontal('center');
        }

        public function columnWidths(): array {
            return [
                'A' => 5, 'B' => 25, 'C' => 15, 'D' => 6, 'E' => 12, 'F' => 15,
                'G' => 6, 'H' => 12, 'I' => 15, 'J' => 12, 'K' => 12, 'L' => 15, 'M' => 8
            ];
        }
    }, $filename);
}

// PAYROLL PENGABDIAN
public function pengabdianData(Request $request)
{
    $query = DB::table('pengabdian');

    if ($request->lokasi_filter) {
        $query->where('lokasi', $request->lokasi_filter);
    }
    if (Auth::guard('user')->user()?->hasRole('staff')) {
        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $query->where('lokasi', $cabang ? $cabang->nama_cabang : '');
    }

    $pengabdian = $query->orderBy('nama')->get();
    
    // ✅ Ambil SEMUA karyawan (untuk keperluan edit)
    $karyawan = DB::table('karyawan')
        ->select('nik', 'nama_lengkap')
        ->orderBy('nama_lengkap')
        ->get();
    
    // ✅ Ambil NIK yang sudah terpakai (untuk validasi di JavaScript)
    $nikTerpakai = DB::table('pengabdian')->pluck('nik')->toArray();

    return view('payroll.pengabdian.data_pengabdian', compact('pengabdian', 'karyawan', 'nikTerpakai'));
}

    // 🟢 Simpan Data Pengabdian
// 🟢 Simpan Data Pengabdian (DENGAN VALIDASI)
public function pengabdianStore(Request $request)
{
    try {
        // ✅ Ambil NIK dari nama yang dipilih
        $karyawan = DB::table('karyawan')
            ->where('nama_lengkap', $request->nama)
            ->first();

        if (!$karyawan) {
            return redirect()
                ->back()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->withInput();
        }

        $nik = $karyawan->nik;

        // ✅ CEK DUPLIKASI NIK
        $exists = DB::table('pengabdian')
            ->where('nik', $nik)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Data pengabdian untuk karyawan dengan NIK ' . $nik . ' sudah ada!')
                ->withInput();
        }

        // ✅ Insert data jika tidak ada duplikasi
        DB::table('pengabdian')->insert([
            'nik' => $nik,
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'tunjangan_jabatan' => $this->cleanRupiah($request->tunjangan_jabatan),
            'tunjangan_pengabdian' => $this->cleanRupiah($request->tunjangan_pengabdian),
            'tunjangan_lainnya' => $this->cleanRupiah($request->tunjangan_lainnya),
            'honorarium_wkt' => $request->honorarium_wkt ?: 0,
            'honorarium_stn' => $this->cleanRupiah($request->honorarium_stn),
            'kehadiran_stn' => $this->cleanRupiah($request->kehadiran_stn),
            'potongan_dansos' => $this->cleanRupiah($request->potongan_dansos),
            'potongan_fpmk' => $this->cleanRupiah($request->potongan_fpmk),
            'potongan_lainnya' => $this->cleanRupiah($request->potongan_lainnya),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('payroll.pengabdian.data_pengabdian')
            ->with('success', 'Data Pengabdian berhasil ditambahkan.');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Gagal menambah data: ' . $e->getMessage())
            ->withInput();
    }
}

// 🟢 Update Data Pengabdian (DENGAN VALIDASI)
public function pengabdianUpdate(Request $request, $id)
{
    try {
        // ✅ Ambil NIK dari nama yang dipilih
        $karyawan = DB::table('karyawan')
            ->where('nama_lengkap', $request->nama)
            ->first();

        if (!$karyawan) {
            return redirect()
                ->back()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->withInput();
        }

        $nik = $karyawan->nik;

        // ✅ CEK DUPLIKASI NIK (kecuali data yang sedang diedit)
        $exists = DB::table('pengabdian')
            ->where('nik', $nik)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Data pengabdian untuk karyawan dengan NIK ' . $nik . ' sudah ada!')
                ->withInput();
        }

        // ✅ Update data jika tidak ada duplikasi
        DB::table('pengabdian')->where('id', $id)->update([
            'nik' => $nik,
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'tunjangan_jabatan' => $this->cleanRupiah($request->tunjangan_jabatan),
            'tunjangan_pengabdian' => $this->cleanRupiah($request->tunjangan_pengabdian),
            'tunjangan_lainnya' => $this->cleanRupiah($request->tunjangan_lainnya),
            'honorarium_wkt' => $request->honorarium_wkt ?: 0,
            'honorarium_stn' => $this->cleanRupiah($request->honorarium_stn),
            'kehadiran_stn' => $this->cleanRupiah($request->kehadiran_stn),
            'potongan_dansos' => $this->cleanRupiah($request->potongan_dansos),
            'potongan_fpmk' => $this->cleanRupiah($request->potongan_fpmk),
            'potongan_lainnya' => $this->cleanRupiah($request->potongan_lainnya),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('payroll.pengabdian.data_pengabdian')
            ->with('success', 'Data Pengabdian berhasil diperbarui.');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
            ->withInput();
    }
}

// 🟢 Ambil Data Pengabdian untuk Edit (AJAX)
public function pengabdianEdit($id)
{
    $pengabdian = DB::table('pengabdian')->where('id', $id)->first();

    if (!$pengabdian) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    // Ubah ke array supaya null jadi string kosong, aman untuk form
    $data = [
        'id' => $pengabdian->id,
        'nik' => $pengabdian->nik ?? '',
        'nama' => $pengabdian->nama ?? '',
        'lokasi' => $pengabdian->lokasi ?? '',
        'tunjangan_jabatan' => (int)($pengabdian->tunjangan_jabatan ?? 0),
        'tunjangan_pengabdian' => (int)($pengabdian->tunjangan_pengabdian ?? 0),
        'tunjangan_lainnya' => (int)($pengabdian->tunjangan_lainnya ?? 0),
        'honorarium_wkt' => (int)($pengabdian->honorarium_wkt ?? 0),
        'honorarium_stn' => (int)($pengabdian->honorarium_stn ?? 0),
        'kehadiran_wkt' => (int)($pengabdian->kehadiran_wkt ?? 0),
        'kehadiran_stn' => (int)($pengabdian->kehadiran_stn ?? 0),
        'potongan_dansos' => (int)($pengabdian->potongan_dansos ?? 0),
        'potongan_fpmk' => (int)($pengabdian->potongan_fpmk ?? 0),
        'potongan_lainnya' => (int)($pengabdian->potongan_lainnya ?? 0),
    ];

    return response()->json($data);
}

// 🟢 Hapus Data Pengabdian
public function pengabdianDelete($id)
{
    try {
        DB::table('pengabdian')->where('id', $id)->delete();

        return redirect()
            ->back()
            ->with('success', 'Data Pengabdian berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}

// 🔧 Fungsi bantu untuk membersihkan format Rupiah
private function cleanRupiah($value)
{
    return (int) str_replace(['Rp', '.', ',', ' '], '', $value ?: 0);
}

// PAYROLL PENGABDIAN - REKAP GAJI
public function rekapPengabdian(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    // Ambil data pengabdian dengan join ke karyawan
    $rekap = DB::table('pengabdian')
        ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
        ->select(
            'pengabdian.*',
            'karyawan.nama_lengkap'
        )
        ->when($request->lokasi, fn($q) => $q->where('pengabdian.lokasi', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($q) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $q->where('pengabdian.lokasi', $cabang ? $cabang->nama_cabang : '');
        })
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    // Hitung kehadiran otomatis dari presensi & kalkulasi gaji
    $rekap = $rekap->map(function ($p) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        // ✅ Ambil data presensi (status hadir = 'h')
        $kehadiran = DB::table('presensi')
            ->where('nik', $p->nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->where('status', 'h')
            ->pluck('tgl_presensi')
            ->unique()
            ->count();

        // Update kehadiran_wkt dengan data real
        $p->kehadiran_wkt = $kehadiran;

        // ✅ Hitung Honorarium
        $jml_honorarium = ($p->honorarium_wkt ?? 0) * ($p->honorarium_stn ?? 0);

        // ✅ Hitung Kehadiran
        $jml_kehadiran = $kehadiran * ($p->kehadiran_stn ?? 0);

        // ✅ Total Tunjangan
        $total_tunjangan = 
            ($p->tunjangan_jabatan ?? 0) + 
            ($p->tunjangan_pengabdian ?? 0) + 
            ($p->tunjangan_lainnya ?? 0);

        // ✅ Total Pendapatan (Tunjangan + Honorarium + Kehadiran)
        $total_pendapatan = $total_tunjangan + $jml_honorarium + $jml_kehadiran;

        // ✅ Total Potongan
        $total_potongan = 
            ($p->potongan_dansos ?? 0) + 
            ($p->potongan_fpmk ?? 0) + 
            ($p->potongan_lainnya ?? 0);

        // ✅ Gaji Bersih (Pendapatan - Potongan)
        $gaji_bersih = $total_pendapatan - $total_potongan;

        // Simpan hasil kalkulasi ke object
        $p->jml_honorarium = $jml_honorarium;
        $p->jml_kehadiran = $jml_kehadiran;
        $p->total_tunjangan = $total_tunjangan;
        $p->total_pendapatan = $total_pendapatan;
        $p->total_potongan = $total_potongan;
        $p->gaji_bersih = $gaji_bersih;

        return $p;
    });

    // ✅ Summary untuk footer tabel
    $summary = [
        'total_tunjangan_jabatan'    => $rekap->sum('tunjangan_jabatan'),
        'total_tunjangan_pengabdian' => $rekap->sum('tunjangan_pengabdian'),
        'total_tunjangan_lainnya'    => $rekap->sum('tunjangan_lainnya'),
        'total_tunjangan'            => $rekap->sum('total_tunjangan'),
        'total_honorarium_wkt'       => $rekap->sum('honorarium_wkt'),
        'total_jml_honorarium'       => $rekap->sum('jml_honorarium'),
        'total_kehadiran_wkt'        => $rekap->sum('kehadiran_wkt'),
        'total_jml_kehadiran'        => $rekap->sum('jml_kehadiran'),
        'total_pendapatan'           => $rekap->sum('total_pendapatan'),
        'total_potongan_dansos'      => $rekap->sum('potongan_dansos'),
        'total_potongan_fpmk'        => $rekap->sum('potongan_fpmk'),
        'total_potongan_lainnya'     => $rekap->sum('potongan_lainnya'),
        'total_potongan'             => $rekap->sum('total_potongan'),
        'total_gaji_bersih'          => $rekap->sum('gaji_bersih'),
    ];

    return view('payroll.pengabdian.rekap_pengabdian', [
        'rekap'   => $rekap,
        'summary' => $summary,
        'bulan'   => $bulan,
        'tahun'   => $tahun,
    ]);
}

// CETAK REKAP PENGABDIAN
public function cetakPengabdian(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));
    $lokasi = $request->get('lokasi');

    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Ambil data pengabdian dengan join ke karyawan
    $rekap = DB::table('pengabdian')
        ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
        ->select(
            'pengabdian.*',
            'karyawan.nama_lengkap'
        )
        ->when($lokasi, fn($q) => $q->where('pengabdian.lokasi', $lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($q) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $q->where('pengabdian.lokasi', $cabang ? $cabang->nama_cabang : '');
        })
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    // Hitung kehadiran otomatis dari presensi & kalkulasi gaji
    $rekap = $rekap->map(function ($p) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $kehadiran = DB::table('presensi')
            ->where('nik', $p->nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->where('status', 'h')
            ->pluck('tgl_presensi')
            ->unique()
            ->count();

        $p->kehadiran_wkt = $kehadiran;
        $p->jml_honorarium = ($p->honorarium_wkt ?? 0) * ($p->honorarium_stn ?? 0);
        $p->jml_kehadiran = $kehadiran * ($p->kehadiran_stn ?? 0);
        
        $total_pendapatan = 
            ($p->tunjangan_jabatan ?? 0) + 
            ($p->tunjangan_pengabdian ?? 0) + 
            ($p->tunjangan_lainnya ?? 0) +
            $p->jml_honorarium + 
            $p->jml_kehadiran;
        
        $total_potongan = 
            ($p->potongan_dansos ?? 0) + 
            ($p->potongan_fpmk ?? 0) + 
            ($p->potongan_lainnya ?? 0);
        
        $p->gaji_bersih = $total_pendapatan - $total_potongan;

        return $p;
    });

    $lokasi_name = $lokasi;
    if (Auth::guard('user')->user()?->hasRole('staff')) {
        $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
        $lokasi_name = $cabang ? $cabang->nama_cabang : '';
    }

    return view('payroll.pengabdian.cetak_pengabdian', [
        'rekap' => $rekap,
        'bulan' => $bulanIndo[$bulan],
        'tahun' => $tahun,
        'lokasi' => $lokasi_name
    ]);
}

// DOWNLOAD PDF PENGABDIAN
public function pdfPengabdian(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));
    $lokasi = $request->get('lokasi');

    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Ambil data pengabdian dengan join ke karyawan
    $rekap = DB::table('pengabdian')
        ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
        ->select(
            'pengabdian.*',
            'karyawan.nama_lengkap'
        )
        ->when($lokasi, fn($q) => $q->where('pengabdian.lokasi', $lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($q) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $q->where('pengabdian.lokasi', $cabang ? $cabang->nama_cabang : '');
        })
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    // Hitung kehadiran otomatis dari presensi & kalkulasi gaji
    $rekap = $rekap->map(function ($p) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $kehadiran = DB::table('presensi')
            ->where('nik', $p->nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->where('status', 'h')
            ->pluck('tgl_presensi')
            ->unique()
            ->count();

        $p->kehadiran_wkt = $kehadiran;
        $p->jml_honorarium = ($p->honorarium_wkt ?? 0) * ($p->honorarium_stn ?? 0);
        $p->jml_kehadiran = $kehadiran * ($p->kehadiran_stn ?? 0);
        
        $total_pendapatan = 
            ($p->tunjangan_jabatan ?? 0) + 
            ($p->tunjangan_pengabdian ?? 0) + 
            ($p->tunjangan_lainnya ?? 0) +
            $p->jml_honorarium + 
            $p->jml_kehadiran;
        
        $total_potongan = 
            ($p->potongan_dansos ?? 0) + 
            ($p->potongan_fpmk ?? 0) + 
            ($p->potongan_lainnya ?? 0);
        
        $p->gaji_bersih = $total_pendapatan - $total_potongan;

        return $p;
    });

    $lokasi_name = $lokasi;
    if (Auth::guard('user')->user()?->hasRole('staff')) {
        $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
        $lokasi_name = $cabang ? $cabang->nama_cabang : '';
    }

    $data = [
        'rekap' => $rekap,
        'bulan' => $bulanIndo[$bulan],
        'tahun' => $tahun,
        'lokasi' => $lokasi_name
    ];

    // Load view untuk PDF
    $pdf = PDF::loadView('payroll.pengabdian.pdf_pengabdian', $data);
    
    // Set paper landscape
    $pdf->setPaper('a4', 'landscape');
    
    // Download PDF
    $filename = 'Rekap_Gaji_Pengabdian_' . $bulanIndo[$bulan] . '_' . $tahun . '.pdf';
    return $pdf->download($filename);
}

// SLIP GAJI PENGABDIAN
public function slipgajiPengabdian(Request $request)
{
    $bulan = $request->get('bulan', date('m'));
    $tahun = $request->get('tahun', date('Y'));
    
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Query dengan DISTINCT untuk mencegah duplikasi kehadiran
    $rekap = DB::select("
        SELECT 
            p.id AS pengabdian_id,
            k.nama_lengkap,
            p.lokasi,
            p.tunjangan_jabatan,
            p.tunjangan_pengabdian,
            p.tunjangan_lainnya,
            p.honorarium_wkt,
            p.honorarium_stn,
            (p.honorarium_wkt * IFNULL(p.honorarium_stn, 0)) AS jml_honorarium,
            IFNULL(pr.jumlah_hadir, 0) AS kehadiran_wkt,
            p.kehadiran_stn,
            (IFNULL(pr.jumlah_hadir, 0) * IFNULL(p.kehadiran_stn, 0)) AS jml_kehadiran,
            p.potongan_dansos,
            p.potongan_fpmk,
            p.potongan_lainnya,
            (
                IFNULL(p.tunjangan_jabatan, 0) +
                IFNULL(p.tunjangan_pengabdian, 0) +
                IFNULL(p.tunjangan_lainnya, 0) +
                (p.honorarium_wkt * IFNULL(p.honorarium_stn, 0)) +
                (IFNULL(pr.jumlah_hadir, 0) * IFNULL(p.kehadiran_stn, 0))
                -
                (IFNULL(p.potongan_dansos, 0) + IFNULL(p.potongan_fpmk, 0) + IFNULL(p.potongan_lainnya, 0))
            ) AS gaji_bersih
        FROM pengabdian p
        JOIN karyawan k ON p.nik = k.nik
        LEFT JOIN (
            SELECT nik, COUNT(DISTINCT tgl_presensi) as jumlah_hadir
            FROM presensi
            WHERE MONTH(tgl_presensi) = ?
            AND YEAR(tgl_presensi) = ?
            AND status = 'h'
            GROUP BY nik
        ) pr ON k.nik = pr.nik
        WHERE 1=1
            " . ($request->lokasi ? " AND p.lokasi = '" . $request->lokasi . "'" : "") . "
            " . (Auth::guard('user')->user()?->hasRole('staff') ? " AND p.lokasi = (SELECT nama_cabang FROM cabang WHERE kode_cabang = '" . Auth::guard('user')->user()->kode_cabang . "')" : "") . "
        ORDER BY k.nama_lengkap ASC
    ", [$bulan, $tahun]);

    return view('payroll.pengabdian.slipgaji_pengabdian', [
        'rekap' => $rekap,
        'bulan' => (int)$bulan,
        'tahun' => (int)$tahun,
        'bulanIndo' => $bulanIndo,
        'namaBulan' => $bulanIndo[(int)$bulan] ?? 'Tidak Valid',
        'lokasi' => $request->lokasi
    ]);
}

// DOWNLOAD EXCEL PENGABDIAN
public function excelPengabdian(Request $request)
{
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $namaBulan = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET',
        4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
        7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER',
        10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
    ];

    // Ambil data pengabdian dengan join ke karyawan
    $rekap = DB::table('pengabdian')
        ->join('karyawan', 'pengabdian.nik', '=', 'karyawan.nik')
        ->select(
            'pengabdian.*',
            'karyawan.nama_lengkap'
        )
        ->when($request->lokasi, fn($q) => $q->where('pengabdian.lokasi', $request->lokasi))
        ->when(Auth::guard('user')->user()?->hasRole('staff'), function($q) {
            $kode_cabang = Auth::guard('user')->user()->kode_cabang;
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
            return $q->where('pengabdian.lokasi', $cabang ? $cabang->nama_cabang : '');
        })
        ->orderBy('karyawan.nama_lengkap')
        ->get();

    // Hitung kehadiran otomatis dari presensi & kalkulasi gaji
    $rekap = $rekap->map(function ($p) use ($bulan, $tahun) {
        $dari = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        // Ambil data presensi (status hadir = 'h')
        $kehadiran = DB::table('presensi')
            ->where('nik', $p->nik)
            ->whereBetween('tgl_presensi', [$dari, $sampai])
            ->where('status', 'h')
            ->pluck('tgl_presensi')
            ->unique()
            ->count();

        // Update kehadiran_wkt dengan data real
        $p->kehadiran_wkt = $kehadiran;

        // Hitung Honorarium
        $p->jml_honorarium = ($p->honorarium_wkt ?? 0) * ($p->honorarium_stn ?? 0);

        // Hitung Kehadiran
        $p->jml_kehadiran = $kehadiran * ($p->kehadiran_stn ?? 0);

        // Total Pendapatan
        $total_pendapatan = 
            ($p->tunjangan_jabatan ?? 0) + 
            ($p->tunjangan_pengabdian ?? 0) + 
            ($p->tunjangan_lainnya ?? 0) +
            $p->jml_honorarium + 
            $p->jml_kehadiran;

        // Total Potongan
        $total_potongan = 
            ($p->potongan_dansos ?? 0) + 
            ($p->potongan_fpmk ?? 0) + 
            ($p->potongan_lainnya ?? 0);

        // Gaji Bersih
        $p->gaji_bersih = $total_pendapatan - $total_potongan;

        return $p;
    });

    $data = $rekap->map(function ($p, $i) {
        return [
            'No' => $i + 1,
            'Nama' => $p->nama_lengkap,
            'Tunj_Jabatan' => $p->tunjangan_jabatan ?? 0,
            'Tunj_Pengabdian' => $p->tunjangan_pengabdian ?? 0,
            'Tunj_Lainnya' => $p->tunjangan_lainnya ?? 0,
            'Hon_Wkt' => $p->honorarium_wkt ?? 0,
            'Hon_Stn' => $p->honorarium_stn ?? 0,
            'Hon_Jml' => $p->jml_honorarium ?? 0,
            'Khd_Wkt' => $p->kehadiran_wkt ?? 0,
            'Khd_Stn' => $p->kehadiran_stn ?? 0,
            'Khd_Jml' => $p->jml_kehadiran ?? 0,
            'Pot_Dansos' => $p->potongan_dansos ?? 0,
            'Pot_FPMK' => $p->potongan_fpmk ?? 0,
            'Pot_Lainnya' => $p->potongan_lainnya ?? 0,
            'Diterima' => $p->gaji_bersih ?? 0,
            'Paraf' => $i + 1,
        ];
    });

    $filename = "REKAP GAJI PENGABDIAN PERIODE {$namaBulan[$bulan]} $tahun.xlsx";

    return Excel::download(new class($data) implements 
        \Maatwebsite\Excel\Concerns\FromCollection, 
        \Maatwebsite\Excel\Concerns\WithHeadings,
        \Maatwebsite\Excel\Concerns\WithStyles,
        \Maatwebsite\Excel\Concerns\WithColumnWidths
    {
        private $data;
        public function __construct($data) { $this->data = $data; }
        public function collection() { return $this->data; }
        
        public function headings(): array {
            return [
                ['No','Nama','Tunjangan','','','Honorarium','','','Kehadiran','','','Potongan','','','Diterima','Paraf'],
                ['','','Jabatan','Pengabdian','Lainnya','Wkt','Stn','Jml','Wkt','Stn','Jml','Dansos','FPMK','Lainnya','','']
            ];
        }
        
        public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
            $totalRows = count($this->data) + 2;
            
            // Border untuk semua cell
            $sheet->getStyle('A1:P' . $totalRows)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Header bold dan center
            $sheet->getStyle('A1:P2')->getFont()->setBold(true);
            $sheet->getStyle('A1:P2')->getAlignment()->setHorizontal('center')->setVertical('center');
            
            // Background header
            $sheet->getStyle('A1:P2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            
            // Merge cells untuk header baris pertama
            $sheet->mergeCells('A1:A2'); // No
            $sheet->mergeCells('B1:B2'); // Nama
            $sheet->mergeCells('C1:E1'); // Tunjangan
            $sheet->mergeCells('F1:H1'); // Honorarium
            $sheet->mergeCells('I1:K1'); // Kehadiran
            $sheet->mergeCells('L1:N1'); // Potongan
            $sheet->mergeCells('O1:O2'); // Diterima
            $sheet->mergeCells('P1:P2'); // Paraf
            
            // Center alignment untuk kolom tertentu
            $sheet->getStyle('A3:A' . $totalRows)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('C3:P' . $totalRows)->getAlignment()->setHorizontal('center');
            
            // Format number untuk kolom rupiah
            $sheet->getStyle('C3:E' . $totalRows)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle('G3:H' . $totalRows)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle('J3:K' . $totalRows)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle('L3:O' . $totalRows)->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        public function columnWidths(): array {
            return [
                'A' => 5,  // No
                'B' => 25, // Nama
                'C' => 12, // Tunj Jabatan
                'D' => 12, // Tunj Pengabdian
                'E' => 12, // Tunj Lainnya
                'F' => 6,  // Hon Wkt
                'G' => 12, // Hon Stn
                'H' => 15, // Hon Jml
                'I' => 6,  // Khd Wkt
                'J' => 12, // Khd Stn
                'K' => 15, // Khd Jml
                'L' => 12, // Pot Dansos
                'M' => 12, // Pot FPMK
                'N' => 12, // Pot Lainnya
                'O' => 15, // Diterima
                'P' => 8,  // Paraf
            ];
        }
    }, $filename);
}

}
