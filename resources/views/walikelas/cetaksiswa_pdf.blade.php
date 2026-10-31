<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa Kelas {{ $kelasAktif->nama_kelas }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 10px;
        }

        .kop-laporan {
            margin-bottom: 10px;
            text-align: center;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            border: none !important;
            vertical-align: middle;
        }

        .kop-logo {
            width: 65px;
            height: auto;
        }

        .kop-title {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000000;
        }

        .kop-subtitle {
            margin: 2px 0;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 9px;
            font-style: italic;
            margin: 0;
            color: #000000;
            line-height: 1.2;
        }

        .kop-line {
            border: none;
            border-top: 2px solid #000000;
            margin: 6px 0 12px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 11px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            color: #000000;
        }

        .info-table td.label {
            width: 110px;
            font-weight: bold;
        }

        .info-table td.colon {
            width: 12px;
        }

        .info-table td.value {
            font-weight: bold;
        }

        /* Tabel Hitam Putih Formal Seperti Excel */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .data-table th {
            background-color: #f2f2f2;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 8px;
            border: 1px solid #000000;
            text-transform: uppercase;
            text-align: center;
        }

        .data-table td {
            padding: 5px 8px;
            border: 1px solid #000000;
            font-size: 11px;
            color: #000000;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: monospace;
        }

        .signature-container {
            margin-top: 25px;
            width: 100%;
        }

        .signature-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 11px;
            color: #000000;
        }

        .signature-space {
            height: 55px;
        }
    </style>
</head>
<body>

    {{-- Kop Sekolah Hitam Putih Resmi --}}
    <div class="kop-laporan">
        <table class="kop-table">
            <tr>
                <td style="width: 75px; text-align: center;">
                    @php
                        $path = public_path('assets/img/website.png');
                        $logoData = file_exists($path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($path)) : '';
                    @endphp
                    @if($logoData)
                        <img src="{{ $logoData }}" alt="Logo" class="kop-logo">
                    @endif
                </td>
                <td style="text-align: center;">
                    <h2 class="kop-title">PONDOK PESANTREN MIFTAHUL KHAER</h2>
                    <h4 class="kop-subtitle">DAFTAR DATA SISWA KELAS</h4>
                    <p class="kop-alamat">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug,<br>Kabupaten Tangerang, Banten 15810</p>
                </td>
                <td style="width: 75px;"></td>
            </tr>
        </table>
        <hr class="kop-line">
    </div>

    {{-- Info Kelas --}}
    <table class="info-table">
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td class="value">{{ $kelasAktif->nama_kelas }}</td>

            <td class="label">Tahun Ajaran</td>
            <td class="colon">:</td>
            <td class="value">{{ date('Y') }}/{{ date('Y') + 1 }}</td>
        </tr>
        <tr>
            <td class="label">Lembaga</td>
            <td class="colon">:</td>
            <td class="value">{{ $kelasAktif->lembaga }} Miftahul Khaer</td>

            <td class="label">Lokasi Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ $namaCabang }}</td>
        </tr>
            <td class="label">Total Siswa</td>
            <td class="colon">:</td>
            <td class="value">{{ $siswas->count() }} Siswa</td>
        </tr>
        <tr>
            <td class="label">Wali Kelas</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($kelasAktif->waliKelas->nama_lengkap ?? Auth::guard('karyawan')->user()->nama_lengkap) }}</td>

            <td class="label"></td>
            <td class="colon"></td>
            <td class="value"></td>
        </tr>
    </table>

    {{-- Tabel Data Siswa Hitam Putih Formal --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">NO</th>
                <th style="width: 140px;">NISN</th>
                <th style="text-align: left;">NAMA SISWA</th>
                <th style="width: 60px;">L/P</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $s)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center font-mono">{{ $s->nisn ?? '-' }}</td>
                    <td>{{ strtoupper($s->nama_siswa) }}</td>
                    <td class="text-center">{{ $s->jenis_kelamin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 15px;">
                        Data siswa belum tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <div class="signature-container">
        <div class="signature-box">
            <div>Tangerang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
            <div>Wali Kelas {{ $kelasAktif->nama_kelas }},</div>
            <div class="signature-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">
                {{ strtoupper($kelasAktif->waliKelas->nama_lengkap ?? Auth::guard('karyawan')->user()->nama_lengkap) }}
            </div>
        </div>
    </div>

</body>
</html>
