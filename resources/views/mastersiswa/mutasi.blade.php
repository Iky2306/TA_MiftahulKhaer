@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Tools Mutasi & Kenaikan Kelas</h2>
                <div class="text-muted mt-1">Pindahkan atau luluskan siswa secara massal dalam satu klik</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="/panel/mastersiswa" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Kembali ke Data Siswa
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- Alert --}}
        <div class="row">
            <div class="col-12">
                @if (Session::get('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></div>
                            <div>{{ Session::get('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
                @if (Session::get('warning'))
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg></div>
                            <div>{{ Session::get('warning') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Ringkasan Kelas --}}
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Siswa Per Kelas</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Lembaga</th>
                            <th>Cabang</th>
                            <th>Jumlah Siswa Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kelas as $k)
                        @php $total = isset($siswaPerKelas[$k->kode_kelas]) ? $siswaPerKelas[$k->kode_kelas]->total : 0; @endphp
                        <tr>
                            <td><strong>{{ $k->nama_kelas }}</strong> <span class="text-muted">({{ $k->kode_kelas }})</span></td>
                            <td><span class="badge bg-purple-lt">{{ $k->lembaga }}</span></td>
                            <td><span class="badge bg-blue-lt">{{ $k->kode_cabang ?? '-' }}</span></td>
                            <td>
                                @if($total > 0)
                                    <span class="badge bg-green">{{ $total }} siswa</span>
                                @else
                                    <span class="text-muted">Kosong</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            {{-- Card Naik Kelas / Pindah Kelas --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 11l5 -5l5 5" /><path d="M7 17l5 -5l5 5" /></svg>
                            Naik Kelas / Pindah Kelas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Cara kerja:</strong> Semua siswa aktif di <strong>Kelas Asal</strong> akan dipindahkan ke <strong>Kelas Tujuan</strong> secara massal.
                            <br><span class="text-muted">Bisa juga digunakan untuk pindah jurusan, pindah lembaga, atau pindah lokasi sekolah.</span>
                        </div>
                        <form action="/panel/mastersiswa/naikkelas" method="POST" onsubmit="return confirm('Yakin ingin memindahkan SEMUA siswa aktif dari kelas asal ke kelas tujuan?');">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Kelas Asal</label>
                                <select name="kelas_asal" class="form-select" required>
                                    <option value="">-- Pilih Kelas Asal --</option>
                                    @foreach ($kelas as $k)
                                        @php $total = isset($siswaPerKelas[$k->kode_kelas]) ? $siswaPerKelas[$k->kode_kelas]->total : 0; @endphp
                                        <option value="{{ $k->kode_kelas }}">{{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }}) — {{ $total }} siswa</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="text-center my-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M18 13l-6 6" /><path d="M6 13l6 6" /></svg>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lokasi Sekolah Tujuan</label>
                                <select name="cabang_tujuan" id="naik_cabang" class="form-select" required>
                                    <option value="">-- Pilih Lokasi Tujuan --</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kelas Tujuan</label>
                                <select name="kelas_tujuan" id="naik_kelas" class="form-select" required disabled>
                                    <option value="">-- Pilih Lokasi Terlebih Dahulu --</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 11l5 -5l5 5" /><path d="M7 17l5 -5l5 5" /></svg>
                                Proses Naik/Pindah Kelas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Card Luluskan --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 13l-10 -13" /><path d="M2 9l5 -6l5 5l5 -5l5 6" /><path d="M12 22l0 -15" /></svg>
                            Luluskan Siswa
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <strong>Perhatian!</strong> Semua siswa aktif di kelas yang dipilih akan diubah statusnya menjadi <strong>"Lulus" (Alumni)</strong>.
                            <br><span class="text-muted">Siswa yang sudah lulus tidak akan muncul lagi di daftar absensi guru.</span>
                        </div>
                        <form action="/panel/mastersiswa/luluskan" method="POST" onsubmit="return confirm('⚠️ PERINGATAN: Semua siswa aktif di kelas ini akan di-LULUSKAN dan TIDAK BISA di-undo otomatis. Yakin?');">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Pilih Kelas yang Lulus</label>
                                <select name="kelas_lulus" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        @php $total = isset($siswaPerKelas[$k->kode_kelas]) ? $siswaPerKelas[$k->kode_kelas]->total : 0; @endphp
                                        @if($total > 0)
                                            <option value="{{ $k->kode_kelas }}">{{ $k->nama_kelas }} ({{ $k->lembaga }} - {{ $k->kode_cabang }}) — {{ $total }} siswa</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 13l-10 -13" /><path d="M2 9l5 -6l5 5l5 -5l5 6" /><path d="M12 22l0 -15" /></svg>
                                Proses Kelulusan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const kelasData = {!! json_encode($kelas->map(function($k) {
        return ['kode_kelas' => $k->kode_kelas, 'nama_kelas' => $k->nama_kelas, 'lembaga' => $k->lembaga, 'kode_cabang' => $k->kode_cabang];
    })->values()) !!};

    // Filter kelas tujuan berdasarkan cabang
    document.getElementById('naik_cabang').addEventListener('change', function() {
        const cabangValue = this.value;
        const kelasSelect = document.getElementById('naik_kelas');

        kelasSelect.innerHTML = '';

        if (!cabangValue) {
            kelasSelect.innerHTML = '<option value="">-- Pilih Lokasi Terlebih Dahulu --</option>';
            kelasSelect.disabled = true;
            return;
        }

        const filtered = kelasData.filter(k => k.kode_cabang === cabangValue);

        if (filtered.length === 0) {
            kelasSelect.innerHTML = '<option value="">-- Tidak ada kelas di lokasi ini --</option>';
            kelasSelect.disabled = true;
            return;
        }

        kelasSelect.disabled = false;
        kelasSelect.innerHTML = '<option value="">-- Pilih Kelas Tujuan --</option>';
        filtered.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.kode_kelas;
            opt.textContent = k.nama_kelas + ' (' + k.lembaga + ')';
            kelasSelect.appendChild(opt);
        });
    });
});
</script>
@endsection
