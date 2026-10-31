@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Konfigurasi Poin & Peringkat (Leaderboard)
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-8 col-lg-7">
                <div class="card">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                            Pengaturan Periode Peringkat Guru & Piket
                        </h3>
                    </div>
                    <div class="card-body">
                        @if (Session::get('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                </div>
                                <div>{{ Session::get('success') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                        @endif

                        @if (Session::get('warning'))
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                                </div>
                                <div>{{ Session::get('warning') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                        @endif

                        <form action="/konfigurasi/updateleaderboard" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Mulai Perhitungan</label>
                                    <input type="date" class="form-control" name="tgl_mulai" value="{{ $tgl_mulai }}" required>
                                    <small class="text-muted">Tanggal efektif perhitungan skor poin dimulai.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Selesai Perhitungan</label>
                                    <input type="date" class="form-control" name="tgl_selesai" value="{{ $tgl_selesai }}" required>
                                    <small class="text-muted">Tanggal akhir batas periode perhitungan poin.</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Periode Peringkat</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>🟢 Aktif (Poin Diperhitungkan)</option>
                                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>🔴 Non-Aktif / Belum Dimulai (Poin Ditangguhkan)</option>
                                </select>
                                <small class="text-muted">Jika non-aktif, dashboard akan menampilkan pesan informasi kustom tanpa memunculkan poin mentah.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Pesan Informasi Tampilan Dashboard</label>
                                <textarea name="pesan" class="form-control" rows="3" placeholder="Contoh: Tanggal mulai perhitungan skor poin belum dimulai atau telah berakhir.">{{ $pesan }}</textarea>
                                <small class="text-muted">Pesan ini akan otomatis muncul pada dashboard jika periode penilaian belum mulai, sudah lewat, atau di-nonaktifkan oleh admin.</small>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M14 4l0 4l-6 0l0 -4"></path>
                                    </svg>
                                    Simpan Konfigurasi Poin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-5">
                <div class="card">
                    <div class="card-header bg-info-lt">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                            Panduan Fitur Konfigurasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled space-y-2">
                            <li class="d-flex align-items-start">
                                <span class="badge bg-blue me-2">1</span>
                                <div><strong>Kontrol Admin Penuh:</strong> Admin dapat menentukan kapan periode kompetisi/penilaian poin dimulai dan berakhir.</div>
                            </li>
                            <li class="d-flex align-items-start mt-3">
                                <span class="badge bg-blue me-2">2</span>
                                <div><strong>Penanganan Tanggal:</strong> Jika hari ini berada di luar rentang tanggal yang diatur, sistem otomatis menangguhkan peringkat dan menampilkan pesan informasi kustom.</div>
                            </li>
                            <li class="d-flex align-items-start mt-3">
                                <span class="badge bg-blue me-2">3</span>
                                <div><strong>Keamanan Data:</strong> Pengubahan tanggal konfigurasi tidak akan merusak atau menghapus riwayat presensi / jurnal lama di database.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
