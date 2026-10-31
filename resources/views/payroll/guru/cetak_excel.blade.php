<table border="1" cellspacing="0" cellpadding="3">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th> 
            <th rowspan="2">Tunjangan</th>
            <th colspan="3">Honorarium</th>
            <th colspan="3">Kehadiran</th>
            <th rowspan="2">Dansos</th>
            <!-- <th rowspan="2">Sim Kop</th> hapus -->
            <th rowspan="2">Diterima</th>
            <th rowspan="2">Paraf</th>
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
                <td>{{ $index + 1 }}</td>
                <td>{{ $g->nama_lengkap }}</td> 
                <td>{{ $g->tunjangan_jabatan }}</td>

                <td>{{ $g->tjm }}</td>
                <td>{{ $g->gaji_pokok }}</td>
                <td>{{ $g->jml_gaji_pokok }}</td>

                <td>{{ $g->hadir }}</td>
                <td>{{ $g->transport }}</td>
                <td>{{ $g->jml_transport }}</td>

                <td>{{ $g->dana_sosial }}</td>
                <!-- <td>{{ $g->simpanan_koperasi }}</td> hapus -->
                <td>{{ $g->total_gaji }}</td>
                <td>{{ $index + 1 }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>TOTAL</strong></td>
            <td>{{ $rekap->sum('tunjangan_jabatan') }}</td>

            <td>{{ $rekap->sum('tjm') }}</td>
            <td></td>
            <td>{{ $rekap->sum('jml_gaji_pokok') }}</td>

            <td>{{ $rekap->sum('hadir') }}</td>
            <td></td>
            <td>{{ $rekap->sum('jml_transport') }}</td>

            <td>{{ $rekap->sum('dana_sosial') }}</td>
            <!-- <td>{{ $rekap->sum('simpanan_koperasi') }}</td> hapus -->
            <td colspan="2">{{ $rekap->sum('total_gaji') }}</td>
        </tr>
    </tfoot>
</table>
