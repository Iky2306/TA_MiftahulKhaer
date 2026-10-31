@extends('layouts.admin.tabler')

@section('content')
<style>
    .modal-siswa-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e2e8f0;
    }
    .modal-siswa-card:hover {
        border-color: #3b82f6;
        background-color: #f0f9ff !important;
    }
    .step-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        margin-right: 6px;
    }
    .bulk-bar-sticky {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 14px;
    }
</style>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Data Master Siswa
                </h2>
                <div class="text-muted mt-1" style="font-size: 13px;">
                    Kelola data siswa per kelas, lembaga (MI/MTs/MA/SMK), kenaikan kelas & mutasi rombel massal secara praktis.
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modal-kenaikan-kelas">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" /><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" /></svg>
                        Kenaikan & Mutasi Kelas Massal
                    </button>
                    <a href="/panel/mastersiswa/export-excel?{{ http_build_query(request()->all()) }}" class="btn btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M8 11h8" /><path d="M8 15h8" /><path d="M11 11v8" /></svg>
                        Export Excel
                    </a>
                    <a href="/panel/mastersiswa/template" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                        Template Excel
                    </a>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-import-siswa">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                        Import Siswa
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-siswa">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Tambah Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                @if (Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (Session::get('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Perhatian!</strong> {{ Session::get('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="/panel/mastersiswa" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Filter Lembaga</label>
                            <select name="lembaga" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="MI" {{ request('lembaga') == 'MI' ? 'selected' : '' }}>MI</option>
                                <option value="MTs" {{ request('lembaga') == 'MTs' ? 'selected' : '' }}>MTs</option>
                                <option value="MA" {{ request('lembaga') == 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="SMK" {{ request('lembaga') == 'SMK' ? 'selected' : '' }}>SMK</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Filter Lokasi</label>
                            <select name="kode_cabang" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }} ({{ $c->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Filter Kelas</label>
                            <select name="kode_kelas" class="form-select">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->kode_kelas }}" {{ request('kode_kelas') == $k->kode_kelas ? 'selected' : '' }}>
                                        Kelas {{ $k->nama_kelas }} ({{ $k->kode_kelas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Filter Status</label>
                            <select name="status" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif / Alumni</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Cari NISN / Nama Siswa</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Ketik NISN / Nama..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    Cari
                                </button>
                                @if(request()->hasAny(['lembaga', 'kode_cabang', 'kode_kelas', 'status', 'search']))
                                    <a href="/panel/mastersiswa" class="btn btn-outline-secondary" title="Reset Filter">Reset</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table List Siswa --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Siswa (Total: {{ $siswas->total() }})</h3>

                {{-- Action bar pilihan massal --}}
                <div id="bulk-action-bar" class="d-none bulk-bar-sticky">
                    <span class="badge bg-primary text-white me-2 px-2 py-2" id="selected-count" style="font-size: 12px;">0 Siswa Dipilih</span>
                    <button type="button" class="btn btn-warning btn-sm me-1 text-dark fw-bold" id="btn-bulk-move">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-right-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 17l5 -5l-5 -5" /><path d="M21 12l-18 0" /><path d="M8 7l-5 5l5 5" stroke-opacity="0"/></svg>
                        Pindahkan yang Dipilih
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-bulk-graduate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /><path d="M16 11l2 2l4 -4" /></svg>
                        Luluskan / Nonaktifkan
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped table-hover" id="table-siswa">
                    <thead>
                        <tr>
                            <th class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" id="check-all-table" title="Pilih Semua">
                            </th>
                            <th class="w-1">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">L/P</th>
                            <th>Kelas</th>
                            <th>Lembaga / Cabang</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $s)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input check-siswa-row" value="{{ $s->id }}" data-nama="{{ $s->nama_siswa }}" data-kelas="{{ $s->kode_kelas }}">
                                </td>
                                <td>{{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}</td>
                                <td class="font-monospace text-muted">{{ $s->nisn ?? '-' }}</td>
                                <td class="fw-bold text-dark">{{ $s->nama_siswa }}</td>
                                <td class="text-center">
                                    @if($s->jenis_kelamin == 'L')
                                        <span class="badge bg-blue text-blue-fg">L</span>
                                    @else
                                        <span class="badge bg-pink text-pink-fg">P</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt">Kelas {{ $s->kelas->nama_kelas ?? $s->kode_kelas }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-lt me-1">{{ $s->kelas->lembaga ?? '-' }}</span>
                                    <span class="text-muted" style="font-size: 12px;">({{ $s->kelas->kode_cabang ?? '-' }})</span>
                                </td>
                                <td class="text-center">
                                    @if($s->status == 'aktif')
                                        <span class="badge bg-success-lt">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-lt">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        <button class="btn btn-info btn-sm btn-edit" 
                                                data-id="{{ $s->id }}"
                                                data-nisn="{{ $s->nisn }}"
                                                data-nama="{{ $s->nama_siswa }}"
                                                data-jk="{{ $s->jenis_kelamin }}"
                                                data-kelas="{{ $s->kode_kelas }}"
                                                data-status="{{ $s->status }}">
                                            Edit
                                        </button>
                                        <form action="/panel/mastersiswa/{{ $s->id }}/delete" method="POST" class="d-inline form-delete-siswa">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-confirm" data-nama="{{ $s->nama_siswa }}">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
                                        </div>
                                        <p class="empty-title">Data Siswa Belum Ada / Tidak Ditemukan</p>
                                        <p class="empty-subtitle text-muted">
                                            Gunakan tombol <strong>Import Data Siswa</strong> atau <strong>Tambah Siswa Manual</strong> untuk memasukkan data siswa.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswas->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between py-2">
                    <p class="m-0 text-muted" style="font-size: 13px;">
                        Menampilkan <span>{{ $siswas->firstItem() }}</span> hingga <span>{{ $siswas->lastItem() }}</span> dari <span>{{ $siswas->total() }}</span> siswa
                    </p>
                    <div>
                        {{ $siswas->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Kenaikan Kelas & Mutasi Siswa Massal --}}
<div class="modal modal-blur fade" id="modal-kenaikan-kelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/panel/mastersiswa/bulk-promote" method="POST" id="form-kenaikan-kelas">
                @csrf
                <div class="modal-header bg-warning-lt">
                    <h5 class="modal-title fw-bold text-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" /><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" /></svg>
                        Proses Kenaikan Kelas & Mutasi Siswa Massal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3" style="font-size: 13px;">
                        📌 <strong>Urutan Kenaikan Kelas Berantai yang Dihimbau (Agar Siswa Tidak Tercampur):</strong>
                        <ol class="mb-1 ps-3 mt-1 fw-bold text-dark">
                            <li>Langkah 1: Luluskan tingkat akhir terlebih dahulu (misal: Luluskan Kelas XII / IX / VI).</li>
                            <li>Langkah 2: Naikkan kelas di bawahnya berurutan dari atas (XI ➔ XII, lalu X ➔ XI).</li>
                            <li>Langkah 3: Masukkan/Import siswa baru ke kelas paling awal (Kelas X / VII / I).</li>
                        </ol>
                        <small class="text-muted">💡 Jika ada siswa yang <strong>tinggal kelas</strong>, cukup **hapus centang** pada nama siswa tersebut di bawah.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label required fw-bold">
                                <span class="badge bg-warning text-dark step-badge">1</span> Pilih Kelas Asal (Rombel Saat Ini)
                            </label>
                            <select id="modal_kelas_asal" class="form-select">
                                <option value="">-- Pilih Kelas Asal --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->kode_kelas }}" {{ request('kode_kelas') == $k->kode_kelas ? 'selected' : '' }}>
                                        Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label required fw-bold">
                                <span class="badge bg-warning text-dark step-badge">2</span> Pilih Aksi Proses
                            </label>
                            <select name="aksi" id="modal_aksi_tujuan" class="form-select" required>
                                <option value="pindah">Pindahkan / Naikkan ke Kelas Tujuan</option>
                                <option value="lulus">Luluskan / Set Non-Aktifkan Siswa</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="group_kelas_tujuan">
                        <label class="form-label required fw-bold">
                            <span class="badge bg-warning text-dark step-badge">3</span> Pilih Kelas Tujuan (Rombel Baru / Major Target)
                        </label>
                        <select name="kode_kelas_tujuan" id="modal_kelas_tujuan" class="form-select">
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach ($kelases as $k)
                                <option value="{{ $k->kode_kelas }}">
                                    Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }})
                                </option>
                            @endforeach
                        </select>
                        <div id="warning_kelas_tujuan" class="mt-2 d-none"></div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">
                                Daftar Siswa yang Dipilih (<span id="modal_total_siswa" class="text-primary fw-bold">0</span> Siswa)
                            </label>
                            <div>
                                <button type="button" class="btn btn-outline-secondary btn-sm me-1" id="btn-select-all-modal">Centang Semua</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-deselect-all-modal">Hapus Centang</button>
                            </div>
                        </div>
                        <div class="border rounded p-3 bg-light" style="max-height: 260px; overflow-y: auto;" id="container_siswa_list">
                            <div class="text-center py-4 text-muted" id="placeholder_modal_siswa">
                                👈 Silakan pilih <strong>Kelas Asal</strong> terlebih dahulu atau centang siswa dari tabel utama.
                            </div>
                            <div id="modal_siswa_checkboxes" class="row g-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold" id="btn-submit-promote">
                        🚀 Jalankan Kenaikan / Pindah Kelas Massal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Siswa --}}
<div class="modal modal-blur fade" id="modal-siswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/panel/mastersiswa/store" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Siswa Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NISN <span class="text-muted font-weight-normal" style="font-size: 11px;">(Kosongkan untuk buat otomatis oleh sistem)</span></label>
                        <input type="text" class="form-control" name="nisn" placeholder="Kosongkan untuk generate otomatis">
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Nama Siswa</label>
                        <input type="text" class="form-control" name="nama_siswa" placeholder="Contoh: AFIFAH AINI LUBIS" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jenis Kelamin (L/P)</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Pilih Kelas</label>
                            <select name="kode_kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->kode_kelas }}">
                                        Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Siswa</label>
                        <select name="status" class="form-select">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Siswa --}}
<div class="modal modal-blur fade" id="modal-edit-siswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/panel/mastersiswa/update" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NISN (Opsional)</label>
                        <input type="text" class="form-control" name="nisn" id="edit_nisn">
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Nama Siswa</label>
                        <input type="text" class="form-control" name="nama_siswa" id="edit_nama_siswa" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Pilih Kelas</label>
                            <select name="kode_kelas" id="edit_kode_kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->kode_kelas }}">
                                        Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Siswa</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import Siswa --}}
<div class="modal modal-blur fade" id="modal-import-siswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/panel/mastersiswa/import" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Siswa dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size: 13px;">
                        📌 <strong>Petunjuk Import:</strong>
                        <ol class="mb-0 ps-3 mt-1">
                            <li>Unduh file template excel dengan menekan tombol <strong>Download Template Excel</strong>.</li>
                            <li>Isi kolom <code>nisn</code>, <code>nama_siswa</code>, dan <code>jenis_kelamin</code> (L/P).</li>
                            <li>Pilih Kelas tujuan di bawah ini sebelum mengunggah file.</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Tujuan Kelas</label>
                        <select name="kode_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach ($kelases as $k)
                                <option value="{{ $k->kode_kelas }}" {{ request('kode_kelas') == $k->kode_kelas ? 'selected' : '' }}>
                                    Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Upload File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Mulai Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Hapus data siswa konfirmasi SweetAlert2
        $(document).on('click', '.btn-delete-confirm', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Data Siswa?',
                html: 'Yakin ingin menghapus data siswa <b>' + nama + '</b>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Edit siswa single modal
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var nisn = $(this).data('nisn');
            var nama = $(this).data('nama');
            var jk = $(this).data('jk');
            var kelas = $(this).data('kelas');
            var status = $(this).data('status');

            $('#edit_id').val(id);
            $('#edit_nisn').val(nisn);
            $('#edit_nama_siswa').val(nama);
            $('#edit_jenis_kelamin').val(jk);
            $('#edit_kode_kelas').val(kelas);
            $('#edit_status').val(status);

            $('#modal-edit-siswa').modal('show');
        });

        // ----------------------------------------------------
        // LOGIK MUTASI / KENAIKAN KELAS MASSAL (CHECKBOX & MODAL)
        // ----------------------------------------------------

        // Update jumlah tercentang & Tampilkan Bulk Action Bar
        function updateBulkBar() {
            var checkedCount = $('.check-siswa-row:checked').length;
            if (checkedCount > 0) {
                $('#bulk-action-bar').removeClass('d-none');
                $('#selected-count').text(checkedCount + ' Siswa Dipilih');
            } else {
                $('#bulk-action-bar').addClass('d-none');
            }
        }

        // Check All di Tabel
        $('#check-all-table').on('change', function() {
            $('.check-siswa-row').prop('checked', $(this).is(':checked'));
            updateBulkBar();
        });

        $('.check-siswa-row').on('change', function() {
            var totalRows = $('.check-siswa-row').length;
            var totalChecked = $('.check-siswa-row:checked').length;
            $('#check-all-table').prop('checked', totalRows === totalChecked);
            updateBulkBar();
        });

        // Tombol "Pindahkan yang Dipilih" dari Bulk Action Bar
        $('#btn-bulk-move').on('click', function() {
            populateModalFromTableChecked('pindah');
        });

        // Tombol "Luluskan / Nonaktifkan" dari Bulk Action Bar
        $('#btn-bulk-graduate').on('click', function() {
            populateModalFromTableChecked('lulus');
        });

        // Isi modal dari centang tabel utama
        function populateModalFromTableChecked(aksi) {
            var checkedBoxes = $('.check-siswa-row:checked');
            if (checkedBoxes.length === 0) return;

            $('#modal_aksi_tujuan').val(aksi).trigger('change');
            $('#modal_siswa_checkboxes').empty();
            $('#placeholder_modal_siswa').addClass('d-none');

            var count = 0;
            checkedBoxes.each(function() {
                var id = $(this).val();
                var nama = $(this).data('nama');
                var html = `
                    <div class="col-md-6">
                        <div class="form-check modal-siswa-card bg-white p-2 rounded">
                            <input class="form-check-input modal-siswa-chk" type="checkbox" name="siswa_ids[]" value="${id}" id="chk_modal_${id}" checked>
                            <label class="form-check-label fw-bold text-dark ms-1" for="chk_modal_${id}">
                                ${nama}
                            </label>
                        </div>
                    </div>
                `;
                $('#modal_siswa_checkboxes').append(html);
                count++;
            });

            updateModalCount();
            $('#modal-kenaikan-kelas').modal('show');
        }

        // Function untuk validasi ketersediaan kelas tujuan
        function validateTargetClass() {
            var aksi = $('#modal_aksi_tujuan').val();
            if (aksi === 'lulus') {
                $('#warning_kelas_tujuan').addClass('d-none').empty();
                $('#btn-submit-promote').prop('disabled', false);
                return;
            }

            var asal = $('#modal_kelas_asal').val();
            var tujuan = $('#modal_kelas_tujuan').val();

            if (!tujuan) {
                $('#warning_kelas_tujuan').addClass('d-none').empty();
                $('#btn-submit-promote').prop('disabled', false);
                return;
            }

            if (asal && asal === tujuan) {
                $('#warning_kelas_tujuan').removeClass('d-none').html(`
                    <div class="alert alert-danger border-danger mb-0 py-2" style="font-size: 12px;">
                        ⛔ <strong>DIKUNCI:</strong> Kelas Asal dan Kelas Tujuan tidak boleh sama! Silakan pilih kelas tujuan yang berbeda.
                    </div>
                `);
                $('#btn-submit-promote').prop('disabled', true);
                return;
            }

            $.get('/panel/mastersiswa/count-by-kelas/' + tujuan, function(res) {
                if (res.count > 0) {
                    var namaKelasText = $('#modal_kelas_tujuan option:selected').text();
                    $('#warning_kelas_tujuan').removeClass('d-none').html(`
                        <div class="alert alert-danger border-danger mb-0 py-2" style="font-size: 12px;">
                            ⛔ <strong>TOMBOL PROSES DIKUNCI:</strong> ${namaKelasText} saat ini masih terisi <strong>${res.count} siswa aktif</strong>.<br>
                            Anda harus memindahkan/meluluskan siswa yang ada di ${namaKelasText} terlebih dahulu sebelum memindahkan kelas ini agar data siswa tidak tercampur.
                        </div>
                    `);
                    $('#btn-submit-promote').prop('disabled', true);
                } else {
                    var namaKelasText = $('#modal_kelas_tujuan option:selected').text();
                    $('#warning_kelas_tujuan').removeClass('d-none').html(`
                        <div class="alert alert-success border-success mb-0 py-2" style="font-size: 12px;">
                            ✅ <strong>Siap Diproses:</strong> ${namaKelasText} saat ini kosong (0 siswa) dan siap menerima siswa baru.
                        </div>
                    `);
                    $('#btn-submit-promote').prop('disabled', false);
                }
            }).fail(function() {
                $('#btn-submit-promote').prop('disabled', false);
            });
        }

        // Aksi dropdown change (pindah vs lulus)
        $('#modal_aksi_tujuan').on('change', function() {
            var aksi = $(this).val();
            if (aksi === 'lulus') {
                $('#group_kelas_tujuan').addClass('d-none');
                $('#modal_kelas_tujuan').prop('required', false);
                $('#warning_kelas_tujuan').addClass('d-none').empty();
                $('#btn-submit-promote').removeClass('btn-warning').addClass('btn-danger').html('🎓 Luluskan / Non-Aktifkan Siswa yang Dipilih').prop('disabled', false);
            } else {
                $('#group_kelas_tujuan').removeClass('d-none');
                $('#modal_kelas_tujuan').prop('required', true);
                $('#btn-submit-promote').removeClass('btn-danger').addClass('btn-warning').html('🚀 Jalankan Kenaikan / Pindah Kelas Massal');
                validateTargetClass();
            }
        });

        // Cek apakah Kelas Tujuan sudah terisi siswa aktif (agar tidak tercampur)
        $('#modal_kelas_tujuan').on('change', function() {
            validateTargetClass();
        });

        // Select Kelas Asal Dropdown di Modal -> Fetch via AJAX
        $('#modal_kelas_asal').on('change', function() {
            var kode_kelas = $(this).val();
            validateTargetClass();

            if (!kode_kelas) {
                $('#modal_siswa_checkboxes').empty();
                $('#placeholder_modal_siswa').removeClass('d-none');
                updateModalCount();
                return;
            }

            $('#placeholder_modal_siswa').removeClass('d-none').html('⏳ Memuat daftar siswa...');
            $('#modal_siswa_checkboxes').empty();

            $.get('/panel/mastersiswa/get-by-kelas/' + kode_kelas, function(data) {
                $('#placeholder_modal_siswa').addClass('d-none');
                if (data.length === 0) {
                    $('#modal_siswa_checkboxes').html('<div class="col-12 text-center py-3 text-danger">⚠️ Tidak ada siswa aktif ditemukan di kelas asal ini.</div>');
                    updateModalCount();
                    return;
                }

                data.forEach(function(s) {
                    var jkBadge = s.jenis_kelamin === 'L' 
                        ? '<span class="badge bg-blue text-blue-fg ms-1">L</span>' 
                        : '<span class="badge bg-pink text-pink-fg ms-1">P</span>';

                    var html = `
                        <div class="col-md-6">
                            <div class="form-check modal-siswa-card bg-white p-2 rounded d-flex align-items-center">
                                <input class="form-check-input modal-siswa-chk me-2" type="checkbox" name="siswa_ids[]" value="${s.id}" id="chk_modal_${s.id}" checked>
                                <label class="form-check-label fw-bold text-dark flex-grow-1" for="chk_modal_${s.id}">
                                    ${s.nama_siswa} ${jkBadge}
                                    <div class="text-muted font-monospace" style="font-size: 11px;">NISN: ${s.nisn || '-'}</div>
                                </label>
                            </div>
                        </div>
                    `;
                    $('#modal_siswa_checkboxes').append(html);
                });

                updateModalCount();
            }).fail(function() {
                $('#placeholder_modal_siswa').removeClass('d-none').html('❌ Gagal memuat data siswa.');
            });
        });

        // Event listener centang di modal
        $(document).on('change', '.modal-siswa-chk', function() {
            updateModalCount();
        });

        function updateModalCount() {
            var total = $('.modal-siswa-chk:checked').length;
            $('#modal_total_siswa').text(total);
        }

        $('#btn-select-all-modal').on('click', function() {
            $('.modal-siswa-chk').prop('checked', true);
            updateModalCount();
        });

        $('#btn-deselect-all-modal').on('click', function() {
            $('.modal-siswa-chk').prop('checked', false);
            updateModalCount();
        });

        // Submit validation dengan SweetAlert2 (TANPA POPUP NATIVE CONFIRM / ALERT)
        $('#form-kenaikan-kelas').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var checked = $('.modal-siswa-chk:checked').length;

            if (checked === 0) {
                Swal.fire({
                    title: 'Pilih Siswa',
                    text: 'Pilih setidaknya 1 siswa yang akan diproses!',
                    icon: 'warning',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            var aksi = $('#modal_aksi_tujuan').val();
            if (aksi === 'pindah') {
                var tujuan = $('#modal_kelas_tujuan').val();
                if (!tujuan) {
                    Swal.fire({
                        title: 'Kelas Tujuan Belum Dipilih',
                        text: 'Silakan pilih Kelas Tujuan terlebih dahulu!',
                        icon: 'warning',
                        confirmButtonColor: '#3b82f6'
                    });
                    return false;
                }
            }

            var titleText = aksi === 'pindah' ? 'Konfirmasi Kenaikan / Pindah Kelas' : 'Konfirmasi Kelulusan Siswa';
            var confirmBtnText = aksi === 'pindah' ? '🚀 Ya, Pindahkan Sekarang!' : '🎓 Ya, Luluskan Sekarang!';
            var confirmBtnColor = aksi === 'pindah' ? '#f59e0b' : '#ef4444';

            Swal.fire({
                title: titleText,
                html: 'Yakin ingin memproses <b>' + checked + ' siswa</b>?<br><small class="text-muted">Data kelas / status siswa akan langsung diperbarui.</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Pre-fetch jika kelas disaring lewat URL
        if ($('#modal_kelas_asal').val()) {
            $('#modal_kelas_asal').trigger('change');
        }
    });
</script>
@endpush
