<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterSiswa;
use App\Models\MasterKelas;
use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

use App\Exports\AdminMasterSiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;

class MasterSiswaController extends Controller
{
    /**
     * Generate NISN otomatis 10-digit unik
     */
    public static function generateNisn()
    {
        do {
            $prefix = '00' . date('y'); // misal: 0026
            $randomDigits = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $nisn = $prefix . $randomDigits;
        } while (MasterSiswa::where('nisn', $nisn)->exists());

        return $nisn;
    }

    public function index(Request $request)
    {
        $query = MasterSiswa::with('kelas');

        // Filter Cabang / Lembaga / Kelas via MasterKelas
        if ($request->filled('kode_cabang') || $request->filled('lembaga')) {
            $query->whereHas('kelas', function ($q) use ($request) {
                if ($request->filled('kode_cabang')) {
                    $q->where('kode_cabang', $request->kode_cabang);
                }
                if ($request->filled('lembaga')) {
                    $q->where('lembaga', $request->lembaga);
                }
            });
        }

        if ($request->filled('kode_kelas')) {
            $query->where('kode_kelas', $request->kode_kelas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        // Paginasi 25 data per halaman
        $siswas = $query->orderBy('kode_kelas')->orderBy('nama_siswa')->paginate(25)->withQueryString();

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $kelases = MasterKelas::orderBy('kode_kelas')->get();

        return view('mastersiswa.index', compact('siswas', 'cabang', 'kelases'));
    }

    /**
     * Export Data Master Siswa ke Excel (Filtered)
     */
    public function exportExcel(Request $request)
    {
        $namaFile = 'Data_Master_Siswa_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new AdminMasterSiswaExport($request->all()), $namaFile);
    }

    /**
     * Export Data Master Siswa ke PDF (Filtered)
     */
    public function exportPdf(Request $request)
    {
        $query = MasterSiswa::with('kelas');

        if ($request->filled('kode_cabang') || $request->filled('lembaga')) {
            $query->whereHas('kelas', function ($q) use ($request) {
                if ($request->filled('kode_cabang')) {
                    $q->where('kode_cabang', $request->kode_cabang);
                }
                if ($request->filled('lembaga')) {
                    $q->where('lembaga', $request->lembaga);
                }
            });
        }

        if ($request->filled('kode_kelas')) {
            $query->where('kode_kelas', $request->kode_kelas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        $siswas = $query->orderBy('kode_kelas')->orderBy('nama_siswa')->get();

        $pdf = Pdf::loadView('mastersiswa.cetak_pdf', compact('siswas'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Data_Master_Siswa_' . date('Ymd') . '.pdf');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:100',
            'kode_kelas' => 'required|exists:master_kelas,kode_kelas',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        try {
            $nisn = !empty($request->nisn) ? trim($request->nisn) : self::generateNisn();

            MasterSiswa::create([
                'nisn' => $nisn,
                'nama_siswa' => $request->nama_siswa,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kode_kelas' => $request->kode_kelas,
                'status' => $request->status ?? 'aktif',
            ]);

            return redirect('/panel/mastersiswa')->with(['success' => 'Data Siswa Berhasil Ditambahkan (NISN: ' . $nisn . ')']);
        } catch (\Exception $e) {
            return redirect('/panel/mastersiswa')->with(['warning' => 'Gagal Menambahkan Data Siswa: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:master_siswa,id',
            'nama_siswa' => 'required|string|max:100',
            'kode_kelas' => 'required|exists:master_kelas,kode_kelas',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        try {
            $siswa = MasterSiswa::findOrFail($request->id);
            $nisn = !empty($request->nisn) ? trim($request->nisn) : ($siswa->nisn ?: self::generateNisn());

            $siswa->update([
                'nisn' => $nisn,
                'nama_siswa' => $request->nama_siswa,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kode_kelas' => $request->kode_kelas,
                'status' => $request->status ?? 'aktif',
            ]);

            return redirect('/panel/mastersiswa')->with(['success' => 'Data Siswa Berhasil Diperbarui']);
        } catch (\Exception $e) {
            return redirect('/panel/mastersiswa')->with(['warning' => 'Gagal Memperbarui Data Siswa: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $siswa = MasterSiswa::findOrFail($id);
            $siswa->delete();

            return redirect('/panel/mastersiswa')->with(['success' => 'Data Siswa Berhasil Dihapus']);
        } catch (\Exception $e) {
            return redirect('/panel/mastersiswa')->with(['warning' => 'Gagal Menghapus Data Siswa: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport(), 'Template_Import_Data_Siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|exists:master_kelas,kode_kelas',
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            if ($request->filled('kurikulum')) {
                MasterKelas::where('kode_kelas', $request->kode_kelas)
                    ->update(['kurikulum' => $request->kurikulum]);
            }

            $importer = new SiswaImport($request->kode_kelas);
            Excel::import($importer, $request->file('file_excel'));

            return redirect('/panel/mastersiswa?kode_kelas=' . $request->kode_kelas)
                ->with(['success' => 'Berhasil Mengimpor ' . $importer->successCount . ' Data Siswa ke Kelas ' . $request->kode_kelas]);
        } catch (\Exception $e) {
            return redirect('/panel/mastersiswa')
                ->with(['warning' => 'Gagal Mengimpor Data Siswa: ' . $e->getMessage()]);
        }
    }

    /**
     * Ambil data siswa per kelas via AJAX untuk modal kenaikan kelas
     */
    public function getSiswaByKelas($kode_kelas)
    {
        $siswas = MasterSiswa::where('kode_kelas', $kode_kelas)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get(['id', 'nisn', 'nama_siswa', 'jenis_kelamin', 'status']);

        return response()->json($siswas);
    }

    /**
     * Cek jumlah siswa aktif di kelas tertentu (untuk peringatan kelas tujuan)
     */
    public function countSiswaByKelas($kode_kelas)
    {
        $count = MasterSiswa::where('kode_kelas', $kode_kelas)
            ->where('status', 'aktif')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Proses Kenaikan Kelas, Mutasi Rombel/Lembaga, atau Kelulusan Massal
     */
    public function bulkPromote(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:master_siswa,id',
            'aksi' => 'required|in:pindah,lulus',
            'kode_kelas_tujuan' => 'required_if:aksi,pindah|nullable|exists:master_kelas,kode_kelas',
        ]);

        try {
            $count = count($request->siswa_ids);

            if ($request->aksi === 'pindah') {
                MasterSiswa::whereIn('id', $request->siswa_ids)
                    ->update([
                        'kode_kelas' => $request->kode_kelas_tujuan,
                        'status' => 'aktif'
                    ]);

                $targetKelas = MasterKelas::find($request->kode_kelas_tujuan);
                $namaTarget = $targetKelas ? 'Kelas ' . $targetKelas->nama_kelas . ' (' . $targetKelas->lembaga . ')' : $request->kode_kelas_tujuan;

                return redirect('/panel/mastersiswa?kode_kelas=' . $request->kode_kelas_tujuan)
                    ->with(['success' => "Berhasil memindahkan / menaikkan {$count} siswa ke {$namaTarget}!"]);
            } else {
                MasterSiswa::whereIn('id', $request->siswa_ids)
                    ->update(['status' => 'nonaktif']);

                return redirect('/panel/mastersiswa')
                    ->with(['success' => "Berhasil meluluskan / menonaktifkan {$count} siswa!"]);
            }
        } catch (\Exception $e) {
            return redirect('/panel/mastersiswa')
                ->with(['warning' => 'Gagal Memproses Kenaikan / Pindah Kelas: ' . $e->getMessage()]);
        }
    }
}
