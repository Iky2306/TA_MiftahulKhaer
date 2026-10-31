<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterKelas;
use App\Models\MasterMapelKelas;
use App\Models\JurnalMengajar;
use App\Exports\MapelTemplateExport;
use App\Imports\MapelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Karyawan;

class MasterKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterKelas::query();

        if ($request->has('lembaga') && !empty($request->lembaga)) {
            $query->where('lembaga', $request->lembaga);
        }

        if ($request->has('kode_cabang') && !empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%')
                  ->orWhere('kode_kelas', 'like', '%' . $search . '%');
            });
        }

        $kelas = $query->with('waliKelas')->orderBy('kode_kelas')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $gurus = Karyawan::where('kode_dept', '!=', 'PPT')->orderBy('nama_lengkap')->get();

        return view('masterkelas.index', compact('kelas', 'cabang', 'gurus'));
    }

    public function generateCode(Request $request)
    {
        $kode_cabang = strtoupper(trim($request->kode_cabang));
        $lembaga = strtoupper(trim($request->lembaga));

        if (empty($kode_cabang) || empty($lembaga)) {
            return response()->json(['kode_kelas' => '']);
        }

        $prefix = $kode_cabang . '-' . $lembaga . '-';
        
        $existing = MasterKelas::where('kode_kelas', 'like', $prefix . '%')
            ->pluck('kode_kelas')
            ->toArray();

        $maxNumber = 0;
        foreach ($existing as $code) {
            $numPart = str_replace($prefix, '', $code);
            if (is_numeric($numPart)) {
                $num = (int)$numPart;
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
        $nextKode = $prefix . $nextNumber;

        return response()->json(['kode_kelas' => $nextKode]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|unique:master_kelas',
            'nama_kelas' => 'required',
            'lembaga' => 'required|in:MI,MTs,MA,SMK'
        ]);

        try {
            MasterKelas::create([
                'kode_kelas' => $request->kode_kelas,
                'nama_kelas' => $request->nama_kelas,
                'lembaga' => $request->lembaga,
                'kode_cabang' => $request->kode_cabang,
                'wali_kelas_nik' => $request->wali_kelas_nik,
                'qr_token' => Str::uuid()->toString()
            ]);
            return redirect('/panel/masterkelas')->with(['success' => 'Data Kelas Tersimpan']);
        } catch (\Exception $e) {
            return redirect('/panel/masterkelas')->with(['warning' => 'Data Kelas Gagal Disimpan']);
        }
    }

    public function update(Request $request)
    {
        $kode_kelas = $request->kode_kelas;
        $nama_kelas = $request->nama_kelas;
        $lembaga = $request->lembaga;
        
        try {
            MasterKelas::where('kode_kelas', $kode_kelas)->update([
                'nama_kelas' => $nama_kelas,
                'lembaga' => $lembaga,
                'kode_cabang' => $request->kode_cabang,
                'wali_kelas_nik' => $request->wali_kelas_nik
            ]);
            return redirect('/panel/masterkelas')->with(['success' => 'Data Kelas Berhasil Diupdate']);
        } catch (\Exception $e) {
            return redirect('/panel/masterkelas')->with(['warning' => 'Data Kelas Gagal Diupdate']);
        }
    }

    public function delete($kode_kelas)
    {
        $delete = MasterKelas::where('kode_kelas', $kode_kelas)->delete();
        if ($delete) {
            return redirect('/panel/masterkelas')->with(['success' => 'Data Kelas Berhasil Dihapus']);
        } else {
            return redirect('/panel/masterkelas')->with(['warning' => 'Data Kelas Gagal Dihapus']);
        }
    }

    // MAPEL KELAS
    public function mapel($kode_kelas)
    {
        $kelas = MasterKelas::where('kode_kelas', $kode_kelas)->firstOrFail();
        $mapels = MasterMapelKelas::where('kode_kelas', $kode_kelas)->get();
        return view('masterkelas.mapel', compact('kelas', 'mapels'));
    }

    public function storeMapel(Request $request, $kode_kelas)
    {
        $request->validate(['nama_mapel' => 'required']);
        
        try {
            MasterMapelKelas::create([
                'kode_kelas' => $kode_kelas,
                'nama_mapel' => $request->nama_mapel
            ]);
            return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')->with(['success' => 'Mata Pelajaran Ditambahkan']);
        } catch (\Exception $e) {
            return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')->with(['warning' => 'Gagal Menambahkan Mata Pelajaran']);
        }
    }

    public function deleteMapel($id)
    {
        $mapel = MasterMapelKelas::findOrFail($id);
        $kode_kelas = $mapel->kode_kelas;
        $mapel->delete();
        return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')->with(['success' => 'Mata Pelajaran Dihapus']);
    }

    public function downloadTemplateMapel()
    {
        return Excel::download(new MapelTemplateExport(), 'Template_Import_Mata_Pelajaran.xlsx');
    }

    public function importMapel(Request $request, $kode_kelas)
    {
        $request->validate([
            'file' => 'required|file|max:5120|mimes:xlsx,csv,xls',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv',
        ]);

        try {
            $import = new MapelImport($kode_kelas);
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $duplicateCount = $import->getDuplicateCount();

            if ($successCount == 0 && $duplicateCount > 0) {
                return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')
                    ->with(['warning' => "Import selesai. Semua {$duplicateCount} mata pelajaran di file sudah ada di kelas ini (duplikat)."]);
            }

            if ($successCount == 0) {
                return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')
                    ->with(['warning' => 'Tidak ada mata pelajaran yang berhasil diimport. Periksa kembali format file Excel.']);
            }

            $message = "Berhasil mengimpor {$successCount} mata pelajaran ke kelas ini.";
            if ($duplicateCount > 0) {
                $message .= " ({$duplicateCount} mata pelajaran dilewati karena sudah ada).";
            }

            return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')
                ->with(['success' => $message]);

        } catch (\Exception $e) {
            return redirect('/panel/masterkelas/' . $kode_kelas . '/mapel')
                ->with(['warning' => 'Import Gagal: ' . $e->getMessage()]);
        }
    }

    public function showQrPreview($kode_kelas)
    {
        $kelas = MasterKelas::where('kode_kelas', $kode_kelas)->firstOrFail();
        
        // Fetch QR as base64 - added ecc=H (High Error Correction) to support central logo overlay reliably
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=450x450&ecc=H&data=" . urlencode($kelas->qr_token);
        
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]);

        try {
            $qr_content = file_get_contents($qr_url, false, $context);
            $qr_base64 = 'data:image/png;base64,' . base64_encode($qr_content);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal mengambil QR Code dari server. Pastikan koneksi internet aktif.']);
        }

        return view('masterkelas.preview_qr', compact('kelas', 'qr_base64'));
    }

    // MONITORING KELAS
    public function monitoring(Request $request)
    {
        $tanggal_hari_ini = date('Y-m-d');
        $kode_cabang = $request->kode_cabang;

        // Ambil semua kelas (filter by cabang if selected)
        $query = MasterKelas::orderBy('nama_kelas');
        if ($kode_cabang) {
            $query->where('kode_cabang', $kode_cabang);
        }
        $master_kelas = $query->get();

        // Ambil jurnal hari ini
        $jurnal_hari_ini = JurnalMengajar::with('karyawan')
            ->where('tanggal', $tanggal_hari_ini)
            ->get();

        $cabang = DB::table('cabang')->get();

        return view('masterkelas.monitoring', compact('master_kelas', 'jurnal_hari_ini', 'tanggal_hari_ini', 'cabang', 'kode_cabang'));
    }
}
