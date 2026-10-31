@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Pengajuan</div>
    <div class="right"></div>
</div>
<style>
    .pengajuan-wrapper {
        padding: 16px;
        margin-top: 70px;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .menu-card {
        background: #fff;
        border-radius: 12px;
        padding: 14px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #1e293b !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1.5px solid #f1f5f9;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }

    .menu-card:active {
        transform: scale(0.96);
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }

    .menu-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .menu-card-icon ion-icon {
        font-size: 24px;
    }

    .menu-card-title {
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        color: #1e293b;
        line-height: 1.3;
    }

    .riwayat-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 16px;
        text-decoration: none !important;
        color: #1e293b !important;
    }

    .riwayat-btn .left-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .riwayat-btn ion-icon {
        font-size: 22px;
        color: #3b82f6;
    }

    .riwayat-btn .label {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }

    .riwayat-btn .sublabel {
        font-size: 10px;
        color: #64748b;
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
</style>
@endsection

@section('content')
<div class="pengajuan-wrapper" style="padding-bottom: 100px;">

    @if(Session::get('success'))
        <div class="alert alert-success" style="border-radius: 8px; font-size: 12px; margin-bottom: 12px;">
            {{ Session::get('success') }}
        </div>
    @endif

    {{-- Riwayat --}}
    <a href="/presensi/izin" class="riwayat-btn">
        <div class="left-info">
            <ion-icon name="time-outline"></ion-icon>
            <div>
                <div class="label">Riwayat Pengajuan</div>
                <div class="sublabel">Lihat semua riwayat pengajuan saya</div>
            </div>
        </div>
        <ion-icon name="chevron-forward-outline" style="color:#94a3b8; font-size: 18px;"></ion-icon>
    </a>

    {{-- Buat Pengajuan Baru --}}
    <div class="section-title">Buat Pengajuan Baru</div>

    <div class="menu-grid">

        {{-- Izin Tidak Hadir --}}
        <a href="/izinabsen" class="menu-card" id="menu-izin">
            <div class="menu-card-icon" style="background: #dbeafe;">
                <ion-icon name="document-outline" style="color: #1d4ed8;"></ion-icon>
            </div>
            <div class="menu-card-title">Izin</div>
        </a>

        {{-- Cuti --}}
        <a href="/izincuti" class="menu-card" id="menu-cuti">
            <div class="menu-card-icon" style="background: #fef9c3;">
                <ion-icon name="calendar-outline" style="color: #b45309;"></ion-icon>
            </div>
            <div class="menu-card-title">Cuti</div>
        </a>

        {{-- Sakit --}}
        <a href="/izinsakit" class="menu-card" id="menu-sakit">
            <div class="menu-card-icon" style="background: #fee2e2;">
                <ion-icon name="medkit-outline" style="color: #b91c1c;"></ion-icon>
            </div>
            <div class="menu-card-title">Sakit</div>
        </a>

        {{-- Konfirmasi Kehadiran --}}
        <a href="/konfirmasikehadiran" class="menu-card" id="menu-konfirmasi">
            <div class="menu-card-icon" style="background: #dcfce7;">
                <ion-icon name="checkmark-circle-outline" style="color: #15803d;"></ion-icon>
            </div>
            <div class="menu-card-title">Konfirmasi<br>Kehadiran</div>
        </a>

    </div>
</div>
@endsection
