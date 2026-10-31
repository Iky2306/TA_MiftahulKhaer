@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Monitoring Live</div>
                <h2 class="page-title">
                    Aktivitas Mengajar Kelas (Hari Ini: {{ date('d M Y') }})
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {{-- Filter Lokasi --}}
                    @foreach($cabang as $c)
                        <a href="/panel/monitoringkelas?kode_cabang={{ $c->kode_cabang }}" 
                           class="btn {{ $kode_cabang == $c->kode_cabang ? 'btn-primary' : 'btn-outline-primary' }} d-none d-sm-inline-block">
                            {{ $c->nama_cabang }}
                        </a>
                    @endforeach
                    @if($kode_cabang)
                        <a href="/panel/monitoringkelas" class="btn btn-outline-secondary d-none d-sm-inline-block">Semua Lokasi</a>
                    @endif
                    <a href="/panel/monitoringkelas{{ $kode_cabang ? '?kode_cabang='.$kode_cabang : '' }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                        Refresh
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    @foreach(['MI', 'MTs', 'MA', 'SMK'] as $lembaga)
                        <li class="nav-item">
                            <a href="#tab-{{ $lembaga }}" class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab">
                                Lembaga {{ $lembaga }} 
                                <span class="badge bg-blue-lt ms-2">{{ $master_kelas->where('lembaga', $lembaga)->count() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @foreach(['MI', 'MTs', 'MA', 'SMK'] as $lembaga)
                        <div class="tab-pane {{ $loop->first ? 'active show' : '' }}" id="tab-{{ $lembaga }}">
                            <div class="row row-cards g-2">
                                @php
                                    $kelas_lembaga = $master_kelas->where('lembaga', $lembaga);
                                @endphp
                                @forelse($kelas_lembaga as $k)
                                    @php
                                        $jurnal_kelas_ini = $jurnal_hari_ini->where('kode_kelas', $k->kode_kelas)->all();
                                    @endphp
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                        <div class="card h-100 {{ count($jurnal_kelas_ini) > 0 ? 'border-primary' : 'border-0 border-top border-3 border-secondary' }} shadow-sm" style="border-radius: 8px;">
                                            <div class="card-header py-2 px-3 {{ count($jurnal_kelas_ini) > 0 ? 'bg-primary text-white' : 'bg-light' }}" style="min-height: auto; border-bottom: none; border-radius: 8px 8px 0 0;">
                                                <h3 class="card-title text-truncate" style="font-size: 13px; margin: 0; font-weight: 700; width:100%;">
                                                    {{ $k->nama_kelas }}
                                                    @if($k->kode_cabang)
                                                        <span style="font-size: 9px; opacity: 0.7;">({{ $k->kode_cabang }})</span>
                                                    @endif
                                                </h3>
                                                @if(count($jurnal_kelas_ini) > 0)
                                                <div class="card-actions">
                                                    <!-- <span class="badge bg-green ms-2 badge-blink" style="animation: blinker 1.5s linear infinite; font-size:9px; padding:2px 4px;">LIVE</span> -->
                                                </div>
                                                @endif
                                            </div>
                                            <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                @if(count($jurnal_kelas_ini) > 0)
                                                    @foreach($jurnal_kelas_ini as $j)
                                                        <div class="{{ !$loop->last ? 'mb-2 pb-2 border-bottom' : '' }}">
                                                            <div class="d-flex align-items-center">
                                                                @if($j->karyawan && $j->karyawan->foto)
                                                                    <span class="avatar avatar-sm me-2" style="background-image: url('{{ Storage::url('uploads/karyawan/' . $j->karyawan->foto) }}'); border-radius: 4px;"></span>
                                                                @else
                                                                    <span class="avatar avatar-sm me-2 bg-blue-lt" style="border-radius: 4px;">{{ substr($j->karyawan->nama_lengkap ?? '?', 0, 2) }}</span>
                                                                @endif
                                                                <div style="flex:1; min-width:0;">
                                                                    <div class="font-weight-bold text-truncate" style="font-size: 11px; color:#1e293b; line-height: 1.2;">{{ $j->karyawan->nama_lengkap ?? 'Unknown' }}</div>
                                                                    <div class="text-primary text-truncate" style="font-size:10px; font-weight:600; line-height: 1.2;">{{ $j->nama_mapel }}</div>
                                                                    <div class="text-muted" style="font-size:10px; line-height: 1.2;">
                                                                        🕒 {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ $j->jam_selesai ? date('H:i', strtotime($j->jam_selesai)) : 'Skrg' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- Absensi siswa telah dihapus --}}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center text-muted" style="font-size: 11px; font-style:italic;">
                                                        -- Kosong --
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-4">
                                        Belum ada data kelas untuk lembaga {{ $lembaga }}.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blinker {
  50% { opacity: 0; }
}
</style>
@endsection
