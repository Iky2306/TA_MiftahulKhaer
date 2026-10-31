@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle" style="font-weight: 600; letter-spacing: 0.5px;">Riwayat Presensi</div>
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
    </style>

    <div class="row">
        <div class="col">
            <div class="excel-ribbon-toolbar">
                <div class="excel-ribbon-title">
                    <ion-icon name="funnel-outline"></ion-icon> Filter Data Presensi
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="form-group mb-0">
                            @php 
                                $selectedBulan = Request('bulan') ?: date('m'); 
                                $namabulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            @endphp
                            <select name="bulan" id="bulan" class="form-control excel-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option {{ $selectedBulan == $i ? 'selected' : '' }} value="{{ $i }}">
                                        {{ $namabulanIndo[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-0">
                            @php $selectedTahun = Request('tahun') ?: date('Y'); @endphp
                            <select name="tahun" id="tahun" class="form-control excel-select">
                                @php
                                    $tahun_awal = 2024;
                                    $tahun_sekarang = date('Y');
                                    for ($t = $tahun_awal; $t <= $tahun_sekarang; $t++) {
                                        $selected = ($selectedTahun == $t) ? 'selected' : '';
                                        echo "<option $selected value='$t'>$t</option>";
                                    } 
                                @endphp
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" style="margin-right: -4px; margin-left: -4px;">
                    <div class="col-6" style="padding-right: 4px; padding-left: 4px;">
                        <button class="btn excel-btn-search w-100" id="getdata">
                            <ion-icon name="search-outline"></ion-icon> Tampilkan
                        </button>
                    </div>
                    <div class="col-6" style="padding-right: 4px; padding-left: 4px;">
                        <button type="button" id="btn-download-pdf" class="btn btn-outline-primary w-100" style="height: 38px; border-radius: 4px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 13px; border-color: #1e74fd; color: #1e74fd; background: white;">
                            <ion-icon name="download-outline" style="font-size: 16px;"></ion-icon> Unduh PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2" style="margin-bottom: 80px; padding-bottom: 20px;">
        <div class="col" id="showhistori"></div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            function loadHistori() {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                if (!bulan || !tahun) return;
                
                $("#showhistori").html('<div class="text-center mt-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading data...</p></div>');
                
                $.ajax({
                    type: 'POST',
                    url: '/gethistori',
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun
                    },
                    cache: false,
                    success: function(respond) {
                        $("#showhistori").html(respond);
                    }
                });
            }

            // Click Handler untuk tombol Unduh PDF
            $("#btn-download-pdf").click(function(e) {
                e.preventDefault();
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                if (!bulan || !tahun) return;
                var pdfUrl = "/presensi/downloadrekapabsensi?bulan=" + parseInt(bulan) + "&tahun=" + tahun;
                window.location.href = pdfUrl;
            });

            // Muat data otomatis saat pertama kali dibuka
            loadHistori();

            // Memuat data saat tombol ditekan
            $("#getdata").click(function(e) {
                loadHistori();
            });

            // Memuat data secara otomatis ketika bulan atau tahun diubah
            $("#bulan, #tahun").change(function(e) {
                loadHistori();
            });
        });
    </script>
@endpush
