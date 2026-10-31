@extends('layouts.admin.tabler')

@section('content')
<style>
    .btn-sm-danger-fixed {
        padding: 4px 8px;
        font-size: 0.75rem;
    }
</style>
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Data Guru</div>
                <div class="card-actions">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Tambah Guru
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @unless(Auth::guard('user')->user()->hasRole('staff'))
                <div class="table-responsive">
                    <form method="GET" action="{{ route('payroll.guru.data') }}" class="mb-3">
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
                                    <input type="text" id="searchNamaGuru" class="form-control" placeholder="Cari nama guru...">
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                <div class="table-responsive">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                                    </span>
                                    <input type="text" id="searchNamaGuru" class="form-control" placeholder="Cari nama guru...">
                                </div>
                            </div>
                        </div>
                    </div>
                @endunless

                    <table id="tabelDataGuru" class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Lokasi Sekolah</th>
                                <th>Jabatan</th>
                                <th>Tunjangan Jabatan</th>
                                <th>Potongan</th>
                                <th>Jam Mengajar</th>
                                <th>Golongan</th>
                                <th>Gaji Pokok</th>
                                <th class="text-center" style="width: 8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guru as $index => $g)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $g->karyawan_nik }}</td>
                                <td>{{ $g->nama_lengkap }}</td>
                                <td>{{ $g->lokasi_sekolah }}</td>
                                <td>{{ $g->jabatan }}</td>
                                <td>Rp. {{ number_format($g->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($g->potongan,0,',','.') }}</td>
                                <td>{{ $g->jam_mengajar }}</td>
                                <td>Golongan {{ $g->golongan }}</td>
                                <td>Rp. {{ number_format(($g->jam_mengajar * ($g->gaji_pokok ?? 0)), 0, ',', '.') }}</td>
                                
                                <td class="text-center">
    <!-- Tombol Edit -->
    <a href="{{ route('payroll.guru.edit', $g->id) }}" class="btn btn-warning btn-sm" title="Edit">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M4 20h4l10.5-10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
            <line x1="13.5" y1="6.5" x2="17.5" y2="10.5" />
        </svg>
    </a>

    <!-- Tombol Hapus -->
    <form action="{{ route('payroll.guru.delete', $g->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7h16" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-3h6v3" />
            </svg>
        </button>
    </form>
</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">Belum ada data guru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal modal-blur fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('payroll.guru.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Guru</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::guard('user')->user()->hasRole('staff'))
                        @php
                            $cabang = DB::table('cabang')->where('kode_cabang', Auth::guard('user')->user()->kode_cabang)->first();
                            $nama_cabang = $cabang ? $cabang->nama_cabang : '';
                        @endphp
                        <input type="hidden" name="lokasi_sekolah" value="{{ $nama_cabang }}">
                    @else
                    <div class="mb-3">
                        <label class="form-label">Lokasi Sekolah</label>
                        <select name="lokasi_sekolah" class="form-select" required>
                            <option value="Pusat">Pusat</option>
                            <option value="Cabang">Cabang</option>
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-select" required>
                            @foreach($jabatan as $j)
                                <option value="{{ $j->nama_dept }}">{{ $j->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Golongan</label>
                        <select name="golongan" class="form-select" required>
                            <option value="">-- Pilih Golongan --</option>
                            <option value="1">Golongan 1</option>
                            <option value="2">Golongan 2</option>
                            <option value="3">Golongan 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Jabatan</label>
                        <input type="text" name="tunjangan_jabatan" class="form-control rupiah" placeholder="Rp 0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mengajar</label>
                        <input type="number" name="jam_mengajar" class="form-control" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan</label>
                        <input type="text" name="potongan" class="form-control rupiah" placeholder="Rp 0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchNamaGuru');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = document.getElementById('tabelDataGuru');
            const rows = table.querySelectorAll('tbody tr');
            let visibleIndex = 0;

            rows.forEach(row => {
                // Kolom Nama ada di index ke-2 (0-based)
                const namaCell = row.cells[2];
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
@endpush
@endsection

