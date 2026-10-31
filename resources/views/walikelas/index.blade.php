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

<style>
    .kelasku-wrapper {
        padding: 16px;
        margin-top: 70px;
        padding-bottom: 100px;
    }

    .wk-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 16px;
        margin-bottom: 18px;
        border: 1px solid #f1f5f9;
    }

    .wk-badge {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid #bfdbfe;
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .menu-card {
        background: #fff;
        border-radius: 14px;
        padding: 16px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #1e293b !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1.5px solid #f1f5f9;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
    }

    .menu-card:active {
        transform: scale(0.96);
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }

    .menu-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .menu-card-icon ion-icon {
        font-size: 26px;
    }

    .menu-card-title {
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        color: #1e293b;
        line-height: 1.3;
    }

    .badge-soon {
        position: absolute;
        top: 6px;
        right: 6px;
        font-size: 8px;
        padding: 2px 6px;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="kelasku-wrapper">
    
    {{-- Header Info Kelas --}}
    <div class="wk-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <span class="wk-badge">WALI KELAS</span>
            </div>
            @if($kelasDiampu->count() > 1)
                <div>
                    <form action="/guru/walikelas/monitoring" method="GET" id="formSwitchKelas">
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

        <h3 class="mb-1 fw-bold text-dark" style="font-size: 20px;">KELAS {{ $kelasAktif->nama_kelas }}</h3>
        <div class="text-muted" style="font-size: 12px;">
            Lembaga: <strong>{{ $kelasAktif->lembaga }}</strong> &middot; Kode: <code>{{ $kelasAktif->kode_kelas }}</code>
        </div>
    </div>

    {{-- Title Section --}}
    <div class="section-title">Fitur Wali Kelas</div>

    {{-- Grid Menu Card (Identik Halaman Pengajuan) --}}
    <div class="menu-grid">
        
        {{-- Menu 1: E-Jurnal --}}
        <a href="/guru/walikelas/jurnal?kode_kelas={{ $kelasAktif->kode_kelas }}" class="menu-card" id="menu-ejurnal">
            <div class="menu-card-icon" style="background: #dbeafe;">
                <ion-icon name="journal-outline" style="color: #1d4ed8;"></ion-icon>
            </div>
            <div class="menu-card-title">E-Jurnal</div>
        </a>

        {{-- Menu 2: Data Siswa --}}
        <a href="/guru/walikelas/siswa?kode_kelas={{ $kelasAktif->kode_kelas }}" class="menu-card" id="menu-datasiswa">
            <div class="menu-card-icon" style="background: #fef3c7;">
                <ion-icon name="people-outline" style="color: #d97706;"></ion-icon>
            </div>
            <div class="menu-card-title">Data Siswa</div>
        </a>

    </div>

</div>
@endsection
