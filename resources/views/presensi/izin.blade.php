@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/pengajuan" class="headerButton">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle" style="font-weight: 600; letter-spacing: 0.5px;">Riwayat Pengajuan</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection
@section('content')
    <style>
        .excel-ribbon-toolbar {
            background-color: #f3f2f1;
            border: 1px solid #edebe9;
            border-radius: 8px;
            padding: 12px;
            margin-top: 65px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            margin-bottom: 15px;
        }
        .excel-ribbon-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e74fd;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .excel-select {
            height: 38px !important;
            border: 1px solid #d2d0ce !important;
            border-radius: 4px !important;
            font-size: 13px !important;
            color: #323130 !important;
            background-color: #ffffff !important;
            padding: 0 10px !important;
            box-shadow: none !important;
        }
        .excel-btn-search {
            background-color: #1e74fd !important;
            border-color: #1e74fd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            height: 38px;
            border-radius: 4px !important;
            font-size: 13px !important;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(30, 116, 253, 0.2) !important;
            transition: all 0.2s ease !important;
        }
        .excel-btn-search:hover {
            background-color: #0e65e6 !important;
            border-color: #0e65e6 !important;
            box-shadow: 0 3px 6px rgba(14, 101, 230, 0.3) !important;
        }
        
        /* Excel Table Grid Style */
        .excel-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: #ffffff;
            border: 1px solid #d2d0ce;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 100px;
        }
        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #323130;
            min-width: 700px;
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
        .excel-table tbody tr {
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .excel-table tbody tr:nth-child(even) {
            background-color: #faf9f8;
        }
        .excel-table tbody tr:hover {
            background-color: #e8f1ff;
            outline: 1px solid #1e74fd;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 10px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-type-i { background-color: #fff2cc; color: #7f6000; }
        .badge-type-s { background-color: #ddebf7; color: #1f4e78; }
        .badge-type-c { background-color: #e8f8f5; color: #117864; }
        
        .badge-approved { background-color: #e2f0d9; color: #385723; }
        .badge-pending { background-color: #fff2cc; color: #7f6000; }
        .badge-rejected { background-color: #fce4d6; color: #c00000; }
        
        .text-left { text-align: left !important; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #605e5c !important; }
    </style>

    <div class="row">
        <div class="col">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp
            @if (Session::get('success'))
                <div class="alert alert-success" style="border-radius: 8px; font-size: 12px; margin-top: 10px; margin-bottom: 0px;">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="alert alert-warning" style="border-radius: 8px; font-size: 12px; margin-top: 10px; margin-bottom: 0px;">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="excel-ribbon-toolbar">
                <div class="excel-ribbon-title">
                    <ion-icon name="funnel-outline"></ion-icon> Filter Pengajuan
                </div>
                <form method="GET" action="/presensi/izin">
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group mb-0">
                                @php
                                    $namabulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                @endphp
                                <select name="bulan" id="bulan" class="form-control excel-select">
                                    <option value="">Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option {{ Request('bulan') == $i ? 'selected' : '' }} value="{{ $i }}">
                                            {{ $namabulanIndo[$i] }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group mb-0">
                                <select name="tahun" id="tahun" class="form-control excel-select">
                                    <option value="">Tahun</option>
                                    @php
                                        $tahun_awal = 2025;
                                        $tahun_sekarang = date('Y');
                                        for ($t = $tahun_awal; $t <= $tahun_sekarang; $t++) {
                                            $selected = (Request('tahun') == $t) ? 'selected' : '';
                                            echo "<option $selected value='$t'>$t</option>";
                                        }
                                    @endphp
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button class="btn excel-btn-search w-100">
                                <ion-icon name="search-outline"></ion-icon> Cari Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($dataizin->isEmpty())
        <div class="alert alert-outline-warning">
            <p class="m-0">Belum ada riwayat pengajuan izin, sakit, atau cuti.</p>
        </div>
    @else
        <div class="excel-table-container">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 13%">ID Pengajuan</th>
                        <th style="width: 15%">Tgl Mulai</th>
                        <th style="width: 15%">Tgl Selesai</th>
                        <th style="width: 10%">Jenis</th>
                        <th style="width: 8%">Durasi</th>
                        <th style="width: 12%">Status</th>
                        <th style="width: 22%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataizin as $d)
                        @php
                            if ($d->status == 'i') {
                                $tipeLabel = 'Izin';
                                $tipeBadge = 'badge-type-i';
                            } elseif ($d->status == 's') {
                                $tipeLabel = 'Sakit';
                                $tipeBadge = 'badge-type-s';
                            } elseif ($d->status == 'c') {
                                $tipeLabel = 'Cuti';
                                $tipeBadge = 'badge-type-c';
                            } else {
                                $tipeLabel = 'Lainnya';
                                $tipeBadge = 'badge-type-i';
                            }

                            if ($d->status_approved == 0) {
                                $appLabel = 'Menunggu';
                                $appBadge = 'badge-pending';
                            } elseif ($d->status_approved == 1) {
                                $appLabel = 'Disetujui';
                                $appBadge = 'badge-approved';
                            } else {
                                $appLabel = 'Ditolak';
                                $appBadge = 'badge-rejected';
                            }
                        @endphp
                        <tr class="card_izin" kode_izin="{{ $d->kode_izin }}" status_approved="{{ $d->status_approved }}"
                            data-toggle="modal" data-target="#actionSheetIconed">
                            <td class="text-bold" style="background-color: #f3f2f1; color: #605e5c;">{{ $loop->iteration }}</td>
                            <td class="text-bold" style="color: #1e74fd;">{{ $d->kode_izin }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tgl_izin_dari)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tgl_izin_sampai)) }}</td>
                            <td><span class="status-badge {{ $tipeBadge }}">{{ $tipeLabel }}</span></td>
                            <td class="text-bold">{{ hitunghari($d->tgl_izin_dari, $d->tgl_izin_sampai) }} Hari</td>
                            <td><span class="status-badge {{ $appBadge }}">{{ $appLabel }}</span></td>
                            <td class="text-left">
                                {{ $d->keterangan }}
                                @if ($d->status == 'c' && !empty($d->nama_cuti))
                                    <span class="badge bg-warning" style="font-size: 9px; padding: 2px 4px;">{{ $d->nama_cuti }}</span>
                                @endif
                                @if (!empty($d->doc_sid))
                                    <span style="color: #1e74fd; font-size: 10px; font-weight: bold; margin-left: 5px;">
                                        <ion-icon name="document-attach-outline"></ion-icon> SID
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="modal fade action-sheet" id="actionSheetIconed" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aksi Pengajuan</h5>
                </div>
                <div class="modal-body" id="showact">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade dialogbox" id="deleteConfirm" data-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apakah Anda yakin?</h5>
                </div>
                <div class="modal-body">
                    Data pengajuan ini akan dihapus secara permanen.
                </div>
                <div class="modal-footer">
                    <div class="btn-inline">
                        <a href="#" class="btn btn-text-secondary" data-dismiss="modal">Batal</a>
                        <a href="" class="btn btn-text-primary" id="hapuspengajuan">Hapus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $(".card_izin").click(function(e) {
                var kode_izin = $(this).attr("kode_izin");
                var status_approved = $(this).attr("status_approved");

                if (status_approved == 1) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Pengajuan sudah disetujui, tidak dapat diubah lagi!',
                        icon: 'warning'
                    })
                } else {
                    $("#showact").load('/izin/' + kode_izin + '/showact');
                }
            });
        });
    </script>
@endpush
