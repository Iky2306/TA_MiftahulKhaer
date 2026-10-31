<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Gaji Pengabdian</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        /* ===== HEADER ===== */
        .kop-laporan {
            position: relative;
            text-align: center;
            margin-bottom: 5px;
        }
        .kop-laporan img {
            position: absolute;
            left: 10px;
            top: -10px; 
            width: 65px;
            height: 65px;
        }
        .kop-laporan h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .kop-laporan h4 {
            margin: 2px 0;
            font-size: 13px;
        }
        .kop-laporan p {
            font-size: 10px;
            margin: 0;
            font-style: italic;
        }
        hr {
            border: none;
            border-top: 2px solid black;
            margin: 5px 0 10px 0;
        }
        .info-periode {
            text-align: center;
            font-size: 11px;
            margin-bottom: 8px;
        }

        /* ===== TABEL REKAP (table-layout: auto;) ===== */
        .tabel-gaji {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto; /* LEBAR KOLOM MENYESUAIKAN ISI */
            font-size: 10.5px;
        }
        .tabel-gaji th,
        .tabel-gaji td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
            white-space: nowrap; 
            text-align: center; /* Default header center */
        }
        .tabel-gaji thead th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .tabel-gaji tfoot td {
            background: #f0f0f0;
            font-weight: bold;
        }
        
        /* Aturan untuk kolom Paraf (Rata Kiri) */
        .tabel-gaji th:last-child, 
        .tabel-gaji td:last-child { 
            text-align: left !important; /* Paraf selalu rata kiri */
        }

    </style>
</head>
<body>

    <div class="kop-laporan">
        <img src="{{ public_path('assets/img/website.png') }}" alt="Logo">
        <h2>PONPES MIFTAHUL KHAER</h2>
        <h4>LAPORAN REKAP GAJI PENGABDIAN</h4>
        <p>Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug, Kabupaten Tangerang, Banten 15810</p>
        <hr>
    </div>

    <div class="info-periode">
        <strong>Bulan: {{ $bulan }} / Tahun: {{ $tahun }}</strong>
        @if(isset($lokasi)) | <strong>{{ $lokasi }}</strong> @endif
    </div>

    <table class="tabel-gaji">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama</th>
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
                    <td>{{ $index + 1 }}</td>
                    
                    <td style="text-align: left;">{{ $p->nama_lengkap }}</td> 
                    
                    <td style="text-align: center;">{{ number_format($p->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td> 
                    <td style="text-align: center;">{{ number_format($p->tunjangan_pengabdian ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($p->tunjangan_lainnya ?? 0, 0, ',', '.') }}</td>
                    
                    <td style="text-align: center;">{{ $p->honorarium_wkt ?? 0 }}</td>
                    <td style="text-align: center;">{{ number_format($p->honorarium_stn ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($p->jml_honorarium ?? 0, 0, ',', '.') }}</td>
                    
                    <td style="text-align: center;">{{ $p->kehadiran_wkt ?? 0 }}</td>
                    <td style="text-align: center;">{{ number_format($p->kehadiran_stn ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($p->jml_kehadiran ?? 0, 0, ',', '.') }}</td>
                    
                    <td style="text-align: center;">{{ number_format($p->potongan_dansos ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($p->potongan_fpmk ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($p->potongan_lainnya ?? 0, 0, ',', '.') }}</td>
                    
                    <td style="text-align: center;">{{ number_format($p->gaji_bersih ?? 0, 0, ',', '.') }}</td>
                    
                    <td>{{ $index + 1 }}.</td> 
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: center;"><strong>TOTAL</strong></td>
                
                <td style="text-align: center;">{{ number_format($rekap->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ number_format($rekap->sum('tunjangan_pengabdian'), 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ number_format($rekap->sum('tunjangan_lainnya'), 0, ',', '.') }}</td>
                
                <td style="text-align: center;">{{ $rekap->sum('honorarium_wkt') }}</td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;">{{ number_format($rekap->sum('jml_honorarium'), 0, ',', '.') }}</td>
                
                <td style="text-align: center;">{{ $rekap->sum('kehadiran_wkt') }}</td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;">{{ number_format($rekap->sum('jml_kehadiran'), 0, ',', '.') }}</td>
                
                <td style="text-align: center;">{{ number_format($rekap->sum('potongan_dansos'), 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ number_format($rekap->sum('potongan_fpmk'), 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ number_format($rekap->sum('potongan_lainnya'), 0, ',', '.') }}</td>
                
                <td style="text-align: center;" colspan="2">{{ number_format($rekap->sum('gaji_bersih'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>