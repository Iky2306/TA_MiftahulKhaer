@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/guru/jurnal/create" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Lengkapi Jurnal Kelas</div>
    <div class="right"></div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .input-wrapper {
        position: relative !important;
        width: 100% !important;
    }
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .select2-container--default .select2-selection--single {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        height: 44px !important;
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px !important;
        color: #1e293b !important;
        padding-left: 10px !important;
        padding-right: 25px !important;
        font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        right: 6px !important;
    }
    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        overflow: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        z-index: 1050 !important;
        position: absolute !important;
        left: 0 !important;
        top: 100% !important;
    }
    .select2-search--dropdown {
        padding: 6px !important;
        box-sizing: border-box !important;
    }
    .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        font-size: 13px !important;
        outline: none !important;
    }
    .select2-results__option {
        padding: 10px 12px !important;
        font-size: 13px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    
    /* Time Input Fix for Mobile */
    input[type="time"] {
        max-width: 100% !important;
        width: 100% !important;
        min-width: 0 !important;
        font-size: 12.5px !important;
        padding: 6px 2px !important;
        text-align: center !important;
        box-sizing: border-box !important;
    }
    input[type="time"]::-webkit-calendar-picker-indicator {
        width: 14px !important;
        height: 14px !important;
        margin-left: 2px !important;
        cursor: pointer;
    }

    /* File Input Fix for Mobile */
    input[type="file"] {
        max-width: 100% !important;
        width: 100% !important;
        min-width: 0 !important;
        font-size: 11px !important;
        padding: 5px !important;
        box-sizing: border-box !important;
    }
    input[type="file"]::file-selector-button {
        padding: 4px 8px !important;
        margin-right: 6px !important;
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        background: #e2e8f0 !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        cursor: pointer;
    }
    
    textarea {
        max-width: 100% !important;
        width: 100% !important;
        font-size: 13.5px !important;
    }
</style>
@endsection

@section('content')
<div class="section" style="margin-top: 70px !important; padding: 0 8px 140px 8px !important;">
    <div class="card" style="border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: visible;">
        <div class="card-body" style="padding: 14px 12px;">
            <div class="alert alert-success d-flex align-items-center mb-3 text-left" style="border-radius: 10px; padding: 10px 12px;">
                <ion-icon name="checkmark-circle" style="font-size: 24px; margin-right: 8px; flex-shrink: 0;"></ion-icon>
                <div style="font-size: 12.5px; line-height: 1.3;">Lokasi dikunci di: <br><strong style="font-size: 13.5px;">Kelas {{ $kelas->nama_kelas }}</strong></div>
            </div>

            <form action="/guru/jurnal/store" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kode_kelas" value="{{ $kelas->kode_kelas }}">

                <div class="form-group basic mb-2">
                    <div class="input-wrapper">
                        <label class="label" style="font-size: 12px; color:#6b7280; margin-bottom: 4px;">Mata Pelajaran (Cari dengan mengetik)</label>
                        <select name="nama_mapel" id="nama_mapel" class="form-control select2-search" required style="width: 100%;">
                            <option value="">-- Cari / Pilih Mapel --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->nama_mapel }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                        @if($mapels->count() == 0)
                            <small class="text-danger mt-1 font-weight-bold d-block" style="font-size: 11px;">Admin belum memasukkan satupun mata pelajaran untuk ruangan ini. Hubungi admin sekolah.</small>
                        @endif
                    </div>
                </div>

                <div class="form-group basic mb-2">
                    <div class="input-wrapper">
                        <label class="label" style="font-size: 12px; color:#6b7280; margin-bottom: 4px;">Waktu Mengajar (Discan Pada {{ date('H:i') }})</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label style="font-size: 10px; color:#94a3b8; margin-bottom: 2px; display: block;">Dari Jam</label>
                                <input type="time" class="form-control" name="jam_mulai" required value="{{ date('H:i') }}"
                                       style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; color: #312e81; font-weight: bold;">
                            </div>
                            <div class="col-6">
                                <label style="font-size: 10px; color:#94a3b8; margin-bottom: 2px; display: block;">Sampai Jam</label>
                                <input type="time" class="form-control" name="jam_selesai" required
                                       style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group basic mb-2">
                    <div class="input-wrapper">
                        <label class="label" style="font-size: 12px; color:#6b7280; margin-bottom: 4px;">Materi / Kegiatan Yang Diampu</label>
                        <textarea class="form-control" name="materi" rows="3" placeholder="Deskripsikan materi atau kegiatan yang diajarkan..." required
                                  style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px;"></textarea>
                    </div>
                </div>

                <div class="form-group basic mb-2">
                    <div class="input-wrapper">
                        <label class="label" style="font-size: 12px; color:#6b7280; margin-bottom: 4px;">Foto Dokumentasi (Opsional)</label>
                        <input type="file" class="form-control" name="foto" accept="image/*"
                               style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Format JPG/PNG, maks. 2MB</small>
                    </div>
                </div>

                <div class="mt-4 mb-2">
                    <button type="submit" class="btn btn-primary btn-block w-100" style="border-radius: 10px; font-weight: 600; padding: 12px 16px; font-size: 14px;">
                        <ion-icon name="save-outline" style="font-size: 18px; vertical-align: middle; margin-right: 4px;"></ion-icon> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-search').each(function() {
                $(this).select2({
                    placeholder: "-- Cari / Pilih Mapel --",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this).closest('.input-wrapper'),
                    language: {
                        noResults: function() { return "Mapel tidak ditemukan"; }
                    }
                });
            });
        }
    });
</script>
@endpush

