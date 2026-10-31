@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title text-dark fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-whatsapp text-success me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"></path>
                        <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"></path>
                    </svg>
                    Konfigurasi WA Gateway Fonnte
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-status-start bg-success"></div>
                    <div class="card-body p-4">
                        @if (Session::get('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><path d="M9 12l2 2l4 -4"></path></svg>
                                </div>
                                <div>
                                    {{ Session::get('success') }}
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                        @endif

                        @if (Session::get('warning'))
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75L13.84 4a2 2 0 0 0 -3.68 0L3.16 16.25A2 2 0 0 0 5 19z"></path></svg>
                                </div>
                                <div>
                                    {{ Session::get('warning') }}
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                        @endif

                        <p class="text-muted mb-4">
                            Konfigurasikan integrasi WhatsApp otomatis untuk mempermudah alur persetujuan, notifikasi cuti, sakit, izin, dan konfirmasi kehadiran guru/karyawan secara instan melalui layanan Fonnte.
                        </p>

                        <form action="/konfigurasi/updatewagateway" method="POST">
                            @csrf
                            
                            <!-- Token Input -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Fonnte API Token</label>
                                <div class="input-group input-group-flat border rounded">
                                    <input type="password" class="form-control border-0" id="wa_gateway_token" name="wa_gateway_token" value="{{ $wa_token }}" placeholder="Masukkan token API Fonnte Anda..." required>
                                    <span class="input-group-text bg-white border-0" style="cursor: pointer;" onclick="toggleTokenVisibility()">
                                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                                        </svg>
                                    </span>
                                </div>
                                <small class="form-hint text-muted mt-1">
                                    Dapatkan API token perangkat Anda langsung melalui dashboard resmi <a href="https://fonnte.com/" target="_blank" class="text-decoration-underline text-success fw-bold">Fonnte</a>.
                                </small>
                            </div>

                            <!-- Admin Numbers Grid -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-1">Nomor WhatsApp Penerima Notifikasi (Admin / Kepsek)</label>
                                <p class="text-muted small mb-3">
                                    Pengajuan baru dari guru/karyawan akan diteruskan ke nomor WhatsApp yang terdaftar di bawah ini (minimal 1 nomor, maksimal 4 nomor). Gunakan format lokal seperti <strong>08...</strong> atau internasional <strong>628...</strong>.
                                </p>
                                
                                <div class="row g-3">
                                    <!-- Admin 1 -->
                                    <div class="col-md-3">
                                        <div class="card bg-light border-0 p-3 text-center rounded shadow-sm">
                                            <span class="badge bg-success-lt mb-3 w-auto mx-auto">Admin #1 (Utama)</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold text-start mb-1">Nama Admin</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_name_1" value="{{ $name1 }}" placeholder="Nama Lengkap Admin" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-semibold text-start mb-1">No. WhatsApp</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_1" value="{{ $admin1 }}" placeholder="Contoh: 08123456789" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin 2 -->
                                    <div class="col-md-3">
                                        <div class="card bg-light border-0 p-3 text-center rounded shadow-sm">
                                            <span class="badge bg-secondary-lt mb-3 w-auto mx-auto">Admin #2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold text-start mb-1">Nama Admin</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_name_2" value="{{ $name2 }}" placeholder="Opsional">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-semibold text-start mb-1">No. WhatsApp</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_2" value="{{ $admin2 }}" placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin 3 -->
                                    <div class="col-md-3">
                                        <div class="card bg-light border-0 p-3 text-center rounded shadow-sm">
                                            <span class="badge bg-secondary-lt mb-3 w-auto mx-auto">Admin #3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold text-start mb-1">Nama Admin</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_name_3" value="{{ $name3 }}" placeholder="Opsional">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-semibold text-start mb-1">No. WhatsApp</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_3" value="{{ $admin3 }}" placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin 4 -->
                                    <div class="col-md-3">
                                        <div class="card bg-light border-0 p-3 text-center rounded shadow-sm">
                                            <span class="badge bg-secondary-lt mb-3 w-auto mx-auto">Admin #4</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold text-start mb-1">Nama Admin</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_name_4" value="{{ $name4 }}" placeholder="Opsional">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-semibold text-start mb-1">No. WhatsApp</label>
                                                <input type="text" class="form-control text-center bg-white border" name="admin_4" value="{{ $admin4 }}" placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                        <path d="M12 14m-2 0a2 2 0 0 1 4 0a2 2 0 0 1 -4 0"></path>
                                        <path d="M14 4l0 4l-6 0l0 -4"></path>
                                    </svg>
                                    Simpan Konfigurasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTokenVisibility() {
        const tokenInput = document.getElementById('wa_gateway_token');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (tokenInput.type === 'password') {
            tokenInput.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="3" y1="3" x2="21" y2="21"></line>
                <path d="M10.584 10.587a2 2 0 0 0 2.829 2.83"></path>
                <path d="M9.363 5.365a9.001 9.001 0 0 1 12.637 6.635a9.006 9.006 0 0 1 -4.017 5.986m-2.5 1.514a9 9 0 0 1 -15.483 -7.5a9 9 0 0 1 4.341 -5.997"></path>
            `;
        } else {
            tokenInput.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="12" cy="12" r="2"></circle>
                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
            `;
        }
    }
</script>
@endsection
