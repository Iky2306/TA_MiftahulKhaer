<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Gaji Pengabdian</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 10px;
        }
        
        table { border-collapse: collapse; margin-top: 10px; }

        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }

        .kop-table { width: 100%; }
        .kop-table td { border: none !important; }

        /* ===== Aturan tabel gaji ===== */
        #tabel-gaji {
            border-collapse: collapse;
            margin-left: auto;
            margin-right: auto;
            table-layout: fixed;
            width: auto;
        }   

        #tabel-gaji th, 
        #tabel-gaji td {
            white-space: nowrap;
        }

        /* Lebar kolom */
        #tabel-gaji .kolom-nama { 
            white-space: normal; 
            text-align: left; 
            min-width: 180px; 
        }
        #tabel-gaji .col-qty { width: 35px; }
        #tabel-gaji .col-stn { width: 70px; }
        #tabel-gaji .col-jml { width: 90px; }
        #tabel-gaji .col-kecil { width: 70px; }
        #tabel-gaji .col-paraf { width: 60px; }

        thead { background: #f2f2f2; }
        tfoot { background: #f2f2f2; }
        tfoot td, tfoot th { font-weight: bold; }

        h2, p { text-align: center; margin: 0; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .kop-logo { width: 70px; }

        @media print {
            @page { size: landscape; margin: 10mm; }
            .no-print { display: none; }
        }

        .kop-laporan { margin-bottom: 10px; text-align: center; }
        .kop-logo { width: 80px; }
        .kop-title { margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .kop-subtitle { margin: 3px 0; font-size: 16px; font-weight: 600; }
        .kop-alamat { font-size: 12px; font-style: italic; margin: 0; color: #333; }
        .kop-line { border: none; border-top: 2px solid #000; margin: 5px 0 0; }
        
        table th.paraf-col, 
        table td.paraf-col {
            text-align: left !important;
        }
        
        /* Kolom Nama tetap rata tengah di header */
        table th.kolom-nama{
            text-align: center !important;
        }
    </style>
</head>
<body>

    <div class="kop-laporan">
        <table class="kop-table">
            <tr>
                <td width="100" class="text-center align-top">
                    <img src="{{ asset('assets/img/website.png') }}" alt="Logo Sekolah" class="kop-logo">
                </td>
                <td class="text-center">
                    <h2 class="kop-title">PONPES MIFTAHUL KHAER</h2>
                    <h4 class="kop-subtitle">Laporan Rekap Gaji Pengabdian</h4>
                    <p class="kop-alamat">
                        Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug, <br>
                        Kabupaten Tangerang, Banten 15810
                    </p>
                </td>
            </tr>
        </table>
        <hr class="kop-line">
    </div>

    <p class="text-center">
        Bulan: {{ $bulan }} / Tahun: {{ $tahun }} 
        @if($lokasi) | Lokasi: <strong>{{ $lokasi }}</strong> @endif
    </p>

    <table id="tabel-gaji">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" class="kolom-nama">Nama</th>
                <th colspan="3">Tunjangan</th>
                <th colspan="3">Honorarium</th>
                <th colspan="3">Kehadiran</th>
                <th colspan="3">Potongan</th>
                <th rowspan="2" class="col-kecil">Diterima</th>
                <th rowspan="2" class="col-paraf">Paraf</th>
            </tr>
            <tr>
                <th class="col-kecil">Jabatan</th>
                <th class="col-kecil">Pengabdian</th>
                <th class="col-kecil">Lainnya</th>
                <th class="col-qty">Wkt</th>
                <th class="col-stn">Stn</th>
                <th class="col-jml">Jml</th>
                <th class="col-qty">Wkt</th>
                <th class="col-stn">Stn</th>
                <th class="col-jml">Jml</th>
                <th class="col-kecil">Dansos</th>
                <th class="col-kecil">FPMK</th>
                <th class="col-kecil">Lainnya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="kolom-nama">{{ $p->nama_lengkap }}</td>

                    {{-- Tunjangan --}}
                    <td class="text-center">Rp. {{ number_format($p->tunjangan_jabatan ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->tunjangan_pengabdian ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->tunjangan_lainnya ?? 0, 0, ',', '.') }}</td>

                    {{-- Honorarium --}}
                    <td>{{ $p->honorarium_wkt ?? 0 }}</td>
                    <td class="text-center">Rp. {{ number_format($p->honorarium_stn ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->jml_honorarium ?? 0, 0, ',', '.') }}</td>

                    {{-- Kehadiran --}}
                    <td>{{ $p->kehadiran_wkt ?? 0 }}</td>
                    <td class="text-center">Rp. {{ number_format($p->kehadiran_stn ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->jml_kehadiran ?? 0, 0, ',', '.') }}</td>

                    {{-- Potongan --}}
                    <td class="text-center">Rp. {{ number_format($p->potongan_dansos ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->potongan_fpmk ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">Rp. {{ number_format($p->potongan_lainnya ?? 0, 0, ',', '.') }}</td>

                    {{-- Diterima --}}
                    <td class="text-center">Rp. {{ number_format($p->gaji_bersih ?? 0, 0, ',', '.') }}</td>

                    {{-- Paraf --}}
                    <td class="paraf-col">{{ $index + 1 }}.</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>

                {{-- Total Tunjangan --}}
                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_pengabdian'), 0, ',', '.') }}</td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('tunjangan_lainnya'), 0, ',', '.') }}</td>

                {{-- Total Honorarium --}}
                <td>{{ $rekap->sum('honorarium_wkt') }}</td>
                <td></td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_honorarium'), 0, ',', '.') }}</td>

                {{-- Total Kehadiran --}}
                <td>{{ $rekap->sum('kehadiran_wkt') }}</td>
                <td></td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('jml_kehadiran'), 0, ',', '.') }}</td>

                {{-- Total Potongan --}}
                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_dansos'), 0, ',', '.') }}</td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_fpmk'), 0, ',', '.') }}</td>
                <td class="text-center">Rp. {{ number_format($rekap->sum('potongan_lainnya'), 0, ',', '.') }}</td>

                {{-- Total Diterima --}}
                <td class="text-center" colspan="2">Rp. {{ number_format($rekap->sum('gaji_bersih'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>