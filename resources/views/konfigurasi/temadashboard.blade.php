@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Konfigurasi Tema
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (Session::get('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                        @endif

                        @if (Session::get('warning'))
                        <div class="alert alert-warning">
                            {{ Session::get('warning') }}
                        </div>
                        @endif

                        <form action="/konfigurasi/updatetemadashboard" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Tema Dashboard Pegawai</label>
                                <p class="text-muted">Tema yang dipilih akan berlaku untuk <strong>semua pegawai</strong>.</p>
                            </div>

                            <div class="row g-3">
                                {{-- Theme Default --}}
                                <div class="col-md-4">
                                    <label class="form-selectgroup-item" style="cursor:pointer;">
                                        <input type="radio" name="tema_dashboard" value="default" class="form-selectgroup-input"
                                            {{ ($tema ?? 'default') == 'default' ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label d-flex flex-column p-3 border rounded"
                                            style="min-height: 200px;">
                                            {{-- Preview Default --}}
                                            <span class="d-block rounded mb-2" style="height: 60px; background-color: #092c9f;"></span>
                                            <span class="d-flex gap-2 mb-2">
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background-image: linear-gradient(to bottom right, rgba(21, 189, 74, 1), rgba(21, 189, 74, 0.8));"></span>
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background-image: linear-gradient(to bottom right, rgba(184, 13, 45, 1), rgba(184, 13, 45, 0.8));"></span>
                                            </span>
                                            <span class="fw-bold text-center mt-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"></path></svg>
                                                Default (Biru)
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Theme Ramadan --}}
                                <div class="col-md-4">
                                    <label class="form-selectgroup-item" style="cursor:pointer;">
                                        <input type="radio" name="tema_dashboard" value="ramadan" class="form-selectgroup-input"
                                            {{ ($tema ?? 'default') == 'ramadan' ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label d-flex flex-column p-3 border rounded"
                                            style="min-height: 200px;">
                                            {{-- Preview Ramadan --}}
                                            <span class="d-block rounded mb-2" style="height: 60px; background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);"></span>
                                            <span class="d-flex gap-2 mb-2">
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%);"></span>
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background: linear-gradient(135deg, #C62828 0%, #EF5350 100%);"></span>
                                            </span>
                                            <span class="fw-bold text-center mt-auto">
                                                Hijau
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Theme merdeka --}}
                                <div class="col-md-4">
                                    <label class="form-selectgroup-item" style="cursor:pointer;">
                                        <input type="radio" name="tema_dashboard" value="merdeka" class="form-selectgroup-input"
                                            {{ ($tema ?? 'default') == 'merdeka' ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label d-flex flex-column p-3 border rounded"
                                            style="min-height: 200px;">
                                            {{-- Preview merdeka --}}
                                            <span class="d-block rounded mb-2" style="height: 60px; background-color: #B71C1C;"></span>
                                            <span class="d-flex gap-2 mb-2">
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background: linear-gradient(135deg, #C62828 0%, #E53935 100%);"></span>
                                                <span class="d-block rounded" style="height: 30px; width: 50%; background: linear-gradient(135deg, #BF360C 0%, #FF5722 100%);"></span>
                                            </span>
                                            <span class="fw-bold text-center mt-auto">
                                                Merah
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                    </svg>
                                    Simpan Tema
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-selectgroup-input:checked + .form-selectgroup-label {
        border-color: #206bc4 !important;
        box-shadow: 0 0 0 3px rgba(32, 107, 196, 0.25);
        background-color: #f8fafc;
    }
</style>
@endsection
