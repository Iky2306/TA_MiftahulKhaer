@extends('layouts.admin.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Edit Data Guru</div>
            </div>
            <div class="card-body">
                <form action="{{ route('payroll.guru.update', $guru->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Guru</label>
                        <input type="text" class="form-control" value="{{ $guru->nama_lengkap }}" disabled>
                        <input type="hidden" name="karyawan_id" value="{{ $guru->karyawan_nik }}">
                    </div>

                    @if(Auth::guard('user')->user()->hasRole('staff'))
                        <input type="hidden" name="lokasi_sekolah" value="{{ $guru->lokasi_sekolah }}">
                    @else
                    <div class="mb-3">
                        <label class="form-label">Lokasi Sekolah</label>
                        <select name="lokasi_sekolah" class="form-select" required>
                            <option value="Pusat" {{ $guru->lokasi_sekolah == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                            <option value="Cabang" {{ $guru->lokasi_sekolah == 'Cabang' ? 'selected' : '' }}>Cabang</option>
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-select" required>
                            @foreach($jabatan as $j)
                                <option value="{{ $j->nama_dept }}" {{ $guru->jabatan == $j->nama_dept ? 'selected' : '' }}>{{ $j->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Golongan</label>
                        <select name="golongan" class="form-select" required>
                            <option value="1" {{ $guru->golongan == 1 ? 'selected' : '' }}>Golongan 1</option>
                            <option value="2" {{ $guru->golongan == 2 ? 'selected' : '' }}>Golongan 2</option>
                            <option value="3" {{ $guru->golongan == 3 ? 'selected' : '' }}>Golongan 3</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tunjangan Jabatan</label>
                        <input type="text" name="tunjangan_jabatan" class="form-control rupiah" value="Rp {{ number_format($guru->tunjangan_jabatan,0,',','.') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jam Mengajar</label>
                        <input type="number" name="jam_mengajar" class="form-control" value="{{ $guru->jam_mengajar }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan</label>
                        <input type="text" name="potongan" class="form-control rupiah" 
                            value="Rp {{ number_format($guru->potongan,0,',','.') }}">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payroll.guru.data') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
