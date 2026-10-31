@extends('layouts.admin.tabler')

@section('content')
<div class="container">
    <h3>Pengaturan Gaji Guru per Golongan</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('payroll.guru.setingGaji.simpan') }}">
        @csrf

        @for ($i = 1; $i <= 3; $i++)
            @php
                $data = $settingGaji->firstWhere('golongan', $i);
            @endphp
            <div class="card mb-3">
                <div class="card-header">Golongan {{ $i }}</div>
                <div class="card-body row">
                    <input type="hidden" name="golongan[]" value="{{ $i }}">

                    <div class="col-md-3">
                        <label>Kehadiran</label>
                        <input type="number" name="transport[]" class="form-control" value="{{ $data->transport ?? 0 }}">
                    </div>

                    <!-- <div class="col-md-3">
                        <label>simpanan koperasi</label>
                        <input type="number" name="simpanan_koperasi[]" class="form-control" value="{{ $data->simpanan_koperasi ?? 0 }}">
                    </div> -->

                    <div class="col-md-3">
                        <label>Dana Sosial</label>
                        <input type="number" name="dana_sosial[]" class="form-control" value="{{ $data->dana_sosial ?? 0 }}">
                    </div>

                    <div class="col-md-3">
                        <label>Gaji Pokok</label>
                        <input type="number" name="gaji_pokok[]" class="form-control" value="{{ $data->gaji_pokok ?? 0 }}">
                    </div>
                </div>
            </div>
        @endfor

        <button type="submit" class="btn btn-primary">Simpan Semua</button>
    </form>
</div>
@endsection
