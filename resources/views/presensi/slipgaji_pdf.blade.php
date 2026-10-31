<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $data->nama_lengkap }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        .kop-laporan {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .kop-title {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
        }
        .kop-subtitle {
            font-size: 12px;
            color: #e74c3c;
            margin: 2px 0;
            font-weight: bold;
        }
        .kop-alamat {
            font-size: 9px;
            color: #777;
            margin: 0;
        }
        .periode-box {
            background-color: #3498db;
            color: white;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 1px 0;
        }
        .label {
            font-weight: bold;
            width: 60px;
        }
        .section-header {
            background-color: #f2f2f2;
            padding: 3px 10px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-top: 10px;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .salary-table td {
            padding: 4px 10px;
            border-bottom: 1px solid #eee;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .net-pay-box {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: 15px;
        }
        .net-pay-label {
            font-size: 9px;
            text-transform: uppercase;
        }
        .net-pay-amount {
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="kop-laporan">
        <div class="kop-title">PONDOK PESANTREN MIFTAHUL KHAER</div>
        <div class="kop-subtitle">SLIP GAJI {{ strtoupper($type) }}</div>
        <div class="kop-alamat">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug</div>
    </div>

    <div class="periode-box">
        PERIODE: {{ strtoupper($namabulan[(int)$bulan]) }} {{ $tahun }}
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td>: {{ $data->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Unit</td>
            <td>: {{ $data->lokasi_sekolah ?? $data->lokasi }}</td>
        </tr>
    </table>

    <div class="section-header">PENDAPATAN</div>
    <table class="salary-table">
        @if($type == 'guru')
            <tr>
                <td>Gaji Pokok ({{ $data->jam_mengajar }} jam x {{ number_format($data->rate_gaji_pokok, 0, ',', '.') }})</td>
                <td class="amount">Rp. {{ number_format($data->jml_gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kehadiran ({{ $data->hadir }} hari x {{ number_format($data->rate_transport, 0, ',', '.') }})</td>
                <td class="amount">Rp. {{ number_format($data->jml_transport, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Jabatan</td>
                <td class="amount">Rp. {{ number_format($data->tunjangan_jabatan, 0, ',', '.') }}</td>
            </tr>
        @elseif($type == 'pengabdian')
            <tr>
                <td>Honorarium ({{ $data->honorarium_wkt }} x {{ number_format($data->honorarium_stn, 0, ',', '.') }})</td>
                <td class="amount">Rp. {{ number_format($data->jml_honorarium, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kehadiran ({{ $data->hadir }} hari x {{ number_format($data->kehadiran_stn, 0, ',', '.') }})</td>
                <td class="amount">Rp. {{ number_format($data->jml_kehadiran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Jabatan</td>
                <td class="amount">Rp. {{ number_format($data->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Pengabdian</td>
                <td class="amount">Rp. {{ number_format($data->tunjangan_pengabdian ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Lainnya</td>
                <td class="amount">Rp. {{ number_format($data->tunjangan_lainnya ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL PENDAPATAN</td>
            <td class="amount">Rp. {{ number_format($data->total_earnings, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="section-header">POTONGAN</div>
    <table class="salary-table">
        @if($type == 'guru')
            <tr>
                <td>Dana Sosial</td>
                <td class="amount">Rp. {{ number_format($data->dana_sosial, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Lain-lain</td>
                <td class="amount">Rp. {{ number_format($data->potongan, 0, ',', '.') }}</td>
            </tr>
        @elseif($type == 'pengabdian')
            <tr>
                <td>Dana Sosial</td>
                <td class="amount">Rp. {{ number_format($data->potongan_dansos ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan FPMK</td>
                <td class="amount">Rp. {{ number_format($data->potongan_fpmk ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Lainnya</td>
                <td class="amount">Rp. {{ number_format($data->potongan_lainnya ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL POTONGAN</td>
            <td class="amount">- Rp. {{ number_format($data->total_deductions, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="net-pay-box">
        <div class="net-pay-label">Total Gaji Bersih Diterima</div>
        <div class="net-pay-amount">Rp. {{ number_format($data->total_gaji, 0, ',', '.') }}</div>
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
