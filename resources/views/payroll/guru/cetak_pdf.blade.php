<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Gaji Guru</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 10px;
        }
        
        .kop-laporan { 
            margin-bottom: 15px; 
            text-align: center; 
        }
        
        .kop-table { 
            width: 100%; 
            border-collapse: collapse;
        }
        
        .kop-table td { 
            border: none !important; 
            vertical-align: top;
        }
        
        .kop-logo { 
            width: 60px; 
            height: auto;
        }
        
        .kop-title { 
            margin: 0; 
            font-size: 16px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        
        .kop-subtitle { 
            margin: 3px 0; 
            font-size: 14px; 
            font-weight: 600; 
        }
        
        .kop-alamat { 
            font-size: 10px; 
            font-style: italic; 
            margin: 0; 
            color: #333; 
        }
        
        .kop-line { 
            border: none; 
            border-top: 2px solid #000; 
            margin: 8px 0; 
            width: 100%;
        }

        .info-periode {
            text-align: center;
            margin: 10px 0;
            font-size: 11px;
        }

        /* Tabel Gaji */
        .tabel-gaji { 
            width: 100%;
            border-collapse: collapse; 
            font-size: 13px;
            margin-top: 10px;
            table-layout: auto;
        }

        .tabel-gaji th, 
        .tabel-gaji td { 
            border: 1px solid #000; 
            padding: 5px 7px; 
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .tabel-gaji thead th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12px;
        }

        .tabel-gaji tfoot td {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .col-no { 
            width: 30px; 
            min-width: 30px;
        }
        .col-nama { 
            text-align: left !important;
            padding-left: 6px;
            min-width: 150px;
        }
        .col-paraf { 
            text-align: left !important;
            width: 50px;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }

        .tabel-gaji thead .col-nama {
            text-align: center !important;
        }
    </style>
</head>
<body>

    <!-- KOP LAPORAN -->
    <div class="kop-laporan">
        <table class="kop-table">
            <tr>
                <td style="width: 80px; text-align: center;">
                    <img src="{{ public_path('assets/img/website.png') }}" alt="Logo Sekolah" class="kop-logo">
                </td>
                <td style="text-align: center;">
                    <h2 class="kop-title">PONPES MIFTAHUL KHAER</h2>
                    <h4 class="kop-subtitle">Laporan Rekap Gaji Guru</h4>
                    <p class="kop-alamat">
                        Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug,<br>
                        Kabupaten Tangerang, Banten 15810
                    </p>
                </td>
            </tr>
        </table>
        <hr class="kop-line">
    </div>

    <!-- Info Bulan / Tahun -->
    <div class="info-periode">
        <strong>Bulan: {{ $bulan }} / Tahun: {{ $tahun }}</strong>
        @if($lokasi) | <strong>Lokasi: {{ $lokasi }}</strong> @endif
        @if($golongan) | <strong>Golongan: {{ $golongan }}</strong> @endif
    </div>

    <!-- Tabel Rekap -->
    <table class="tabel-gaji">
        <thead>
    <tr>
        <th rowspan="2" class="col-no">No</th>
        <th rowspan="2" class="col-nama">Nama</th> 
        <th rowspan="2">Tunjangan</th>
        <th colspan="3">Honorarium</th>
        <th colspan="3">Kehadiran</th>
        <th rowspan="2">Dansos</th>
        <!-- <th rowspan="2">Sim Kop</th> -->
        <th rowspan="2">Potongan</th>
        <th rowspan="2">Diterima</th>
        <th rowspan="2" class="col-paraf">Paraf</th>
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
            <td class="col-no">{{ $index + 1 }}.</td>
            <td class="col-nama">{{ $g->nama_lengkap }}</td> 
            <td class="text-center">{{ number_format($g->tunjangan_jabatan, 0, ',', '.') }}</td>

            <td>{{ $g->tjm }}</td>
            <td class="text-center">{{ number_format($g->gaji_pokok, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($g->jml_gaji_pokok, 0, ',', '.') }}</td>

            <td>{{ $g->hadir }}</td>
            <td class="text-center">{{ number_format($g->transport, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($g->jml_transport, 0, ',', '.') }}</td>

            <td class="text-center">{{ number_format($g->dana_sosial, 0, ',', '.') }}</td>
            <!-- <td class="text-center">{{ number_format($g->simpanan_koperasi, 0, ',', '.') }}</td> -->
            <td class="text-center">{{ number_format($g->potongan, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($g->total_gaji, 0, ',', '.') }}</td>

            <td class="col-paraf">{{ $index + 1 }}.</td>
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
        <td class="text-center">{{ number_format($rekap->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>

        <td class="text-center">{{ $rekap->sum('tjm') }}</td>
        <td></td>
        <td class="text-center">{{ number_format($rekap->sum('jml_gaji_pokok'), 0, ',', '.') }}</td>

        <td class="text-center">{{ $rekap->sum('hadir') }}</td>
        <td></td>
        <td class="text-center">{{ number_format($rekap->sum('jml_transport'), 0, ',', '.') }}</td>

        <td class="text-center">{{ number_format($rekap->sum('dana_sosial'), 0, ',', '.') }}</td>
        <!-- <td class="text-center">{{ number_format($rekap->sum('simpanan_koperasi'), 0, ',', '.') }}</td> -->
        <td class="text-center">{{ number_format($rekap->sum('potongan'), 0, ',', '.') }}</td>
        <td class="text-center" colspan="2">{{ number_format($rekap->sum('total_gaji'), 0, ',', '.') }}</td>
    </tr>
</tfoot>
    </table>

</body>
</html>