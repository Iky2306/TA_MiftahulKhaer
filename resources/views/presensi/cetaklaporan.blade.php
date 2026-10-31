<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Individu</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .header .logo {
            width: 80px;
            height: 80px;
        }

        .header .title {
            text-align: left;
            padding-left: 10px;
        }

        .header .title h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header .title p {
            margin: 5px 0 0;
            font-size: 10px;
            font-style: italic;
            line-height: 1.4;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }


        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        .data-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }


        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 80px;">
                    <img src="{{ asset('assets/img/website.png') }}" class="logo" alt="Logo">
                </td>
                <td class="title">
                    <h1>LAPORAN ABSENSI GURU</h1>
                    <h1>PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}</h1>
                    <h1>PONPES MIFTAHUL KHAER</h1>
                    <p>Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug, Suka Bakti, Kec. Curug, Kabupaten Tangerang, Banten 15810</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <table>
                    <tr>
                        <td>Nama Pegawai</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td>Mapel</td>
                        <td>:</td>
                        <td>{{ $karyawan->jabatan }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_dept }}</td>
                    </tr>
                    <tr>
                        <td>No. HP</td>
                        <td>:</td>
                        <td>{{ $karyawan->no_hp }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th style="width: 40px;">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presensi as $d)
                @if ($d->status == 'h')
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                        <td>{{ $d->jam_in }}</td>
                        <td>{{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</td>
                        <td>{{ strtoupper($d->status) }}</td>
                        <td class="text-left">Hadir</td>
                    </tr>
                @else
                    @php
                        $bg = '';
                        if ($d->status == 'libur') {
                            $bg = 'style="background-color: #d4edda;"';
                        }
                    @endphp
                    <tr {!! $bg !!}>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{{ strtoupper($d->status) }}</td>
                        <td class="text-left">{{ $d->keterangan }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Tangerang, {{ date('d-m-Y') }}</p>
        <br><br><br>
        <p>.........................................</p>
    </div>
</body>

</html>
