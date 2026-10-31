<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Guru</title>
    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <!-- Set page size here: A5, A4 or A3 -->
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body.A4 .sheet {
            width: 210mm;
            min-height: auto !important; /* Hapus batasan tinggi */
            height: auto !important;
            padding: 10mm;
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .tabeldatakaryawan tr td {
            padding: 5px;
            font-size: 12px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .tabelpresensi thead {
            display: table-header-group; /* Repeat header di setiap halaman */
        }

        .tabelpresensi tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .tabelpresensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
            font-size: 11px;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 11px;
        }

        .foto {
            width: 40px;
            height: 30px;
            object-fit: cover;
        }

        .status-hadir { 
            color: green; 
            font-weight: bold;
        }
        .status-izin { 
            color: orange; 
            font-weight: bold;
        }
        .status-sakit { 
            color: blue; 
            font-weight: bold;
        }
        .status-cuti { 
            color: purple; 
            font-weight: bold;
        }
        .status-alpha { 
            color: red; 
            font-weight: bold;
        }

        /* Styling untuk header tabel */
        .header-section {
            border-bottom: 2px solid black;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        /* Print styling */
        @media print {
            body.A4 .sheet {
                width: 210mm;
                min-height: auto !important;
                height: auto !important;
                page-break-after: auto;
                margin: 0;
                padding: 10mm;
            }

            .tabelpresensi {
                page-break-inside: auto;
            }

            .tabelpresensi thead {
                display: table-header-group;
            }

            .tabelpresensi tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Prevent orphan rows */
            .tabelpresensi tbody tr {
                page-break-inside: avoid;
            }
        }

        /* Button print */
        .no-print {
            margin: 20px 0;
            text-align: center;
        }

        .no-print button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .no-print button:hover {
            background-color: #45a049;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="A4">
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak Laporan</button>
        <button onclick="window.close()" style="background-color: #f44336;">❌ Tutup</button>
    </div>

    <section class="sheet">
        <table class="header-section" style="width: 100%;">
            <tr>
                <td style="width: 100px; vertical-align: top;">
                    <img src="{{ asset('assets/img/website.png') }}" width="80" height="80" alt="">
                </td>
                <td style="vertical-align: top;">
                    <span id="title">
                        LAPORAN ABSENSI GURU<br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        PONPES MIFTAHUL KHAER<br>
                    </span>
                    <span style="font-size: 11px;"><i>Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug, Suka Bakti, Kec. Curug, Kabupaten Tangerang, Banten 15810</i></span>
                </td>
            </tr>
        </table>

        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="6" style="vertical-align: top; padding-right: 20px;">
                    @php
                        $path = Storage::url('uploads/karyawan/' . $karyawan->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" width="120px" height="150" style="border: 1px solid #ddd;">
                </td>
            </tr>
            <tr>
                <td style="width: 120px;"><strong>Username</strong></td>
                <td style="width: 10px;">:</td>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td><strong>Nama Guru</strong></td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td><strong>Departemen</strong></td>
                <td>:</td>
                <td>{{ $karyawan->nama_dept }}</td>
            </tr>
            <tr>
                <td><strong>No. HP</strong></td>
                <td>:</td>
                <td>{{ $karyawan->no_hp }}</td>
            </tr>
        </table>

        <table class="tabelpresensi">
            <thead>
                <tr>
                    <th style="width: 30px;">No.</th>
                    <th style="width: 80px;">Tanggal</th>
                    <th style="width: 70px;">Jam Masuk</th>
                    <th style="width: 60px;">Foto</th>
                    <th style="width: 70px;">Jam Pulang</th>
                    <th style="width: 60px;">Foto</th>
                    <th style="width: 70px;">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presensi as $d)
                    @php
                        $status = '';
                        $statusClass = '';
                        $keterangan = '-';
                        
                        if ($d->status == 'h') {
                            $status = 'Hadir';
                            $statusClass = 'status-hadir';
                            $path_in = Storage::url('uploads/absensi/' . $d->foto_in);
                            $path_out = Storage::url('uploads/absensi/' . $d->foto_out);
                            $keterangan = 'Hadir';
                        } elseif ($d->status == 'i') {
                            $status = 'Izin';
                            $statusClass = 'status-izin';
                            $keterangan = $d->keterangan ?? '-';
                        } elseif ($d->status == 's') {
                            $status = 'Sakit';
                            $statusClass = 'status-sakit';
                            $keterangan = $d->keterangan ?? '-';
                        } elseif ($d->kode_izin && $d->nama_cuti) {
                            $status = 'Cuti';
                            $statusClass = 'status-cuti';
                            $keterangan = $d->nama_cuti;
                        } else {
                            $status = 'Alpa';
                            $statusClass = 'status-alpha';
                        }
                    @endphp

                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: center;">{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                        @if($d->status == 'h')
                            <td style="text-align: center;">{{ $d->jam_in }}</td>
                            <td style="text-align: center;"><img src="{{ url($path_in) }}" alt="" class="foto"></td>
                            <td style="text-align: center;">{{ $d->jam_out ?? 'Belum Absen' }}</td>
                            <td style="text-align: center;">
                                @if ($d->jam_out)
                                    <img src="{{ url($path_out) }}" alt="" class="foto">
                                @else
                                    <img src="{{ asset('assets/img/camera.jpg') }}" alt="" class="foto">
                                @endif
                            </td>
                        @else
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                        @endif
                        <td style="text-align: center;" class="{{ $statusClass }}">{{ $status }}</td>
                        <td>{{ $keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>