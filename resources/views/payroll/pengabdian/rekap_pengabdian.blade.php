@extends('layouts.admin.tabler')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="row g-2">
                <div class="col-12">
                    <h4 class="mb-3">Rekap Gaji Pengabdian</h4>
                </div>
                
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
                            <div class="col-6 col-md-4">
                                <select name="bulan" class="form-select form-select-sm" required>
                                    @foreach(range(1, 12) as $b)
                                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                                            {{ $bulanIndo[$b] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
<div class="col-6 col-md-3">
    <select name="tahun" class="form-select form-select-sm" required>
        @for($t = date('Y') - 1; $t <= date('Y') + 1; $t++)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                {{ $t }}
            </option>
        @endfor
    </select>
</div>
                            @unless(Auth::guard('user')->user()->hasRole('staff'))
                            <div class="col-8 col-md-3">
                                <select name="lokasi" class="form-select form-select-sm">
                                    <option value="">Semua Lokasi</option>
                                    <option value="Pusat" {{ request('lokasi') == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                                    <option value="Cabang" {{ request('lokasi') == 'Cabang' ? 'selected' : '' }}>Cabang</option>
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
                            @endunless
                            <div class="col-4 col-md-2">
                                <div class="d-flex flex-wrap gap-1 justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill w-100 w-md-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                            <path d="M21 21l-6 -6" />
                                        </svg>
                                        <span>Tampilkan</span>
                                    </button>
                                    @if(request()->hasAny(['bulan','tahun','lokasi']))
                                        <a href="{{ route('payroll.pengabdian.rekap') }}" 
                                           class="btn btn-secondary btn-sm flex-fill w-100 w-md-auto d-flex align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                            </svg>
                                            <span class="d-none d-sm-inline">Reset</span>
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
                            <a href="{{ route('payroll.pengabdian.cetak', request()->all()) }}" 
                               target="_blank" 
                               class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-print me-1"></i>
                                <span class="d-none d-sm-inline">Cetak Rekap</span>
                                <span class="d-sm-none">Cetak</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.pengabdian.slipgaji', request()->all()) }}" 
                               target="_blank" 
                               class="btn btn-info btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-file-text me-1"></i>
                                <span class="d-none d-sm-inline">Cetak Slip</span>
                                <span class="d-sm-none">Slip</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.pengabdian.pdf', request()->all()) }}" 
                               class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fa fa-download me-1"></i>
                                <span class="d-none d-sm-inline">Download PDF</span>
                                <span class="d-sm-none">PDF</span>
                            </a>
                        </div>

                        <div class="col-6 col-md-3">
                            <a href="{{ route('payroll.pengabdian.excel', request()->all()) }}" 
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
                <div class="alert alert-warning text-center py-2 my-2">Tidak ada data gaji pengabdian untuk bulan ini.</div>
            @else
                <div class="table-responsive" style="font-size: 0.85rem;">
                    <table class="table table-bordered table-sm align-middle mb-1">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Lokasi</th>
                                <th colspan="3">Tunjangan</th>
                                <th colspan="3">Honorarium</th>
                                <th colspan="3">Kehadiran</th>
                                <th colspan="3">Potongan</th>
                                <th rowspan="2">Diterima</th>
                                <th rowspan="2">Paraf</th>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <th>Pengabdian</th>
                                <th>Lainnya</th>
                                <th>Wkt</th>
                                <th>Stn</th>
                                <th>Jml</th>
                                <th>Wkt</th>
                                <th>Stn</th>
                                <th>Jml</th>
                                <th>Dansos</th>
                                <th>FPMK</th>
                                <th>Lainnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $index => $p)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $p->nama_lengkap }}</td>
                                    <td class="text-center">{{ $p->lokasi }}</td>

                                    {{-- Tunjangan --}}
                                    <td class="text-center">Rp. {{ number_format($p->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->tunjangan_pengabdian ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->tunjangan_lainnya ?? 0, 0, ',', '.') }}</td>

                                    {{-- Honorarium --}}
                                    <td class="text-center">{{ $p->honorarium_wkt ?? 0 }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->honorarium_stn ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->jml_honorarium ?? 0, 0, ',', '.') }}</td>

                                    {{-- Kehadiran --}}
                                    <td class="text-center">{{ $p->kehadiran_wkt ?? 0 }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->kehadiran_stn ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->jml_kehadiran ?? 0, 0, ',', '.') }}</td>

                                    {{-- Potongan --}}
                                    <td class="text-center">Rp. {{ number_format($p->potongan_dansos ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->potongan_fpmk ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp. {{ number_format($p->potongan_lainnya ?? 0, 0, ',', '.') }}</td>

                                    {{-- Diterima --}}
                                    <td class="text-center fw-bold">Rp. {{ number_format($p->gaji_bersih ?? 0, 0, ',', '.') }}</td>
                                    
                                    {{-- Paraf --}}
                                    <td>{{ $index + 1 }}.</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-center">Total</td>

                                {{-- Total Tunjangan --}}
                                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_pengabdian'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_lainnya'), 0, ',', '.') }}</td>

                                {{-- Total Honorarium --}}
                                <td class="text-center">{{ $rekap->sum('honorarium_wkt') }}</td>
                                <td></td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_honorarium'), 0, ',', '.') }}</td>

                                {{-- Total Kehadiran --}}
                                <td class="text-center">{{ $rekap->sum('kehadiran_wkt') }}</td>
                                <td></td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_kehadiran'), 0, ',', '.') }}</td>

                                {{-- Total Potongan --}}
                                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_dansos'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_fpmk'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_lainnya'), 0, ',', '.') }}</td>

                                {{-- Total Diterima --}}
                                <td class="text-center">Rp. {{ number_format($rekap->sum('gaji_bersih'), 0, ',', '.') }}</td>
                                
                                {{-- Paraf --}}
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
