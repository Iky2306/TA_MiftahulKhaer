<style>
    .excel-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #ffffff;
        border: 1px solid #d2d0ce;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-top: 10px;
    }
    .excel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #323130;
        min-width: 600px;
    }
    .excel-table th {
        background-color: #1e74fd; /* SiGuru Blue */
        color: #ffffff;
        font-weight: 600;
        padding: 10px 8px;
        border: 1px solid #0e65e6;
        text-align: center;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
    }
    .excel-table td {
        padding: 8px 6px;
        border: 1px solid #edebe9;
        text-align: center;
        vertical-align: middle;
    }
    .excel-table tbody tr:nth-child(even) {
        background-color: #faf9f8;
    }
    .excel-table tbody tr:hover {
        background-color: #e8f1ff;
        outline: 1px solid #1e74fd;
    }
    /* Status styles matching conditional formatting */
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        font-weight: bold;
        font-size: 10px;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .badge-h { background-color: #e2f0d9; color: #385723; }
    .badge-i { background-color: #fff2cc; color: #7f6000; }
    .badge-s { background-color: #ddebf7; color: #1f4e78; }
    .badge-c { background-color: #e8f8f5; color: #117864; }
    .badge-libur { background-color: #c6efce; color: #006100; }
    
    .text-left { text-align: left !important; }
    .text-bold { font-weight: bold; }
    .text-danger { color: #c00000 !important; }
    .text-success { color: #385723 !important; }
    .empty-cell { color: #a19f9d; font-style: italic; }
</style>

@if ($histori->isEmpty())
    <div class="alert alert-outline-warning">
        <p class="m-0">Tidak ada data presensi untuk periode ini.</p>
    </div>
@else
    <div class="excel-table-container">
        <table class="excel-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Tanggal</th>
                    <th style="width: 15%">Jadwal</th>
                    <th style="width: 13%">Jam Masuk</th>
                    <th style="width: 13%">Jam Pulang</th>
                    <th style="width: 12%">Status</th>
                    <th style="width: 27%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($histori as $d)
                    <tr>
                        <td class="text-bold" style="background-color: #f3f2f1; color: #605e5c;">{{ $loop->iteration }}</td>
                        <td class="text-bold">{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                        
                        @if ($d->status == 'h')
                            <td>{{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}</td>
                            <td class="text-bold text-success">{{ date('H:i', strtotime($d->jam_in)) }}</td>
                            <td class="text-bold">
                                {!! $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '<span class="text-danger">Belum Absen Pulang</span>' !!}
                            </td>
                            <td><span class="status-badge badge-h">Hadir</span></td>
                            <td class="text-left">
                                <span class="text-success" style="font-weight: 600;">Hadir</span>
                            </td>
                        @elseif ($d->status == 'i')
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td><span class="status-badge badge-i">Izin</span></td>
                            <td class="text-left text-bold">IZIN - {{ $d->keterangan }}</td>
                        @elseif ($d->status == 's')
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td><span class="status-badge badge-s">Sakit</span></td>
                            <td class="text-left text-bold">
                                SAKIT - {{ $d->keterangan }}
                                @if (!empty($d->doc_sid))
                                    <span style="color: #0f4c81; font-size: 10px;">(Surat Dokter Dilampirkan)</span>
                                @endif
                            </td>
                        @elseif ($d->status == 'c')
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td><span class="status-badge badge-c">Cuti</span></td>
                            <td class="text-left text-bold">CUTI - {{ $d->keterangan }}</td>
                        @elseif ($d->status == 'libur')
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td class="empty-cell">-</td>
                            <td><span class="status-badge badge-libur">Libur</span></td>
                            <td class="text-left text-success" style="font-weight: 600;">HARI LIBUR - {{ $d->keterangan }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
