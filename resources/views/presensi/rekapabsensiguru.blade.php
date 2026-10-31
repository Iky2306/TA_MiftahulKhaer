@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Rekap Absensi Bulanan</div>
    <div class="right"></div>
</div>
<style>
    .rekap-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 16px;
        margin-bottom: 14px;
    }
    .table-rekap {
        font-size: 11px;
        width: 100%;
        border-collapse: collapse;
    }
    .table-rekap th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 8px 6px;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
    }
    .table-rekap td {
        padding: 8px 6px;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        color: #334155;
    }
</style>
@endsection

@section('content')
<div class="section mt-2" style="margin-top: 70px !important; padding-bottom: 120px !important;">

    {{-- Filter Form --}}
    <div class="row mb-3">
        <div class="col-12">
            <form action="/presensi/rekapabsensi" method="GET">
                <div class="row" style="margin-right: -5px; margin-left: -5px;">
                    <div class="col-5" style="padding-right: 5px; padding-left: 5px;">
                        <select name="bulan" class="form-control" style="background: white; border-radius: 8px; font-size: 13px; height: 42px; font-weight: 600;" required>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ sprintf('%02d', $bulan) == sprintf('%02d', $m) ? 'selected' : '' }}>
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

    {{-- Info Card & Download PDF --}}
    <div class="rekap-card">
        <div class="fw-bold text-dark" style="font-size: 16px;">{{ $karyawan->nama_lengkap }}</div>
        <div class="text-muted" style="font-size: 12px; margin-top: 2px;">
            NIK: <code>{{ $karyawan->nik }}</code> &middot; Jabatan: {{ $karyawan->nama_dept }}
        </div>
        <div class="text-muted" style="font-size: 12px; margin-top: 2px;">
            Periode: <strong>{{ $namabulan[(int)$bulan] }} {{ $tahun }}</strong>
        </div>

        @php
            $downloadUrl = url("/presensi/downloadrekapabsensi?bulan={$bulan}&tahun={$tahun}");
        @endphp

        <div class="mt-3">
            <a href="{{ $downloadUrl }}" class="btn btn-primary w-100" target="_blank" download style="border-radius: 8px; font-weight: 700; font-size: 13px;">
                <ion-icon name="download-outline" style="font-size: 18px; vertical-align: middle; margin-right: 4px;"></ion-icon> Unduh PDF Laporan
            </a>
        </div>
    </div>

    {{-- Tabel Preview Presensi --}}
    <div class="rekap-card p-0" style="overflow: hidden;">
        <div class="p-3 bg-light border-bottom">
            <h4 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Detail Presensi Harian</h4>
        </div>
        <div class="table-responsive">
            <table class="table-rekap">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensi as $d)
                        <tr {{ $d->status == 'libur' ? 'style=background-color:#f8fafc;' : '' }}>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                            <td>{{ $d->jam_in ?? '-' }}</td>
                            <td>{{ isset($d->jam_out) && $d->jam_out != null ? $d->jam_out : ($d->status == 'h' ? 'Belum Absen' : '-') }}</td>
                            <td>
                                @if($d->status == 'h')
                                    <span class="badge bg-success-lt" style="font-size: 9px;">H</span>
                                @elseif($d->status == 'libur')
                                    <span class="badge bg-secondary-lt" style="font-size: 9px;">LIBUR</span>
                                @else
                                    <span class="badge bg-warning-lt" style="font-size: 9px;">{{ strtoupper($d->status) }}</span>
                                @endif
                            </td>
                            <td class="text-start" style="font-size: 10px;">
                                @if($d->status == 'h')
                                    <span class="text-success">Hadir</span>
                                @else
                                    <span class="text-muted">{{ $d->keterangan ?? '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-muted">Belum ada data presensi pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
