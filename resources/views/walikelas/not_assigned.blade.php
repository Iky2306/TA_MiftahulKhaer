@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Kelasku</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section mt-2" style="margin-top: 80px !important; padding-bottom: 120px !important;">
    <div class="card text-center p-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div style="font-size: 48px; color: #94a3b8; margin-bottom: 10px;">
            <ion-icon name="alert-circle-outline"></ion-icon>
        </div>
        <h3 class="fw-bold text-dark mb-1" style="font-size: 16px;">Anda Belum Ditugaskan</h3>
        <p class="text-muted" style="font-size: 12px; line-height: 1.5;">
            Saat ini NIK Anda belum terdaftar sebagai <strong>Wali Kelas</strong> di sistem Manajemen Kelas. Silakan hubungi Administrator Kurikulum jika ada kekeliruan.
        </p>
        <div class="mt-2">
            <a href="/dashboard" class="btn btn-primary btn-sm px-4" style="border-radius: 8px; font-weight: 700;">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
