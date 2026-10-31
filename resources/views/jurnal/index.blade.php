@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">E-Jurnal Mengajar</div>
    <div class="right"></div>
</div>
<style>
    .report-card { background: #fff; border-radius: 10px; margin-bottom: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; }
    .report-header { background: #1e293b; color: #fff; font-size: 13px; font-weight: 700; padding: 10px 14px; }
    .report-header small { font-weight: 400; opacity: 0.7; }

    .pertemuan-row { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; }
    .pertemuan-row:last-child { border-bottom: none; }
    .mapel-name { font-weight: 700; font-size: 13px; color: #1e293b; }
    .mapel-meta { font-size: 11px; color: #64748b; margin-top: 1px; }
    .materi-text { font-size: 11px; color: #475569; background: #f8fafc; padding: 5px 8px; border-radius: 4px; margin-top: 4px; border-left: 3px solid #cbd5e1; }

    .summary-card { background: #1e293b; color: #fff; border-radius: 10px; padding: 14px; margin-bottom: 12px; }
    .summary-card .row-stat { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
    .summary-card .row-stat .val { font-weight: 700; }
</style>
@endsection

@section('content')
<div class="section mt-2" style="margin-top: 70px !important; padding-bottom: 120px !important;">

    <div class="row">
        <div class="col-12 mt-1">
            @if(Session::get('success'))
                <div class="alert alert-success d-flex align-items-start mb-3" style="border-radius: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 14px; box-shadow: 0 2px 8px rgba(34, 197, 94, 0.08);">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 22px; color: #22c55e; margin-right: 10px; flex-shrink: 0; margin-top: 1px;"></ion-icon>
                    <div style="font-size: 12.5px; line-height: 1.4; font-weight: 500;">
                        {{ Session::get('success') }}
                    </div>
                </div>
            @endif

            @if(Session::get('error'))
                <div class="alert alert-danger d-flex align-items-start mb-3" style="border-radius: 12px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 14px; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.08);">
                    <ion-icon name="alert-circle-outline" style="font-size: 22px; color: #ef4444; margin-right: 10px; flex-shrink: 0; margin-top: 1px;"></ion-icon>
                    <div style="font-size: 12.5px; line-height: 1.4; font-weight: 500;">
                        {{ Session::get('error') }}
                    </div>
                </div>
            @endif

            @if(Session::get('warning'))
                <div class="alert alert-warning d-flex align-items-start mb-3" style="border-radius: 12px; background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px 14px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);">
                    <ion-icon name="warning-outline" style="font-size: 22px; color: #f59e0b; margin-right: 10px; flex-shrink: 0; margin-top: 1px;"></ion-icon>
                    <div style="font-size: 12.5px; line-height: 1.4; font-weight: 500;">
                        {{ Session::get('warning') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Aksi Utama --}}
    <div class="row mb-2">
        <div class="col-12">
            <a href="/guru/jurnal/create" class="btn btn-primary w-100" style="border-radius: 10px; font-weight: 700;">
                <ion-icon name="scan-circle-outline" style="font-size: 20px; vertical-align: middle; margin-right: 5px;"></ion-icon>
                Scan QR Kelas
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-3">
        <div class="col-12">
            <form action="/guru/jurnal" method="GET">
                <div class="row" style="margin-right: -5px; margin-left: -5px;">
                    <div class="col-5" style="padding-right: 5px; padding-left: 5px;">
                        <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" class="form-control" style="background: white; border-radius: 8px; font-size: 12px; padding: 6px 4px;" required>
                    </div>
                    <div class="col-5" style="padding-right: 5px; padding-left: 5px;">
                        <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" class="form-control" style="background: white; border-radius: 8px; font-size: 12px; padding: 6px 4px;" required>
                    </div>
                    <div class="col-2" style="padding-right: 5px; padding-left: 5px;">
                        <button class="btn btn-primary btn-block" type="submit" style="height:100%; border-radius: 8px; padding:0;">
                            <ion-icon name="search-outline"></ion-icon>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Guru & Download PDF --}}
    <div class="report-card">
        <div style="padding: 14px;">
            <div style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $karyawan->nama_lengkap }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $karyawan->jabatan }} &middot; {{ $karyawan->nama_dept }}</div>
            <div style="font-size: 12px; color: #64748b;">Periode: <strong>{{ date('d-m-Y', strtotime($dari_tanggal)) }} s/d {{ date('d-m-Y', strtotime($sampai_tanggal)) }}</strong></div>

            @php
                $downloadUrl = url("/guru/jurnal/cetak-pdf?dari_tanggal={$dari_tanggal}&sampai_tanggal={$sampai_tanggal}");
            @endphp

            <div style="margin-top: 10px;">
                 <a href="{{ $downloadUrl }}" class="btn btn-secondary btn-block" style="border-radius: 8px; font-weight: 700; font-size: 12px; margin-top: 0;">
                     <ion-icon name="download-outline" style="font-size: 14px; vertical-align: middle;"></ion-icon> Unduh PDF
                 </a>
            </div>
        </div>
    </div>

    @php
        $grouped = $jurnal->groupBy('kode_kelas');
        $kelasNo = 0;
    @endphp

    {{-- Ringkasan --}}
    <div class="summary-card">
        <div style="font-size: 13px; font-weight: 700; margin-bottom: 8px;">Ringkasan</div>
        <div class="row-stat"><span>Total Pertemuan</span> <span class="val">{{ $jurnal->count() }}</span></div>
        <div class="row-stat"><span>Total Kelas Diajar</span> <span class="val">{{ $grouped->count() }}</span></div>
    </div>

    @if($jurnal->count() > 0)
        @foreach($grouped as $kodeKelas => $jurnalKelas)
            @php
                $kelasNo++;
                $namaKelas = $jurnalKelas->first()->nama_kelas ?? $kodeKelas;
            @endphp

            <div class="report-card">
                <div class="report-header">
                    {{ $kelasNo }}. Kelas {{ $namaKelas }}
                    <small>({{ $jurnalKelas->count() }} pertemuan)</small>
                </div>

                @foreach($jurnalKelas as $d)
                    <div class="pertemuan-row">
                        <div class="mapel-name">{{ $d->nama_mapel }}</div>
                        <div class="mapel-meta">{{ date('d M Y', strtotime($d->tanggal)) }} &middot; {{ date('H:i', strtotime($d->jam_mulai)) }}-{{ date('H:i', strtotime($d->jam_selesai)) }}</div>
                        <div class="materi-text">{{ $d->materi }}</div>
                        @if($d->foto)
                        <div style="margin-top: 5px;">
                            <a href="{{ Storage::url('uploads/jurnal/' . $d->foto) }}" target="_blank"
                               style="font-size: 10px; color: #3b82f6;">
                                <ion-icon name="image-outline" style="vertical-align: middle;"></ion-icon> Lihat Foto
                            </a>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="text-center mt-4">
            <ion-icon name="document-text-outline" style="font-size: 64px; color: #cbd5e1;"></ion-icon>
            <p style="color: #64748b; font-size: 14px; margin-top: 10px;">Belum ada data jurnal mengajar pada periode ini.</p>
        </div>
    @endif
</div>
@endsection
