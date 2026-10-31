@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Manajemen Kelas (QR Jurnal)
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-kelas">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Tambah Kelas
                    </a>
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
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::get('warning'))
                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                @endif
            </div>
        </div>

        {{-- Card Filter Data --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="/panel/masterkelas" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Filter Lembaga</label>
                            <select name="lembaga" class="form-select">
                                <option value="">-- Semua Lembaga --</option>
                                <option value="MI" {{ request('lembaga') == 'MI' ? 'selected' : '' }}>MI</option>
                                <option value="MTs" {{ request('lembaga') == 'MTs' ? 'selected' : '' }}>MTs</option>
                                <option value="MA" {{ request('lembaga') == 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="SMK" {{ request('lembaga') == 'SMK' ? 'selected' : '' }}>SMK</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Filter Lokasi Sekolah</label>
                            <select name="kode_cabang" class="form-select">
                                <option value="">-- Semua Lokasi --</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }} ({{ $c->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cari Kelas / Kode</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama kelas atau kode..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                Filter
                            </button>
                            @if(request('lembaga') || request('kode_cabang') || request('search'))
                                <a href="/panel/masterkelas" class="btn btn-outline-secondary" title="Reset Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Lembaga</th>
                            <th>Lokasi Sekolah</th>
                            {{-- <th>Wali Kelas</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $k)
                        <tr>
                            <td><code>{{ $k->kode_kelas }}</code></td>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td><span class="badge bg-purple-lt">{{ $k->lembaga }}</span></td>
                            <td>
                                @if($k->kode_cabang)
                                    <span class="badge bg-blue-lt">{{ $k->kode_cabang }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">Belum diset</span>
                                @endif
                            </td>
                            {{-- 
                            <td>
                                @if($k->waliKelas)
                                    <span class="badge bg-green-lt">{{ $k->waliKelas->nama_lengkap }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">Belum ditentukan</span>
                                @endif
                            </td>
                            --}}
                            <td>
                                <a href="/panel/masterkelas/{{ $k->kode_kelas }}/mapel" class="btn btn-info btn-sm">Mata Pelajaran ({{ $k->mapels->count() }})</a>
                                <a href="/panel/masterkelas/{{ $k->kode_kelas }}/preview-qr" target="_blank" class="btn btn-dark btn-sm">Cetak QR</a>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-warning btn-sm edit-btn" 
                                       data-kode="{{ $k->kode_kelas }}" 
                                       data-nama="{{ $k->nama_kelas }}" 
                                       data-lembaga="{{ $k->lembaga }}"
                                       data-cabang="{{ $k->kode_cabang }}"
                                       data-walikelas="{{ $k->wali_kelas_nik }}"
                                       data-bs-toggle="modal" data-bs-target="#modal-edit">Edit</a>
                                    <form action="/panel/masterkelas/{{ $k->kode_kelas }}/delete" method="POST" onsubmit="return confirm('Yakin hapus kelas ini?');" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Data kelas tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal modal-blur fade" id="modal-kelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/panel/masterkelas/store" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lokasi Sekolah</label>
                        <select name="kode_cabang" id="create_kode_cabang" class="form-select" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }} ({{ $c->kode_cabang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lembaga</label>
                        <select name="lembaga" id="create_lembaga" class="form-select" required>
                            <option value="">-- Pilih Lembaga --</option>
                            <option value="MI">MI</option>
                            <option value="MTs">MTs</option>
                            <option value="MA">MA</option>
                            <option value="SMK">SMK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Kelas <span class="text-muted small">(Otomatis terisi & terkunci)</span></label>
                        <input type="text" class="form-control" name="kode_kelas" id="create_kode_kelas" placeholder="Pilih Lokasi & Lembaga terlebih dahulu..." readonly style="background-color: #f8fafc; cursor: not-allowed;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" placeholder="Cth: X IPA, X MPLB" required>
                    </div>
                    {{-- 
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas <span class="text-muted small">(Opsional)</span></label>
                        <select name="wali_kelas_nik" class="form-select">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($gurus as $g)
                                <option value="{{ $g->nik }}">{{ $g->nama_lengkap }} ({{ $g->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/panel/masterkelas/update" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kelas (Tidak bisa diedit)</label>
                        <input type="text" class="form-control" name="kode_kelas" id="edit_kode_kelas" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" id="edit_nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lembaga</label>
                        <select name="lembaga" id="edit_lembaga" class="form-select" required>
                            <option value="MI">MI</option>
                            <option value="MTs">MTs</option>
                            <option value="MA">MA</option>
                            <option value="SMK">SMK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Sekolah</label>
                        <select name="kode_cabang" id="edit_kode_cabang" class="form-select" required>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }} ({{ $c->kode_cabang }})</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- 
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas</label>
                        <select name="wali_kelas_nik" id="edit_wali_kelas_nik" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach ($gurus as $g)
                                <option value="{{ $g->nik }}">{{ $g->nama_lengkap }} ({{ $g->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Edit Modal binding
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_kode_kelas').value = this.getAttribute('data-kode');
                document.getElementById('edit_nama_kelas').value = this.getAttribute('data-nama');
                document.getElementById('edit_lembaga').value = this.getAttribute('data-lembaga');
                document.getElementById('edit_kode_cabang').value = this.getAttribute('data-cabang') || '';
                document.getElementById('edit_wali_kelas_nik').value = this.getAttribute('data-walikelas') || '';
            });
        });

        // Auto Generate Kode Kelas saat Lokasi atau Lembaga dipilih
        const selectCabang = document.getElementById('create_kode_cabang');
        const selectLembaga = document.getElementById('create_lembaga');
        const inputKodeKelas = document.getElementById('create_kode_kelas');

        function autoGenerateKode() {
            const cabang = selectCabang.value;
            const lembaga = selectLembaga.value;

            if (cabang && lembaga) {
                inputKodeKelas.placeholder = "Menggenerasi kode...";
                fetch(`/panel/masterkelas/generate-code?kode_cabang=${encodeURIComponent(cabang)}&lembaga=${encodeURIComponent(lembaga)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.kode_kelas) {
                            inputKodeKelas.value = data.kode_kelas;
                        }
                    })
                    .catch(err => {
                        console.error("Error generate code:", err);
                    });
            }
        }

        if (selectCabang && selectLembaga) {
            selectCabang.addEventListener('change', autoGenerateKode);
            selectLembaga.addEventListener('change', autoGenerateKode);
        }
    });
</script>
@endsection
