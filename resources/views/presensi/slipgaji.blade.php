@extends('layouts.presensi')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Slip Gaji Digital</div>
        <div class="right"></div>
    </div>

    <style>
        .slip-card {
            border: 1px solid #2c3e50;
            border-radius: 8px;
            background: white;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            margin-bottom: 20px;
        }

        .kop-laporan {
            text-align: center;
            border-bottom: 1px solid #3498db;
            padding: 10px;
            background: #fff;
        }

        .kop-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            line-height: 1.3;
        }

        .kop-subtitle {
            font-size: 12px;
            font-weight: bold;
            color: #e74c3c;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 10px;
            color: #7f8c8d;
            margin: 0;
        }

        .periode-box {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
        }

        .employee-info {
            background: #ecf0f1;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .info-item {
            display: flex;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-label {
            width: 80px;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-value {
            flex: 1;
            color: #34495e;
            font-weight: bold;
        }

        .section-header {
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .earnings-header { background-color: #27ae60; }
        .deductions-header { background-color: #e74c3c; }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .salary-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        .item-name {
            font-weight: 500;
            color: #333;
        }

        .amount {
            text-align: right;
            font-weight: bold;
            color: #2c3e50;
        }

        .total-row {
            background: #f9f9f9;
            font-weight: bold;
        }

        .net-pay-box {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .net-pay-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 5px;
        }

        .net-pay-amount {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }

        /* Excel Ribbon Toolbar Style */
        .excel-ribbon-toolbar {
            background-color: #f3f2f1;
            border: 1px solid #edebe9;
            border-radius: 8px;
            padding: 12px;
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

        @media print {
            .appHeader, .excel-ribbon-toolbar, .btn-print-group {
                display: none !important;
            }
            body { background: white; }
            .content-page { margin-top: 0 !important; padding: 0 !important; }
            .slip-card { border: none; box-shadow: none; }
        }
    </style>
@endsection

@section('content')
    <div class="content-page" style="margin-top:70px; padding: 15px;">
        <!-- Filter Section -->
        <div class="excel-ribbon-toolbar">
            <div class="excel-ribbon-title">
                <ion-icon name="funnel-outline"></ion-icon> Filter Slip Gaji
            </div>
            <form action="/presensi/slipgaji" method="GET">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            @php
                                $namabulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            @endphp
                            <select name="bulan" id="bulan" class="form-control excel-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option {{ $bulan == $i ? 'selected' : '' }} value="{{ $i }}">
                                        {{ $namabulanIndo[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <select name="tahun" id="tahun" class="form-control excel-select">
                                @php
                                    $tahun_awal = 2024;
                                    $tahun_sekarang = date('Y');
                                    for ($t = $tahun_awal; $t <= $tahun_sekarang; $t++) {
                                        $selected = ($tahun == $t) ? 'selected' : '';
                                        echo "<option $selected value='$t'>$t</option>";
                                    }
                                @endphp
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button type="submit" class="btn excel-btn-search w-100">
                            <ion-icon name="search-outline"></ion-icon> Cari Data
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @php
            function formatRupiah($v) {
                return 'Rp. ' . number_format($v, 0, ',', '.');
            }
        @endphp

        <!-- Slip Card -->
        @if($data)
            <div class="slip-card shadow">
                <div class="kop-laporan">
                    <div class="kop-title">PONDOK PESANTREN MIFTAHUL KHAER</div>
                    <div class="kop-subtitle">SLIP GAJI {{ strtoupper($type) }}</div>
                    <div class="kop-alamat">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug</div>
                </div>

                <div class="periode-box">
                    PERIODE: {{ strtoupper($namabulan[(int)$bulan]) }} {{ $tahun }}
                </div>

                <div class="employee-info">
                    <div class="info-item"><div class="info-label">Nama</div><div class="info-value">: {{ $data->nama_lengkap }}</div></div>
                    <div class="info-item"><div class="info-label">Unit</div><div class="info-value">: {{ $data->lokasi_sekolah ?? $data->lokasi }}</div></div>
                </div>

                <!-- PENDAPATAN -->
                <div class="section-header earnings-header">PENDAPATAN</div>
                <table class="salary-table">
                @if($type == 'guru')
                    <tr>
                        <td class="item-name">Gaji Pokok ({{ $data->jam_mengajar }} jam x {{ number_format($data->rate_gaji_pokok, 0, ',', '.') }})</td>
                        <td class="amount">{{ number_format($data->jml_gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Kehadiran ({{ $data->hadir }} hari x {{ number_format($data->rate_transport, 0, ',', '.') }})</td>
                        <td class="amount">{{ number_format($data->jml_transport, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Jabatan</td>
                        <td class="amount">{{ number_format($data->tunjangan_jabatan, 0, ',', '.') }}</td>
                    </tr>
                @elseif($type == 'pengabdian')
                    <tr>
                        <td class="item-name">Honorarium ({{ $data->honorarium_wkt }} x {{ number_format($data->honorarium_stn, 0, ',', '.') }})</td>
                        <td class="amount">{{ number_format($data->jml_honorarium, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Kehadiran ({{ $data->hadir }} hari x {{ number_format($data->kehadiran_stn, 0, ',', '.') }})</td>
                        <td class="amount">{{ number_format($data->jml_kehadiran, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Jabatan</td>
                        <td class="amount">{{ number_format($data->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Pengabdian</td>
                        <td class="amount">{{ number_format($data->tunjangan_pengabdian ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Tunjangan Lainnya</td>
                        <td class="amount">{{ number_format($data->tunjangan_lainnya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endif
                    <tr class="total-row">
                        <td class="item-name">TOTAL PENDAPATAN</td>
                        <td class="amount">{{ number_format($data->total_earnings, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <!-- POTONGAN -->
                <div class="section-header deductions-header">POTONGAN</div>
                <table class="salary-table">
                @if($type == 'guru')
                    <tr>
                        <td class="item-name">Dana Sosial (Dansos)</td>
                        <td class="amount">{{ number_format($data->dana_sosial, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Potongan Lainnya</td>
                        <td class="amount">{{ number_format($data->potongan, 0, ',', '.') }}</td>
                    </tr>
                @elseif($type == 'pengabdian')
                    <tr>
                        <td class="item-name">Dana Sosial (Dansos)</td>
                        <td class="amount">{{ number_format($data->potongan_dansos ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Potongan FPMK</td>
                        <td class="amount">{{ number_format($data->potongan_fpmk ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="item-name">Potongan Lainnya</td>
                        <td class="amount">{{ number_format($data->potongan_lainnya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endif
                    <tr class="total-row">
                        <td class="item-name">TOTAL POTONGAN</td>
                        <td class="amount text-danger">- {{ number_format($data->total_deductions, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <div class="net-pay-box">
                    <span class="net-pay-label">TOTAL GAJI DITERIMA</span>
                    <span class="net-pay-amount">{{ formatRupiah($data->total_gaji) }}</span>
                </div>
            </div>

            <div class="btn-print-group" style="margin-bottom: 80px;">
                @php
                    $downloadUrl = url("/presensi/downloadslipgaji?bulan={$bulan}&tahun={$tahun}");
                @endphp
                <a href="{{ $downloadUrl }}" class="btn btn-primary btn-block" target="_blank" download style="border-radius: 8px; font-weight: 700; margin-top: 0;">
                    <ion-icon name="download-outline" style="font-size: 18px; vertical-align: middle; margin-right: 5px;"></ion-icon> Unduh PDF
                </a>
            </div>
        @else
            <!-- Pesan Jika Data Belum Diinput Admin -->
            <div class="alert alert-warning shadow-sm" style="border-radius:10px; margin-top:20px; text-align:center; padding:30px 15px;">
                <ion-icon name="document-text-outline" style="font-size: 60px; color: #f39c12; margin-bottom: 10px;"></ion-icon>
                <h4 style="font-weight: 700; color: #d35400;">Slip Gaji Belum Tersedia</h4>
                <p style="font-size: 12px; color: #7f8c8d; margin-bottom:0;">
                    Data penggajian untuk periode ini belum diinput oleh Admin. Silakan cek secara berkala atau hubungi bagian tata usaha/admin.
                </p>
            </div>
        @endif
        <footer style="text-align: center; font-size: 11px; color: #999; margin-top: 20px; padding-bottom: 80px;">
            &copy; Copyright by Putriana Dyah Anjani
            <a href="https://instagram.com/ptriidyhhhh" target="_blank" style="color: #999; text-decoration: none; margin-left: 1px;">
                <ion-icon name="logo-instagram"></ion-icon>
            </a>
        </footer>
    </div>
@endsection
