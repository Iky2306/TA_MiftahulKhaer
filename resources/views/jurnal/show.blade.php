@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/guru/jurnal" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Detail Jurnal</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section mt-2" style="margin-top: 70px !important; padding-bottom: 120px !important;">
    <div class="card" style="border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none;">
        <div class="card-body">
            <div class="text-center mb-3">
                <ion-icon name="book-outline" style="font-size: 48px; color: #3b82f6; margin-bottom: 10px;"></ion-icon>
                <h3 style="color: #1e3a8a; font-size: 20px; font-weight: 700; margin-bottom: 5px;">{{ $jurnal->nama_mapel }}</h3>
                <span class="badge bg-primary" style="font-size: 13px; border-radius: 10px; padding: 5px 12px;">Kelas {{ $jurnal->master_kelas->nama_kelas ?? $jurnal->kode_kelas }}</span>
            </div>

            <ul class="listview image-listview text" style="border-radius: 12px; overflow: hidden; margin-top:20px; border: 1px solid #f1f5f9;">
                <li>
                    <div class="item">
                        <div class="in">
                            <div style="color: #64748b; font-weight: 600;">Tanggal</div>
                            <span class="text-muted" style="font-weight: 500;">{{ date('d F Y', strtotime($jurnal->tanggal)) }}</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <div class="in">
                            <div style="color: #64748b; font-weight: 600;">Waktu Mengajar</div>
                            <span class="text-muted" style="font-weight: 500;">
                                {{ date('H:i', strtotime($jurnal->jam_mulai)) }} - {{ date('H:i', strtotime($jurnal->jam_selesai)) }}
                            </span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <div class="in" style="align-items:flex-start; padding-top: 10px; padding-bottom: 10px;">
                            <div style="min-width: 120px; color: #64748b; font-weight: 600;">Materi / Kegiatan</div>
                            <span class="text-muted text-right" style="white-space: pre-wrap; font-weight: 500;">{{ $jurnal->materi }}</span>
                        </div>
                    </div>
                </li>
            </ul>

            @if($jurnal->foto)
            <div class="mt-4">
                <h4 style="font-size: 14px; color: #1e3a8a; font-weight: 600; margin-bottom: 10px;">Foto Dokumentasi</h4>
                <div class="text-center">
                    <img src="{{ Storage::url('uploads/jurnal/' . $jurnal->foto) }}" alt="Dokumentasi" class="img-fluid rounded"
                         style="max-height: 400px; width: 100%; object-fit: contain; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;">
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
