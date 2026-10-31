@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Jadwal Mengajar</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px; margin-bottom: 80px; padding-bottom: 20px;">
    <div class="col">
        @if ($source == null)
            <div class="alert alert-warning">
                <p style="text-align: center; font-weight: bold">Jadwal mengajar belum disetting</p>
                <p style="text-align: center">Silakan hubungi admin untuk mengatur jadwal mengajar Anda</p>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 1.2rem;">Jadwal Mengajar</h3>
                    <div class="card-subtitle" style="font-size: 0.9rem;">
                        @if($source == 'individu')
                            (PENGATURAN INDIVIDU)
                        @else
                            (PENGATURAN DEPARTEMEN)
                        @endif
                    </div>
                </div>
                <div class="card-body pb-0">
                    @if(count($hariMengajar) > 0)
                        <!-- Wrap tabel dalam container scrollable -->
                        <div style="overflow-x: auto; max-height: 50vh;">
                            <table class="table table-bordered table-sm mb-1" style="min-width: 600px;">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th style="width: 15%;">Hari</th>
                                        <th style="width: 14%;">Awal Masuk</th>
                                        <th style="width: 14%;">Jam Masuk</th>
                                        <th style="width: 14%;">Akhir Masuk</th>
                                        <th style="width: 14%;">Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hariMengajar as $hari)
                                        @php
                                            $jam = $jamKerja[$hari] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $hari }}</td>
                                            <td>{{ $jam ? $jam->awal_jam_masuk : '-' }}</td>
                                            <td>{{ $jam ? $jam->jam_masuk : '-' }}</td>
                                            <td>{{ $jam ? $jam->akhir_jam_masuk : '-' }}</td>
                                            <td>{{ $jam ? $jam->jam_pulang : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p style="text-align: center; margin: 0">Tidak ada hari mengajar yang diatur</p>
                        </div>
                    @endif
                    
                    <div class="alert alert-info mt-2 mb-0 py-2" style="font-size: 0.85rem;">
                        <strong>Informasi:</strong>
                        <ul class="mb-0">
                            <li>Jadwal mengajar dapat berubah sesuai kebijakan sekolah</li>
                            <li>Perubahan jadwal akan diinformasikan sebelumnya</li>
                            <li>Jika ada pertukaran hari segera hubungi admin</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Mobile-first responsive styles */
    .table th, .table td {
        vertical-align: middle;
        padding: 6px 4px;
        font-size: 0.8rem;
    }
    
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* Responsive text sizes */
    @media (max-width: 480px) {
        .card-title {
            font-size: 1.1rem !important;
        }
        
        .card-subtitle {
            font-size: 0.8rem !important;
        }
        
        .alert ul {
            padding-left: 15px;
        }
    }
</style>
@endsection