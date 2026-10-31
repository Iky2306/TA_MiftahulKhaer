@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview
                    </div>
                    <h2 class="page-title">
                        Rekap Presensi Hari Ini {{ date('d-m-Y', strtotime(date('Y-m-d'))) }}
                    </h2>
                </div>

            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">

            <div class="row">

                <div class="col-md-4 col-xl-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-fingerprint" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3"></path>
                                            <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6"></path>
                                            <path d="M12 11v2a14 14 0 0 0 2.5 8"></path>
                                            <path d="M8 15a18 18 0 0 0 1.8 6"></path>
                                            <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlhadir }}
                                    </div>
                                    <div class="text-muted">
                                        Jumlah guru hadir
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-file-text" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                            </path>
                                            <path d="M9 9l1 0"></path>
                                            <path d="M9 13l6 0"></path>
                                            <path d="M9 17l6 0"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlizin }}
                                    </div>
                                    <div class="text-muted">
                                    Jumlah guru Izin
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-warning text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-mood-sick" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z"></path>
                                            <path d="M9 10h-.01"></path>
                                            <path d="M15 10h-.01"></path>
                                            <path d="M8 16l1 -1l1.5 1l1.5 -1l1.5 1l1.5 -1l1 1"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlsakit }}
                                    </div>
                                    <div class="text-muted">
                                    Jumlah guru Sakit
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Ranking Section -->
            @if(isset($isPeriodActive) && !$isPeriodActive)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-body text-center py-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle text-warning mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                <h3 class="text-warning mb-1">Perhitungan Skor & Leaderboard Non-Aktif</h3>
                                <p class="text-muted mb-3">{{ $periodMessage ?? 'Tanggal mulai perhitungan skor poin belum dimulai atau telah berakhir.' }}</p>
                                <a href="/konfigurasi/leaderboard" class="btn btn-warning btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>
                                    Atur Konfigurasi Periode Poin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Top Ranking Teachers -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">🏆 Papan Skor Guru Teladan (Top 10)</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Guru</th>
                                            <th>Kehadiran</th>
                                            <th>E-Jurnal</th>
                                            <th>Skor Efisiensi</th>
                                            <th class="w-1">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($topTeachers->count() > 0)
                                            @foreach($topTeachers as $rank => $t)
                                                @php $index = $loop->iteration; @endphp
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        @if($index == 1) 🥇 @elseif($index == 2) 🥈 @elseif($index == 3) 🥉 @else #{{ $index }} @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex py-1 align-items-center">
                                                            <span class="avatar me-2" style="background-image: url({{ $t['foto'] ? Storage::url('uploads/karyawan/' . $t['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }})"></span>
                                                            <div class="flex-fill">
                                                                <div class="font-weight-medium">{{ $t['nama_lengkap'] }}</div>
                                                                <div class="text-muted"><a href="#" class="text-reset">Username: {{ $t['nik'] }}</a></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">{{ round($t['stats']['presence_efficiency']) }}%</div>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-success" style="width: {{ $t['stats']['presence_efficiency'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">{{ round($t['stats']['journal_efficiency']) }}%</div>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-info" style="width: {{ $t['stats']['journal_efficiency'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    <td class="font-weight-bold text-primary">
                                                        {{ $t['score'] }} Pts
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $t['score'] >= 90 ? 'bg-success' : ($t['score'] >= 75 ? 'bg-primary' : 'bg-warning') }}">
                                                            {{ $t['score'] >= 90 ? 'Excellent' : ($t['score'] >= 75 ? 'Good' : 'Average') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted italic">
                                                    Belum ada data skor guru
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Ranking Piket (Ditinggalkan sementara sesuai permintaan)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">🏆 Papan Skor Petugas Piket Teladan (Top 10)</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Petugas Piket</th>
                                            <th>Kehadiran</th>
                                            <th>Monitoring WA</th>
                                            <th>Skor Efisiensi</th>
                                            <th class="w-1">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($topPiket->count() > 0)
                                            @foreach($topPiket as $rank => $t)
                                                @php $index = $loop->iteration; @endphp
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        @if($index == 1) 🥇 @elseif($index == 2) 🥈 @elseif($index == 3) 🥉 @else #{{ $index }} @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex py-1 align-items-center">
                                                            <span class="avatar me-2" style="background-image: url({{ $t['foto'] ? Storage::url('uploads/karyawan/' . $t['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }})"></span>
                                                            <div class="flex-fill">
                                                                <div class="font-weight-medium">{{ $t['nama_lengkap'] }}</div>
                                                                <div class="text-muted"><a href="#" class="text-reset">Username: {{ $t['nik'] }}</a></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">{{ round($t['stats']['attendance_efficiency']) }}%</div>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-success" style="width: {{ $t['stats']['attendance_efficiency'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">{{ round($t['stats']['monitoring_efficiency']) }}%</div>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-warning" style="width: {{ $t['stats']['monitoring_efficiency'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    <td class="font-weight-bold text-primary">
                                                        {{ $t['score'] }} Pts
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $t['score'] >= 90 ? 'bg-success' : ($t['score'] >= 75 ? 'bg-primary' : 'bg-warning') }}">
                                                            {{ $t['score'] >= 90 ? 'Excellent' : ($t['score'] >= 75 ? 'Good' : 'Average') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted italic">
                                                    Belum ada data skor petugas piket
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                --}}
            @endif
        </div>
    </div>
</div>
@endsection
