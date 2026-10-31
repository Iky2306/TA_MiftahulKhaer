@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/konfirmasikehadiran" class="headerButton">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle" style="font-weight: 600; letter-spacing: 0.5px;">Riwayat Konfirmasi Kehadiran</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<style>
    .excel-ribbon-toolbar {
        background-color: #f3f2f1;
        border: 1px solid #edebe9;
        border-radius: 8px;
        padding: 12px;
        margin-top: 65px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        margin-bottom: 15px;
    }
    .excel-ribbon-title {
        font-size: 11px;
        font-weight: bold;
        color: #1e74fd;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .excel-select {
        height: 38px !important;
        border: 1px solid #d2d0ce !important;
        border-radius: 4px !important;
        font-size: 13px !important;
        color: #323130 !important;
        background-color: #ffffff !important;
        padding: 0 10px !important;
        box-shadow: none !important;
    }
    .excel-btn-search {
        background-color: #1e74fd !important;
        border-color: #1e74fd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        height: 38px;
        border-radius: 4px !important;
        font-size: 13px !important;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(30, 116, 253, 0.2) !important;
        transition: all 0.2s ease !important;
    }
    .excel-btn-search:hover {
        background-color: #0e65e6 !important;
        border-color: #0e65e6 !important;
        box-shadow: 0 3px 6px rgba(14, 101, 230, 0.3) !important;
    }
    
    /* Excel Table Grid Style */
    .excel-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #ffffff;
        border: 1px solid #d2d0ce;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 100px;
    }
    .excel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #323130;
        min-width: 700px;
    }
    .excel-table th {
        background-color: #1e74fd; /* SiGuru Blue */
        color: #ffffff;
        font-weight: 600;
        padding: 10px 8px;
        border: 1px solid #0e65e6;
        text-align: center;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
    }
    .excel-table td {
        padding: 8px 6px;
        border: 1px solid #edebe9;
        text-align: center;
        vertical-align: middle;
    }
    .excel-table tbody tr:nth-child(even) {
        background-color: #faf9f8;
    }
    .excel-table tbody tr:hover {
        background-color: #e8f1ff;
        outline: 1px solid #1e74fd;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        font-weight: bold;
        font-size: 10px;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .badge-approved { background-color: #e2f0d9; color: #385723; }
    .badge-pending { background-color: #fff2cc; color: #7f6000; }
    .badge-rejected { background-color: #fce4d6; color: #c00000; }
    
    .text-left { text-align: left !important; }
    .text-bold { font-weight: bold; }
    .text-success { color: #385723 !important; }
    .text-danger { color: #c00000 !important; }
</style>

<div class="section" style="margin-top:0px; padding-bottom:0px; padding-left:0; padding-right:0;">
    <div class="excel-ribbon-toolbar">
        <div class="excel-ribbon-title">
            <ion-icon name="funnel-outline"></ion-icon> Filter Konfirmasi
        </div>
        <form method="GET" action="/konfirmasikehadiran/riwayat">
            <div class="row">
                <div class="col-8">
                    <div class="form-group mb-0">
                        @php
                            $namabulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        @endphp
                        <select name="bulan" id="bulan" class="form-control excel-select">
                            <option value="">Pilih Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option {{ Request('bulan') == $i ? 'selected' : '' }} value="{{ $i }}">
                                    {{ $namabulanIndo[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-0">
                        <select name="tahun" id="tahun" class="form-control excel-select">
                            <option value="">Tahun</option>
                            @php
                                $tahun_awal = 2025;
                                $tahun_sekarang = date('Y');
                                for ($t = $tahun_awal; $t <= $tahun_sekarang; $t++) {
                                    $selected = (Request('tahun') == $t) ? 'selected' : '';
                                    echo "<option $selected value='$t'>$t</option>";
                                }
                            @endphp
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button class="btn excel-btn-search w-100">
                        <ion-icon name="search-outline"></ion-icon> Cari Data
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(Session::get('success'))
        <div class="alert alert-success" style="border-radius:8px; font-size:12px; margin-bottom:12px;">{{ Session::get('success') }}</div>
    @endif

    @if($data->count() > 0)
        @php
            $namaHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        @endphp
        <div class="excel-table-container">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">Hari & Tanggal</th>
                        <th style="width: 13%">Jam Masuk</th>
                        <th style="width: 13%">Jam Pulang</th>
                        <th style="width: 12%">Status</th>
                        <th style="width: 32%">Alasan & Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                        @php
                            $hari = $namaHari[date('w', strtotime($d->tanggal))];
                            
                            if ($d->status === 'approved') {
                                $appLabel = 'Disetujui';
                                $appBadge = 'badge-approved';
                            } elseif ($d->status === 'rejected') {
                                $appLabel = 'Ditolak';
                                $appBadge = 'badge-rejected';
                            } else {
                                $appLabel = 'Menunggu';
                                $appBadge = 'badge-pending';
                            }
                        @endphp
                        <tr>
                            <td class="text-bold" style="background-color: #f3f2f1; color: #605e5c;">{{ $loop->iteration }}</td>
                            <td class="text-bold">{{ $hari }}, {{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td class="text-bold text-success">{{ date('H:i', strtotime($d->jam_masuk)) }}</td>
                            <td class="text-bold text-danger">{{ date('H:i', strtotime($d->jam_keluar)) }}</td>
                            <td><span class="status-badge {{ $appBadge }}">{{ $appLabel }}</span></td>
                            <td class="text-left">
                                <span class="text-bold">Alasan:</span> {{ $d->alasan }}
                                @if($d->catatan_admin)
                                    <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #edebe9; font-size: 10px; color: #605e5c;">
                                        <span class="text-bold" style="color: #c00000;">Catatan Admin:</span> {{ $d->catatan_admin }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-outline-warning">
            <p class="m-0">Belum ada riwayat pengajuan konfirmasi kehadiran.</p>
        </div>
    @endif
</div>
@endsection
