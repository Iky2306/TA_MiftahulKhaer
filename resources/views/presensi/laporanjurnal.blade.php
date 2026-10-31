@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Laporan E-Jurnal Guru
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="/panel/jurnal/cetaklaporan" id="frmLaporan" target="_blank" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Dari Tanggal</label>
                                        <input type="date" name="dari_tanggal" id="dari_tanggal" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Sampai Tanggal</label>
                                        <input type="date" name="sampai_tanggal" id="sampai_tanggal" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Pilih Guru</label>
                                        <select name="nik" id="nik" class="form-select">
                                            <option value="">Pilih Guru</option>
                                            @foreach ($karyawan as $d)
                                                <option value="{{ $d->nik }}">{{ $d->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" name="cetak" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                        Pratinjau
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" name="exportpdf" class="btn btn-danger w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>
                                        Cetak PDF
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#frmLaporan").submit(function(e) {
            var dari_tanggal = $("#dari_tanggal").val();
            var sampai_tanggal = $("#sampai_tanggal").val();
            var nik = $("#nik").val();
            if (dari_tanggal == "") {
                Swal.fire({
                    title: 'Peringatan!'
                    , text: 'Dari Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#dari_tanggal").focus();
                });
                return false;
            } else if (sampai_tanggal == "") {
                Swal.fire({
                    title: 'Peringatan!'
                    , text: 'Sampai Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#sampai_tanggal").focus();
                });
                return false;
            } else if (nik == "") {
                Swal.fire({
                    title: 'Peringatan!'
                    , text: 'Guru Harus Dipilih !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#nik").focus();
                });
                return false;
            }
        });
    });
</script>
@endpush
