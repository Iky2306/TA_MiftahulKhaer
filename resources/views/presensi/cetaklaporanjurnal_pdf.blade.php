<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jurnal {{ $karyawan->nama_lengkap }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; margin: 10px; color: #000; }

        .kop-laporan { margin-bottom: 15px; text-align: center; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none !important; vertical-align: middle; }
        .kop-logo { width: 70px; height: auto; }
        .kop-title { margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .kop-subtitle { margin: 3px 0; font-size: 14px; font-weight: bold; }
        .kop-alamat { font-size: 10px; font-style: italic; margin: 0; }
        .kop-line { border: none; border-top: 3px double #000; margin: 8px 0; }

        .info { width: 100%; margin-bottom: 12px; }
        .info td { padding: 1px 0; font-size: 9pt; }

        .kelas-title { font-size: 10pt; font-weight: bold; margin: 12px 0 4px 0; padding: 3px 6px; background: #d0d0d0; border: 1px solid #000; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 5px; font-size: 8pt; }
        table.data th { background: #e0e0e0; font-weight: bold; text-align: center; }

        .summary { margin-top: 10px; padding: 5px 8px; border: 1px solid #000; font-size: 8pt; }
        .footer { margin-top: 25px; text-align: right; font-size: 9pt; }
        .footer p { margin: 0; }
    </style>
</head>
<body>
    <div class="kop-laporan">
        <table class="kop-table">
            <tr>
                <td style="width: 80px; text-align: center;">
                    <img src="{{ public_path('assets/img/website.png') }}" alt="Logo" class="kop-logo">
                </td>
                <td style="text-align: center;">
                    <h2 class="kop-title">PONPES MIFTAHUL KHAER</h2>
                    <h4 class="kop-subtitle">LAPORAN E-JURNAL MENGAJAR GURU</h4>
                    <p class="kop-alamat">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug,<br>Kabupaten Tangerang, Banten 15810</p>
                </td>
            </tr>
        </table>
        <hr class="kop-line">
    </div>

    <table class="info">
        <tr><td width="100">Nama Pegawai</td><td width="10">:</td><td><strong>{{ $karyawan->nama_lengkap }}</strong></td></tr>
        <tr><td>Mapel / Bid. Studi</td><td>:</td><td>{{ $karyawan->jabatan }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $karyawan->nama_dept }}</td></tr>
        <tr><td>No. HP</td><td>:</td><td>{{ $karyawan->no_hp }}</td></tr>
        <tr><td>Periode</td><td>:</td><td>{{ date('d-m-Y', strtotime($dari_tanggal)) }} s/d {{ date('d-m-Y', strtotime($sampai_tanggal)) }}</td></tr>
    </table>

    @php
        $grouped = $jurnal->groupBy('kode_kelas');
        $kelasNo = 0;
    @endphp

    @foreach($grouped as $kodeKelas => $jurnalKelas)
        @php
            $kelasNo++;
            $namaKelas = $jurnalKelas->first()->nama_kelas ?? $kodeKelas;
        @endphp

        <div class="kelas-title">{{ $kelasNo }}. KELAS {{ strtoupper($namaKelas) }} &mdash; {{ $jurnalKelas->count() }} Pertemuan</div>

        <table class="data">
            <thead>
                <tr>
                    <th width="16">No</th>
                    <th>Mata Pelajaran</th>
                    <th width="58">Tanggal</th>
                    <th width="50">Jam</th>
                    <th>Materi / Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnalKelas as $pIdx => $d)
                    <tr>
                        <td align="center">{{ $pIdx + 1 }}</td>
                        <td>{{ $d->nama_mapel }}</td>
                        <td align="center">{{ date('d/m/Y', strtotime($d->tanggal)) }}</td>
                        <td align="center">{{ date('H:i', strtotime($d->jam_mulai)) }}-{{ date('H:i', strtotime($d->jam_selesai)) }}</td>
                        <td>{{ $d->materi }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @if($jurnal->count() == 0)
        <p style="text-align:center; padding:30px;">Tidak ada data jurnal untuk periode ini.</p>
    @endif

    <div class="summary">
        <strong>RINGKASAN:</strong>
        Total Pertemuan: <strong>{{ $jurnal->count() }}</strong> &nbsp;|&nbsp;
        Total Kelas: <strong>{{ $grouped->count() }}</strong>
    </div>

    <div class="footer">
        <p>Tangerang, {{ date('d') }} {{ $namabulan[intval(date('n'))] }} {{ date('Y') }}</p>
        <br><br><br>
        <p><strong>( ____________________ )</strong></p>
    </div>
</body>
</html>
