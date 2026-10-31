<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .sheet {
            padding: 10mm;
            width: 297mm;
            height: auto;
            box-shadow: none;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            position: relative;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .institution {
            font-style: italic;
            font-size: 12px;
        }

        .logo {
            position: absolute;
            left: 10mm;
            width: 70px;
            height: 70px;
        }

        .info-box {
            margin: 15px 0;
            padding: 5px;
            border: 1px solid #000;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        table th {
            font-weight: bold;
        }

        .employee-name {
            text-align: left;
            padding-left: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 12px;
            text-align: right;
        }

        .libur-list {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #000;
        }

        .libur-list h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .libur-list ol {
            margin: 0;
            padding-left: 20px;
            font-size: 12px;
        }

        .libur-list li {
            margin-bottom: 3px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .sheet {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="A4 landscape">
    <section class="sheet">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/img/website.png') }}" class="logo" alt="Logo Sekolah">
            <div class="header-title">LAPORAN ABSENSI GURU</div>
            <div class="subtitle">PONPES MIFTAHUL KHAER</div>
            <div class="institution">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug, Suka Bakti, Kec. Curug, Kabupaten Tangerang, Banten 15810</div>
        </div>

        <!-- Info Periode -->
        <div class="info-box">
            Periode: <strong>{{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}</strong>
        </div>

        <!-- Tabel Presensi -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width:20%">Nama Guru</th>
                    <th colspan="{{ $jmlhari }}">Tanggal</th>
                    <th colspan="5" style="width:12%">Rekap</th>
                </tr>
                <tr>
                    @foreach ($rangetanggal as $d)
                        @if ($d != null)
                            <th>{{ date('d', strtotime($d)) }}</th>
                        @endif
                    @endforeach
                    <th>H</th>
                    <th>I</th>
                    <th>S</th>
                    <th>C</th>
                    <th>A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekap as $r)
                    <tr>
                        <td class="employee-name">{{ $r->nama_lengkap }}</td>
                        @php
                            $jml_hadir = 0;
                            $jml_izin = 0;
                            $jml_sakit = 0;
                            $jml_cuti = 0;
                            $jml_alpa = 0;
                        @endphp
                        @for ($i = 1; $i <= $jmlhari; $i++)
                            @php
                                $tgl = "tgl_".$i;
                                $tgl_presensi = $rangetanggal[$i-1];
                                $search_items = [
                                    'nik' => $r->nik,
                                    'tanggal_libur' => $tgl_presensi
                                ];
                                $ceklibur = cekkaryawanlibur($datalibur, $search_items);

                                $datapresensi = explode("|", $r->$tgl);
                                $status = $r->$tgl ? $datapresensi[2] : '';
                                if($status == 'h') $jml_hadir++;
                                if($status == 'i') $jml_izin++;
                                if($status == 's') $jml_sakit++;
                                if($status == 'c') $jml_cuti++;

                                $cekhari = gethari(date('D', strtotime($tgl_presensi)));
                                $hasJadwal = checkAdaJadwal($r->nik, $r->kode_dept ?? '', $r->kode_cabang ?? '', $tgl_presensi, $cekhari, $konfig_by_date ?? [], $konfig_harian ?? [], $konfig_dept ?? []);

                                $cell_content = '';
                                if(!empty($status)) {
                                    $cell_content = $status;
                                } elseif(!empty($ceklibur)) {
                                    $cell_content = 'L';
                                } elseif($hasJadwal) {
                                    $jml_alpa++;
                                    $cell_content = 'A';
                                }
                            @endphp
                            @if(!empty($ceklibur))
                                <td style="background-color: #2ecc71; color: white; font-weight: bold;">L</td>
                            @elseif($cell_content == 'A')
                                <td style="color: red; font-weight: bold;">A</td>
                            @else
                                <td>{{ $cell_content }}</td>
                            @endif
                        @endfor
                        <td>{{ $jml_hadir }}</td>
                        <td>{{ $jml_izin }}</td>
                        <td>{{ $jml_sakit }}</td>
                        <td>{{ $jml_cuti }}</td>
                        <td>{{ $jml_alpa }}</td>
                    </tr>

                    {{-- Simpan total agregat --}}
                    @php
                        $total_hadir = ($total_hadir ?? 0) + $jml_hadir;
                        $total_izin = ($total_izin ?? 0) + $jml_izin;
                        $total_sakit = ($total_sakit ?? 0) + $jml_sakit;
                        $total_cuti = ($total_cuti ?? 0) + $jml_cuti;
                        $total_alpa = ($total_alpa ?? 0) + $jml_alpa;
                    @endphp
                @endforeach

                {{-- Baris total keseluruhan --}}
                <tr style="background:#f0f0f0; font-weight:bold;">
                    <td colspan="{{ $jmlhari + 1 }}" style="text-align:center;">TOTAL KESELURUHAN :</td>
                    <td>{{ $total_hadir }}</td>
                    <td>{{ $total_izin }}</td>
                    <td>{{ $total_sakit }}</td>
                    <td>{{ $total_cuti }}</td>
                    <td>{{ $total_alpa }}</td>
                </tr>
            </tbody>

        </table>

        <!-- Daftar Libur -->
        <div class="libur-list">
            <h4>Keterangan Libur:</h4>
            <ol>
                @foreach ($harilibur as $d)
                    <li>{{ date('d-m-Y', strtotime($d->tanggal_libur)) }} - {{ $d->keterangan }}</li>
                @endforeach
            </ol>
        </div>

        <!-- Footer -->
        <div class="footer">
            Tangerang, {{ date('d-m-Y') }}
        </div>
    </section>

    <!-- Tombol Cetak -->
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Cetak Laporan
        </button>
    </div>
</body>
</html>
