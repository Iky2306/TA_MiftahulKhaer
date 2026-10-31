@extends('layouts.admin.tabler')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Data Jabatan Guru</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Jabatan
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 20%">Kode Jabatan</th>
                            <th>Nama Jabatan</th>
                            <th style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departemen as $d)
                            <tr>
                                <td>{{ $d->kode_dept }}</td>
                                <td>{{ $d->nama_dept }}</td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $d->kode_dept }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <!-- Tombol Delete -->
                                    <form action="{{ url('/departemen/' . $d->kode_dept . '/delete') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jabatan ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit Jabatan -->
                            <div class="modal fade" id="modalEdit{{ $d->kode_dept }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ url('/departemen/' . $d->kode_dept . '/update') }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5>Edit Jabatan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label>Kode Jabatan</label>
                                                    <input type="text" class="form-control" value="{{ $d->kode_dept }}" disabled>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Nama Jabatan</label>
                                                    <input type="text" name="nama_dept" class="form-control" value="{{ $d->nama_dept }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada data jabatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jabatan -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/departemen/store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5>Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Kode Jabatan</label>
                        <input type="text" name="kode_dept" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Nama Jabatan</label>
                        <input type="text" name="nama_dept" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
