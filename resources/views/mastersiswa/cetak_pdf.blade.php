<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Master Siswa</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 11px;
        }
        .meta-table td {
            padding: 3px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .table-data th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 6px 8px;
            border: 1px solid #1e3a8a;
            text-align: center;
        }
        .table-data td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: monospace;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">LAPORAN DATA MASTER SISWA</h1>
                <div class="subtitle">Sistem Informasi Manajemen Sekolah - Miftahul Khaer</div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div class="subtitle">Dicetak pada: {{ date('d-m-Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 12%;"><strong>Filter Lembaga</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 36%;">{{ request('lembaga') ?: 'Semua Lembaga' }}</td>

            <td style="width: 12%;"><strong>Total Siswa</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 36%;">{{ $siswas->count() }} Siswa</td>
        </tr>
        <tr>
            <td><strong>Filter Lokasi</strong></td>
            <td>:</td>
            <td>{{ request('kode_cabang') ?: 'Semua Lokasi' }}</td>

            <td><strong>Filter Status</strong></td>
            <td>:</td>
            <td>{{ strtoupper(request('status') ?: 'Semua Status (Aktif & Non-Aktif)') }}</td>
        </tr>
        <tr>
            <td><strong>Filter Kelas</strong></td>
            <td>:</td>
            <td>{{ request('kode_kelas') ?: 'Semua Kelas' }}</td>

            <td><strong>Pencarian</strong></td>
            <td>:</td>
            <td>{{ request('search') ?: '-' }}</td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 90px;">NISN</th>
                <th>NAMA SISWA</th>
                <th style="width: 35px;">L/P</th>
                <th style="width: 90px;">KELAS</th>
                <th style="width: 70px;">LEMBAGA</th>
                <th style="width: 70px;">LOKASI</th>
                <th style="width: 60px;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $s)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center font-mono">{{ $s->nisn ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ strtoupper($s->nama_siswa) }}</td>
                    <td class="text-center">{{ $s->jenis_kelamin }}</td>
                    <td class="text-center">Kelas {{ $s->kelas->nama_kelas ?? $s->kode_kelas }}</td>
                    <td class="text-center">{{ $s->kelas->lembaga ?? '-' }}</td>
                    <td class="text-center">{{ $s->kelas->kode_cabang ?? '-' }}</td>
                    <td class="text-center" style="font-weight: bold; color: {{ $s->status == 'aktif' ? '#16a34a' : '#dc2626' }};">
                        {{ strtoupper($s->status ?? 'AKTIF') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #94a3b8;">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis dari Sistem Manajemen Sekolah Siguru Miftahul Khaer
    </div>
</body>
</html>
