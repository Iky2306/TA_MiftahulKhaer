@extends('layouts.admin.tabler')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title m-0">Rekap Gaji Guru</div>
        </div>

        <div class="card-body">
            <div class="row g-2">
                {{-- Filter Form --}}
                <div class="col-12">
                    <form method="GET">
                        @php
                            $bulanIndo = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <select name="bulan" class="form-select form-select-sm" required>
                                    @foreach(range(1, 12) as $b)
                                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                                            {{ $bulanIndo[$b] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
    <select name="tahun" class="form-select form-select-sm" required>
        @for($t = date('Y') - 1; $t <= date('Y') + 1; $t++)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                {{ $t }}
            </option>
        @endfor
    </select>
</div>
                            @unless(Auth::guard('user')->user()->hasRole('staff'))
                            <div class="col-6 col-md-3">
                                <select name="lokasi" class="form-select form-select-sm">
                                    <option value="">Semua Lokasi</option>
                                    <option value="Pusat" {{ request('lokasi') == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                                    <option value="Cabang" {{ request('lokasi') == 'Cabang' ? 'selected' : '' }}>Cabang</option>
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
                            @endunless
                            <div class="col-6 col-md-2">
                                <select name="golongan" class="form-select form-select-sm">
                                    <option value="">Semua Gol.</option>
                                    <option value="1" {{ request('golongan') == '1' ? 'selected' : '' }}>Golongan 1</option>
                                    <option value="2" {{ request('golongan') == '2' ? 'selected' : '' }}>Golongan 2</option>
                                    <option value="3" {{ request('golongan') == '3' ? 'selected' : '' }}>Golongan 3</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                            <path d="M21 21l-6 -6" />
                                        </svg>
                                        <span>Tampilkan</span>
                                    </button>
                                    @if(request()->hasAny(['bulan','tahun','lokasi','golongan']))
                                        <a href="{{ route('payroll.guru.rekap') }}" class="btn btn-secondary btn-sm px-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Action Buttons --}}
                <div class="col-12">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.guru.cetak', request()->all()) }}" 
                               target="_blank" 
                               class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-print me-1"></i>
                                <span class="d-none d-sm-inline">Cetak Rekap</span>
                                <span class="d-sm-none">Cetak</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.guru.cetak.slip', request()->all()) }}" 
                               target="_blank" 
                               class="btn btn-info btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-file-text me-1"></i>
                                <span class="d-none d-sm-inline">Cetak Slip</span>
                                <span class="d-sm-none">Slip</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.guru.download.pdf', request()->all()) }}" 
                               class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-download me-1"></i>
                                <span class="d-none d-sm-inline">Download PDF</span>
                                <span class="d-sm-none">PDF</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.guru.download.excel', request()->all()) }}" 
                               class="btn btn-warning btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-file-excel-o me-1"></i>
                                <span class="d-none d-sm-inline">Download Excel</span>
                                <span class="d-sm-none">Excel</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-2">
            @if($rekap->isEmpty())
                <div class="alert alert-warning text-center py-2 my-2">Tidak ada data gaji guru untuk bulan ini.</div>
            @else
                <div class="table-responsive" style="font-size: 0.85rem;">
                    <table class="table table-bordered table-sm align-middle mb-1">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Lokasi</th>
                                <th rowspan="2">Jabatan</th>
                                <th rowspan="2">Tunjangan</th>
                                <th colspan="3">Honorarium</th>
                                <th colspan="3">Kehadiran</th>
                                <th rowspan="2">Dansos</th>
                                <!-- <th rowspan="2">Sim Kop</th> -->
                                <th rowspan="2">Potongan</th>
                                <th rowspan="2">Diterima</th>
                            </tr>
                            <tr>
                                <th>Wkt</th>
                                <th>Stn</th>
                                <th>Jml</th>
                                <th>Wkt</th>
                                <th>Stn</th>
                                <th>Jml</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $index => $g)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $g->nama_lengkap }}</td>
                                    <td class="text-center">{{ $g->lokasi_sekolah }}</td>
                                    <td class="text-center">{{ $g->jabatan }}</td>
                                    <td class="text-center">Rp. {{ number_format($g->tunjangan_jabatan, 0, ',', '.') }}</td>

                                    {{-- Honorarium --}}
                                    <td class="text-center">{{ $g->tjm }}</td>
                                    <td class="text-center">Rp. {{ number_format($g->gaji_pokok, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($g->jml_gaji_pokok, 0, ',', '.') }}</td>

                                    {{-- Kehadiran --}}
                                    <td class="text-center">{{ $g->hadir }}</td>
                                    <td class="text-center">Rp. {{ number_format($g->transport, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($g->jml_transport, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp. {{ number_format($g->dana_sosial, 0, ',', '.') }}</td>
                                    <!-- <td class="text-center">Rp. {{ number_format($g->simpanan_koperasi, 0, ',', '.') }}</td> -->
                                    <td class="text-center">Rp. {{ number_format($g->potongan, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold">Rp. {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-center">Total</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>

                                {{-- Honorarium --}}
                                <td class="text-center">{{ $rekap->sum('tjm') }}</td>
                                <td></td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_gaji_pokok'), 0, ',', '.') }}</td>

                                {{-- Kehadiran --}}
                                <td class="text-center">{{ $rekap->sum('hadir') }}</td>
                                <td></td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_transport'), 0, ',', '.') }}</td>

                                {{-- Potongan & total --}}
                                <td class="text-center">Rp. {{ number_format($rekap->sum('dana_sosial'), 0, ',', '.') }}</td>
                                <!-- <td class="text-center">Rp. {{ number_format($rekap->sum('simpanan_koperasi'), 0, ',', '.') }}</td> -->
                                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('total_gaji'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection