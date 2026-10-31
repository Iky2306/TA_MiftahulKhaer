@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/guru/walikelas/monitoring?kode_kelas={{ $kelasAktif->kode_kelas }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">E-Jurnal Kelas {{ $kelasAktif->nama_kelas }}</div>
    <div class="right"></div>
</div>

<style>
    .wk-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 16px;
        margin-bottom: 14px;
    }
    .wk-badge {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid #bfdbfe;
    }
    .stat-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 8px 4px;
        text-align: center;
        border: 1px solid #e2e8f0;
        min-height: 56px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-num {
        font-size: 16px;
        font-weight: 800;
        color: #1e40af;
        line-height: 1.1;
    }
    .stat-label {
        font-size: 9px;
        color: #64748b;
        font-weight: 600;
        margin-top: 3px;
        line-height: 1.1;
    }
    .jurnal-item {
        border-left: 4px solid #206bc4;
        background: #ffffff;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .materi-box {
        background: #f1f5f9;
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 12px;
        color: #334155;
        margin-top: 6px;
    }
    .siswa-absen-box {
        background: #fff1f2;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 11px;
        color: #be123c;
        margin-top: 6px;
        border: 1px dashed #fecdd3;
    }
</style>
@endsection

@section('content')
<div class="section mt-2" style="margin-top: 70px !important; padding-bottom: 120px !important;">
    
    {{-- Header Info Wali Kelas --}}
    <div class="wk-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <span class="wk-badge">E-JURNAL MENGAJAR GURU</span>
            </div>
            @if($kelasDiampu->count() > 1)
                <div>
                    <form action="/guru/walikelas/jurnal" method="GET" id="formSwitchKelas">
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <select name="kode_kelas" class="form-select form-select-sm" onchange="document.getElementById('formSwitchKelas').submit();" style="font-size: 11px; border-radius: 6px;">
                            @foreach($kelasDiampu as $k)
                                <option value="{{ $k->kode_kelas }}" {{ $k->kode_kelas == $kelasAktif->kode_kelas ? 'selected' : '' }}>
                                    Kelas {{ $k->nama_kelas }} ({{ $k->kode_kelas }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

        <h3 class="mb-1 fw-bold text-dark" style="font-size: 18px;">KELAS {{ $kelasAktif->nama_kelas }}</h3>
        <div class="text-muted" style="font-size: 12px;">
            Lembaga: <strong>{{ $kelasAktif->lembaga }}</strong> &middot; Kode: <code>{{ $kelasAktif->kode_kelas }}</code>
        </div>

        {{-- Row Mini Stats --}}
        <div class="row g-2 mt-2">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num">{{ $stats['total_jurnal'] }}</div>
                    <div class="stat-label">Total Jurnal</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num">{{ $stats['total_guru'] }}</div>
                    <div class="stat-label">Guru Mengajar</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-num">{{ $stats['total_mapel'] }}</div>
                    <div class="stat-label">Mata Pelajaran</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="row mb-3">
        <div class="col-12">
            <form action="/guru/walikelas/jurnal" method="GET">
                <input type="hidden" name="kode_kelas" value="{{ $kelasAktif->kode_kelas }}">
                <div class="row" style="margin-right: -5px; margin-left: -5px;">
                    <div class="col-5" style="padding-right: 5px; padding-left: 5px;">
                        <select name="bulan" class="form-control" style="background: white; border-radius: 8px; font-size: 13px; height: 42px; font-weight: 600;" required>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ $namabulan[$m] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-5" style="padding-right: 5px; padding-left: 5px;">
                        <select name="tahun" class="form-control" style="background: white; border-radius: 8px; font-size: 13px; height: 42px; font-weight: 600;" required>
                            @php $currYr = date('Y'); @endphp
                            @for($y=$currYr-1; $y<=$currYr+1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-2" style="padding-right: 5px; padding-left: 5px;">
                        <button class="btn btn-primary btn-block w-100" type="submit" style="height: 42px; border-radius: 8px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <ion-icon name="search-outline" style="font-size: 20px;"></ion-icon>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Jurnal Mengajar --}}
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <h4 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Riwayat Jurnal ({{ $namabulan[(int)$bulan] }} {{ $tahun }})</h4>
        <span class="badge bg-secondary" style="font-size: 10px;">{{ $jurnals->count() }} Entry</span>
    </div>

    @forelse($jurnals as $j)
        <div class="jurnal-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="d-flex align-items-center">
                    @if($j->foto_guru)
                        <img src="{{ Storage::url('uploads/karyawan/' . $j->foto_guru) }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 800; font-size: 12px;">
                            {{ strtoupper(substr($j->nama_guru, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ $j->nama_guru }}</div>
                        <div class="text-muted" style="font-size: 11px;">{{ date('d-m-Y', strtotime($j->tanggal)) }}</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-blue-lt" style="font-size: 11px;">{{ $j->nama_mapel }}</span>
                    <div class="text-muted mt-1" style="font-size: 10px;">
                        <ion-icon name="time-outline" style="vertical-align: middle;"></ion-icon>
                        {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}
                    </div>
                </div>
            </div>

            <div class="materi-box">
                <div class="fw-bold text-dark" style="font-size: 11px; margin-bottom: 2px;">Materi / Pembahasan:</div>
                <div>{{ $j->materi }}</div>
            </div>

            @if(!empty($j->siswa_absen))
                <div class="siswa-absen-box">
                    <strong><ion-icon name="warning-outline" style="vertical-align: middle;"></ion-icon> Siswa Absen / Izin:</strong> {{ $j->siswa_absen }}
                </div>
            @endif

            @if(!empty($j->foto))
                <div class="mt-2">
                    <a href="{{ Storage::url('uploads/jurnal/' . $j->foto) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 10px; border-radius: 4px;">
                        <ion-icon name="image-outline" style="vertical-align: middle;"></ion-icon> Lihat Foto Bukti
                    </a>
                </div>
            @endif
        </div>
    @empty
        <div class="wk-card text-center text-muted py-4">
            <ion-icon name="document-text-outline" style="font-size: 40px; color: #cbd5e1;"></ion-icon>
            <div class="mt-2" style="font-size: 13px; font-weight: 600;">Belum ada jurnal mengajar terisi</div>
            <div style="font-size: 11px; opacity: 0.8;">Untuk periode {{ $namabulan[(int)$bulan] }} {{ $tahun }}</div>
        </div>
    @endforelse

</div>
@endsection
