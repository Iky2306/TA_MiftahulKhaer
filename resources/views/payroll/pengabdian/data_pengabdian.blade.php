@extends('layouts.admin.tabler')

@section('content')
{{-- CDN Font Awesome & Tabler Icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

<style>
    th { background-color: #ffeb3b; text-align: center; vertical-align: middle; }
    td { vertical-align: middle; }
    i { font-size: 16px; }

    .list-nama {
        transition: all 0.3s ease;
        opacity: 1;
        max-height: 200px;
        overflow-y: auto;
    }
    .list-nama.hide {
        opacity: 0;
        max-height: 0;
        padding: 0;
        margin: 0;
        pointer-events: none;
    }
</style>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Data Pengabdian</div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPengabdian" id="btnTambah">
                    <i class="ti ti-plus"></i> Tambah Data Pengabdian
                </button>
            </div>

            <div class="card-body">
                {{-- Flash message --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @unless(Auth::guard('user')->user()->hasRole('staff'))
                {{-- Filter lokasi --}}
                <form method="GET" action="{{ route('payroll.pengabdian.data_pengabdian') }}" class="mb-3">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <select name="lokasi_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Semua Lokasi --</option>
                                <option value="Pusat" {{ request('lokasi_filter') == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                                <option value="Cabang" {{ request('lokasi_filter') == 'Cabang' ? 'selected' : '' }}>Cabang</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                                </span>
                                <input type="text" id="searchNamaPengabdian" class="form-control" placeholder="Cari nama...">
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                                </span>
                                <input type="text" id="searchNamaPengabdian" class="form-control" placeholder="Cari nama...">
                            </div>
                        </div>
                    </div>
                </div>
                @endunless

                {{-- Tabel utama --}}
                <div class="table-responsive">
                    <table id="tabelDataPengabdian" class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Lokasi</th>
                                <th colspan="3">Tunjangan</th>
                                <th colspan="2">Honorarium</th>
                                <th>Kehadiran</th>
                                <th colspan="3">Potongan</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <th>Pengabdian</th>
                                <th>Lainnya</th>
                                <th>Wkt</th>
                                <th>Stn</th>
                                <th>Stn</th>
                                <th>Dansos</th>
                                <th>FPMK</th>
                                <th>Lainnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengabdian as $index => $p)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $p->nama ?? '-' }}</td>
                                    <td>{{ $p->lokasi ?? '-' }}</td>
                                    <td>Rp {{ number_format($p->tunjangan_jabatan ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->tunjangan_pengabdian ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->tunjangan_lainnya ?? 0,0,',','.') }}</td>
                                    <td>{{ $p->honorarium_wkt ?? '-' }}</td>
                                    <td>Rp {{ number_format($p->honorarium_stn ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->kehadiran_stn ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->potongan_dansos ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->potongan_fpmk ?? 0,0,',','.') }}</td>
                                    <td>Rp {{ number_format($p->potongan_lainnya ?? 0,0,',','.') }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="{{ $p->id }}" title="Edit">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <form action="{{ route('payroll.pengabdian.delete', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4 text-muted">Belum ada data pengabdian</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit --}}
<div class="modal modal-blur fade" id="modalPengabdian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="formPengabdian" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Pengabdian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Nama dengan search --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" id="searchNama" class="form-control mb-2" placeholder="Cari nama...">
                            <select id="selectNama" class="form-select list-nama" size="5">
                                @foreach($karyawan as $k)
                                    <option value="{{ $k->nama_lengkap }}">{{ $k->nik }} - {{ $k->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="nama" id="inputNama">
                        </div>

                        {{-- Lokasi --}}
                        <div class="col-md-6">
                            @if(Auth::guard('user')->user()->hasRole('staff'))
                                @php
                                    $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
                                    $nama_cabang = $cabang ? $cabang->nama_cabang : '';
                                @endphp
                                <input type="hidden" name="lokasi" value="{{ $nama_cabang }}">
                            @else
                            <label class="form-label">Lokasi</label>
                            <select name="lokasi" class="form-select" required>
                                <option value="Pusat">Pusat</option>
                                <option value="Cabang">Cabang</option>
                            </select>
                            @endif
                        </div>

                        {{-- Tunjangan --}}
                        <div class="col-md-4">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <input type="text" name="tunjangan_jabatan" class="form-control rupiah" placeholder="Rp 0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tunjangan Pengabdian</label>
                            <input type="text" name="tunjangan_pengabdian" class="form-control rupiah" placeholder="Rp 0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tunjangan Lainnya</label>
                            <input type="text" name="tunjangan_lainnya" class="form-control rupiah" placeholder="Rp 0">
                        </div>

                        {{-- Honorarium --}}
                        <div class="col-md-3">
                            <label class="form-label">Honorarium (Wkt)</label>
                            <input type="number" name="honorarium_wkt" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Honorarium (Stn)</label>
                            <input type="text" name="honorarium_stn" class="form-control rupiah" placeholder="Rp 0">
                        </div>

                        {{-- Kehadiran --}}
                        <div class="col-md-3">
                            <label class="form-label">Kehadiran (Stn)</label>
                            <input type="text" name="kehadiran_stn" class="form-control rupiah" placeholder="Rp 0">
                        </div>

                        {{-- Potongan --}}
                        <div class="col-md-4">
                            <label class="form-label">Potongan Dansos</label>
                            <input type="text" name="potongan_dansos" class="form-control rupiah" placeholder="Rp 0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Potongan FPMK</label>
                            <input type="text" name="potongan_fpmk" class="form-control rupiah" placeholder="Rp 0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Potongan Lainnya</label>
                            <input type="text" name="potongan_lainnya" class="form-control rupiah" placeholder="Rp 0">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Format rupiah
    const formatRupiah = value => 'Rp ' + (parseInt(value) || 0).toLocaleString('id-ID');

    // Reset form Tambah
    document.getElementById('btnTambah').addEventListener('click', () => {
        const form = document.getElementById('formPengabdian');
        form.reset();
        form.action = "/payroll/pengabdian/store";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('modalTitle').innerText = 'Tambah Data Pengabdian';
        document.querySelectorAll('.rupiah').forEach(el => el.value = 'Rp 0');
    });

    // Edit button
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch('/payroll/pengabdian/edit/' + id)
                .then(res => res.json())
                .then(data => {
                    const form = document.getElementById('formPengabdian');
                    form.action = '/payroll/pengabdian/update/' + id;
                    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    document.getElementById('modalTitle').innerText = 'Edit Data Pengabdian';

                    document.getElementById('inputNama').value = data.nama || '';
                    document.getElementById('searchNama').value = data.nama || '';
                    form.querySelector('[name="lokasi"]').value = data.lokasi || 'Pusat';
                    form.querySelector('input[name="tunjangan_jabatan"]').value = formatRupiah(data.tunjangan_jabatan);
                    form.querySelector('input[name="tunjangan_pengabdian"]').value = formatRupiah(data.tunjangan_pengabdian);
                    form.querySelector('input[name="tunjangan_lainnya"]').value = formatRupiah(data.tunjangan_lainnya);
                    form.querySelector('input[name="honorarium_wkt"]').value = data.honorarium_wkt || 0;
                    form.querySelector('input[name="honorarium_stn"]').value = formatRupiah(data.honorarium_stn);
                    form.querySelector('input[name="kehadiran_stn"]').value = formatRupiah(data.kehadiran_stn);
                    form.querySelector('input[name="potongan_dansos"]').value = formatRupiah(data.potongan_dansos);
                    form.querySelector('input[name="potongan_fpmk"]').value = formatRupiah(data.potongan_fpmk);
                    form.querySelector('input[name="potongan_lainnya"]').value = formatRupiah(data.potongan_lainnya);

                    new bootstrap.Modal(document.getElementById('modalPengabdian')).show();
                })
                .catch(() => alert('Terjadi kesalahan saat mengambil data.'));
        });
    });

    // Rupiah input formatting
    document.querySelectorAll('.rupiah').forEach(el => {
        el.addEventListener('input', e => {
            let val = e.target.value.replace(/[^\d]/g, '');
            e.target.value = val ? 'Rp ' + parseInt(val).toLocaleString('id-ID') : 'Rp 0';
        });
    });

    // Search nama
    const searchInput = document.getElementById('searchNama');
    const selectNama = document.getElementById('selectNama');
    const inputNama = document.getElementById('inputNama');

    searchInput.addEventListener('keyup', () => {
        const filter = searchInput.value.toLowerCase();
        Array.from(selectNama.options).forEach(option => {
            option.style.display = option.text.toLowerCase().includes(filter) ? '' : 'none';
        });
        selectNama.classList.remove('hide');
    });

    selectNama.addEventListener('change', () => {
        const selected = selectNama.value;
        searchInput.value = selected;
        inputNama.value = selected;
        selectNama.classList.add('hide');
    });

    searchInput.addEventListener('focus', () => {
        selectNama.classList.remove('hide');
        searchInput.value = '';
        inputNama.value = '';
    });
    // Search nama pengabdian
    const searchPengabdian = document.getElementById('searchNamaPengabdian');
    if (searchPengabdian) {
        searchPengabdian.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = document.getElementById('tabelDataPengabdian');
            const rows = table.querySelectorAll('tbody tr');
            let visibleIndex = 0;

            rows.forEach(row => {
                // Kolom Nama ada di index ke-1 (0-based)
                const namaCell = row.cells[1];
                if (namaCell) {
                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(filter)) {
                        row.style.display = '';
                        visibleIndex++;
                        row.cells[0].textContent = visibleIndex;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }
});
</script>
@endsection
