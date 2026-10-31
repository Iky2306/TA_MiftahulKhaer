<table border="1" cellspacing="0" cellpadding="3">
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
                <td>{{ $p->nama_lengkap }}</td>

                {{-- Tunjangan --}}
                <td>{{ $p->tunjangan_jabatan ?? 0 }}</td>
                <td>{{ $p->tunjangan_pengabdian ?? 0 }}</td>
                <td>{{ $p->tunjangan_lainnya ?? 0 }}</td>

                {{-- Honorarium --}}
                <td>{{ $p->honorarium_wkt ?? 0 }}</td>
                <td>{{ $p->honorarium_stn ?? 0 }}</td>
                <td>{{ $p->jml_honorarium ?? 0 }}</td>

                {{-- Kehadiran --}}
                <td>{{ $p->kehadiran_wkt ?? 0 }}</td>
                <td>{{ $p->kehadiran_stn ?? 0 }}</td>
                <td>{{ $p->jml_kehadiran ?? 0 }}</td>

                {{-- Potongan --}}
                <td>{{ $p->potongan_dansos ?? 0 }}</td>
                <td>{{ $p->potongan_fpmk ?? 0 }}</td>
                <td>{{ $p->potongan_lainnya ?? 0 }}</td>

                {{-- Diterima --}}
                <td>{{ $p->gaji_bersih ?? 0 }}</td>

                {{-- Paraf --}}
                <td>{{ $index + 1 }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>TOTAL</strong></td>

            {{-- Total Tunjangan --}}
            <td>{{ $rekap->sum('tunjangan_jabatan') }}</td>
            <td>{{ $rekap->sum('tunjangan_pengabdian') }}</td>
            <td>{{ $rekap->sum('tunjangan_lainnya') }}</td>

            {{-- Total Honorarium --}}
            <td>{{ $rekap->sum('honorarium_wkt') }}</td>
            <td></td>
            <td>{{ $rekap->sum('jml_honorarium') }}</td>

            {{-- Total Kehadiran --}}
            <td>{{ $rekap->sum('kehadiran_wkt') }}</td>
            <td></td>
            <td>{{ $rekap->sum('jml_kehadiran') }}</td>

            {{-- Total Potongan --}}
            <td>{{ $rekap->sum('potongan_dansos') }}</td>
            <td>{{ $rekap->sum('potongan_fpmk') }}</td>
            <td>{{ $rekap->sum('potongan_lainnya') }}</td>

            {{-- Total Diterima --}}
            <td colspan="2">{{ $rekap->sum('gaji_bersih') }}</td>
        </tr>
    </tfoot>
</table>