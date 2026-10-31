<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Jurnal {{ $karyawan->nama_lengkap }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 15px; }

        .kop-laporan { margin-bottom: 15px; text-align: center; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none !important; vertical-align: middle; }
        .kop-logo { width: 70px; height: auto; }
        .kop-title { margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .kop-subtitle { margin: 3px 0; font-size: 14px; font-weight: bold; }
        .kop-alamat { font-size: 10px; font-style: italic; margin: 0; }
        .kop-line { border: none; border-top: 3px double #000; margin: 8px 0; }

        .info-table { width: 100%; margin-bottom: 12px; }
        .info-table td { padding: 1px 0; font-size: 11px; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px 6px; font-size: 10px; }
        table.data th { background: #e0e0e0; font-weight: bold; text-align: center; }

        .kelas-title { font-size: 12px; font-weight: bold; margin: 14px 0 6px 0; padding: 4px 6px; background: #d0d0d0; border: 1px solid #000; }

        .summary-box { margin-top: 14px; padding: 6px 8px; border: 1px solid #000; font-size: 11px; }
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }

        .no-print-actions {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: white; padding: 8px 16px;
            display: flex; gap: 8px; align-items: center; z-index: 999;
        }
        .no-print-actions button, .no-print-actions a {
            padding: 6px 14px; border: none; border-radius: 4px;
            font-size: 12px; font-weight: bold; cursor: pointer; text-decoration: none; color: white;
        }
        .btn-print { background: #444; }
        .btn-pdf { background: #666; }
        .btn-back { background: #888; }

        @media print {
            .no-print-actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print-actions">
        <a href="/panel/jurnal/laporan" class="btn-back">Kembali</a>
        <button onclick="window.print()" class="btn-print">Cetak</button>
        <form action="/panel/jurnal/cetaklaporan" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
            <input type="hidden" name="dari_tanggal" value="{{ $dari_tanggal }}">
            <input type="hidden" name="sampai_tanggal" value="{{ $sampai_tanggal }}">
            <button type="submit" name="exportpdf" class="btn-pdf">Download PDF</button>
        </form>
    </div>

    <div style="margin-top: 50px;">
        <div class="kop-laporan">
            <table class="kop-table">
                <tr>
                    <td style="width: 80px; text-align: center;">
                        <img src="{{ asset('assets/img/website.png') }}" alt="Logo" class="kop-logo">
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

        <table class="info-table">
            <tr><td width="100">Nama Pegawai</td><td width="10">:</td><td><strong>{{ $karyawan->nama_lengkap }}</strong></td></tr>
            <tr><td>Mapel / Bid. Studi</td><td>:</td><td>{{ $karyawan->jabatan }}</td></tr>
            <tr><td>Jabatan</td><td>:</td><td>{{ $karyawan->nama_dept }}</td></tr>
            <tr><td>No. HP</td><td>:</td><td>{{ $karyawan->no_hp }}</td></tr>
            <tr><td>Periode</td><td>:</td><td>{{ date('d-m-Y', strtotime($dari_tanggal)) }} s/d {{ date('d-m-Y', strtotime($sampai_tanggal)) }}</td></tr>
        </table>

        @php
            $grouped = $jurnal->groupBy('kode_kelas');
            $kelasNo = 0;
            $noUrut = 0;
        @endphp

        @if($jurnal->count() > 0)
            @foreach($grouped as $kodeKelas => $jurnalKelas)
                @php
                    $kelasNo++;
                    $namaKelas = $jurnalKelas->first()->nama_kelas ?? $kodeKelas;
                @endphp

                <div class="kelas-title">{{ $kelasNo }}. KELAS {{ strtoupper($namaKelas) }} &mdash; {{ $jurnalKelas->count() }} Pertemuan</div>

                <table class="data">
                    <thead>
                        <tr>
                            <th width="25">No</th>
                            <th>Mata Pelajaran</th>
                            <th width="75">Tanggal</th>
                            <th width="65">Jam</th>
                            <th>Materi / Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnalKelas as $pIdx => $d)
                            <tr>
                                <td style="text-align:center;">{{ $pIdx + 1 }}</td>
                                <td>{{ $d->nama_mapel }}</td>
                                <td style="text-align:center;">{{ date('d/m/Y', strtotime($d->tanggal)) }}</td>
                                <td style="text-align:center;">{{ date('H:i', strtotime($d->jam_mulai)) }}-{{ date('H:i', strtotime($d->jam_selesai)) }}</td>
                                <td>{{ $d->materi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            <p style="text-align:center; padding:40px;">Tidak ada data jurnal untuk periode ini.</p>
        @endif

        <div class="summary-box">
            <strong>RINGKASAN PERIODE {{ date('d-m-Y', strtotime($dari_tanggal)) }} s/d {{ date('d-m-Y', strtotime($sampai_tanggal)) }}:</strong>
            Total Pertemuan: <strong>{{ $jurnal->count() }}</strong> &nbsp;|&nbsp;
            Total Kelas: <strong>{{ $grouped->count() }}</strong>
        </div>

        <div class="footer">
            <p>Tangerang, {{ date('d') }} {{ $namabulan[intval(date('n'))] }} {{ date('Y') }}</p>
            <br><br><br>
            <p><strong>( ____________________ )</strong></p>
        </div>
    </div>
</body>
</html>
