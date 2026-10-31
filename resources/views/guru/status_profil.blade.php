@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Status Mengajar</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
<style>
    /* ========= Schedule & Duty – Mobile-first Styles ========= */
    .sd-wrapper {
        margin-top: 60px;
        padding: 12px 14px 100px;
        background: #f0f2f5;
        min-height: 100vh;
    }

    /* ---- Flash Alert ---- */
    .sd-alert {
        border: none;
        border-radius: 12px;
        font-size: 0.82rem;
        padding: 10px 14px;
        margin-bottom: 12px;
        animation: sdSlideDown .35s ease-out;
    }
    .sd-alert-success { background: #d1fae5; color: #065f46; }
    .sd-alert-warning { background: #fef3c7; color: #92400e; }

    /* ---- Card Base ---- */
    .sd-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 14px;
        overflow: hidden;
    }
    .sd-card-body { padding: 16px; }

    /* ---- Subject Card ---- */
    .sd-subject-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .sd-subject-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .sd-subject-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
    }
    .sd-subject-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.2;
    }
    .sd-btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid #3b82f6;
        background: transparent;
        color: #3b82f6;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: all .2s ease;
    }
    .sd-btn-edit:active {
        background: #3b82f6;
        color: #fff;
        transform: scale(0.96);
    }
    .sd-subject-display {
        margin-top: 12px;
        padding: 14px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid #3b82f6;
    }
    .sd-subject-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .sd-subject-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
    }
    .sd-subject-empty {
        color: #94a3b8;
        font-weight: 500;
        font-style: italic;
    }

    /* ---- Edit Form ---- */
    .sd-form { margin-top: 14px; display: none; animation: sdSlideDown .25s ease-out; }
    .sd-form.active { display: block; }
    .sd-form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .sd-form-input {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.88rem;
        color: #1e293b;
        background: #f8fafc;
        transition: border-color .2s;
        outline: none;
    }
    .sd-form-input:focus { border-color: #3b82f6; background: #fff; }
    .sd-form-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 4px;
    }
    .sd-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 12px;
    }
    .sd-btn {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all .2s ease;
    }
    .sd-btn:active { transform: scale(0.96); }
    .sd-btn-cancel { background: #f1f5f9; color: #64748b; }
    .sd-btn-save { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.3); }

    /* ---- Profile Summary ---- */
    .sd-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sd-section-title::before {
        content: '';
        width: 4px;
        height: 18px;
        background: linear-gradient(180deg, #3b82f6, #1d4ed8);
        border-radius: 4px;
    }
    .sd-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-info-row:last-child { border-bottom: none; }
    .sd-info-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }
    .sd-info-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }
    .sd-icon-blue { background: #eff6ff; color: #3b82f6; }
    .sd-icon-red { background: #fef2f2; color: #ef4444; }
    .sd-icon-cyan { background: #ecfeff; color: #06b6d4; }
    .sd-icon-green { background: #f0fdf4; color: #22c55e; }
    .sd-icon-amber { background: #fffbeb; color: #f59e0b; }
    .sd-info-label {
        font-size: 0.82rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sd-info-value {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        text-align: right;
        flex-shrink: 0;
        max-width: 45%;
    }
    .sd-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .sd-badge-cyan { background: #ecfeff; color: #0891b2; }
    .sd-badge-green { background: #f0fdf4; color: #16a34a; }
    .sd-badge-slate { background: #f1f5f9; color: #475569; }

    /* ---- Teaching Schedule Table ---- */
    .sd-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -16px;
        padding: 0 16px;
    }
    .sd-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.78rem;
    }
    .sd-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 8px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .sd-table tbody td {
        padding: 10px 8px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .sd-table tbody tr:last-child td { border-bottom: none; }
    .sd-table .sd-day {
        font-weight: 700;
        color: #1e293b;
    }
    .sd-status-active {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.68rem;
        font-weight: 700;
        background: #dcfce7;
        color: #16a34a;
    }
    .sd-status-active::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #16a34a;
    }
    .sd-status-off {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.68rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #94a3b8;
    }

    .sd-empty-msg {
        text-align: center;
        padding: 20px 12px;
        color: #94a3b8;
        font-size: 0.85rem;
    }
    .sd-empty-msg ion-icon {
        font-size: 36px;
        display: block;
        margin: 0 auto 8px;
        color: #cbd5e1;
    }

    /* ---- Animations ---- */
    @keyframes sdSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="sd-wrapper">

    {{-- Flash Messages --}}
    @if (Session::get('success'))
        <div class="sd-alert sd-alert-success">
            <ion-icon name="checkmark-circle" style="margin-right: 6px; font-size: 16px; vertical-align: -3px;"></ion-icon>
            {{ Session::get('success') }}
        </div>
    @endif
    @if (Session::get('warning'))
        <div class="sd-alert sd-alert-warning">
            <ion-icon name="alert-circle" style="margin-right: 6px; font-size: 16px; vertical-align: -3px;"></ion-icon>
            {{ Session::get('warning') }}
        </div>
    @endif

    {{-- ============ Subject Teacher Card ============ --}}
    <div class="sd-card">
        <div class="sd-card-body">
            <div class="sd-subject-header">
                <div class="sd-subject-left">
                    <div class="sd-subject-icon">
                        <ion-icon name="book-outline"></ion-icon>
                    </div>
                    <h5 class="sd-subject-title">Guru Mata Pelajaran</h5>
                </div>
                <button type="button" class="sd-btn-edit" id="btnToggleEditMapel">
                    <ion-icon name="create-outline"></ion-icon> Ubah
                </button>
            </div>

            {{-- Display --}}
            <div class="sd-subject-display" id="mapelViewSection">
                <span class="sd-subject-label">Mata Pelajaran yang Diampu</span>
                @if($karyawan->jabatan)
                    <span class="sd-subject-value">{{ $karyawan->jabatan }}</span>
                @else
                    <span class="sd-subject-value sd-subject-empty">Belum diatur — ketuk Ubah untuk menambahkan</span>
                @endif
            </div>

            {{-- Edit Form --}}
            <form action="{{ route('guru.status.profil.update-mapel') }}" method="POST" class="sd-form" id="mapelFormSection">
                @csrf
                <label class="sd-form-label">Mata Pelajaran yang Diampu</label>
                <input type="text" class="sd-form-input" id="jabatan" name="jabatan"
                       value="{{ $karyawan->jabatan }}"
                       placeholder="cth. Informatika, Desain Web, Mikrotik"
                       maxlength="100" required>
                <p class="sd-form-hint">Pisahkan beberapa mata pelajaran dengan tanda koma.</p>
                <div class="sd-form-actions">
                    <button type="button" class="sd-btn sd-btn-cancel" id="btnCancelEditMapel">Batal</button>
                    <button type="submit" class="sd-btn sd-btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ Profile Summary Card ============ --}}
    <div class="sd-card">
        <div class="sd-card-body">
            <div class="sd-section-title">Ringkasan Profil</div>

            {{-- Position --}}
            <div class="sd-info-row">
                <div class="sd-info-left">
                    <div class="sd-info-icon sd-icon-blue"><ion-icon name="briefcase-outline"></ion-icon></div>
                    <span class="sd-info-label">Jabatan</span>
                </div>
                <span class="sd-info-value">{{ $departemen->nama_dept ?? '-' }}</span>
            </div>

            {{-- Location --}}
            <div class="sd-info-row">
                <div class="sd-info-left">
                    <div class="sd-info-icon sd-icon-red"><ion-icon name="location-outline"></ion-icon></div>
                    <span class="sd-info-label">Lokasi Sekolah</span>
                </div>
                <span class="sd-info-value">{{ $cabang->nama_cabang ?? '-' }}</span>
            </div>

            {{-- Total Teaching Hours --}}
            <div class="sd-info-row">
                <div class="sd-info-left">
                    <div class="sd-info-icon sd-icon-cyan"><ion-icon name="time-outline"></ion-icon></div>
                    <span class="sd-info-label">Jam Mengajar</span>
                </div>
                <span class="sd-badge sd-badge-cyan">{{ $totalJamMengajar ? $totalJamMengajar . ' Jam' : '0 Jam' }}</span>
            </div>

            {{-- Teaching Days --}}
            <div class="sd-info-row">
                <div class="sd-info-left">
                    <div class="sd-info-icon sd-icon-green"><ion-icon name="calendar-outline"></ion-icon></div>
                    <span class="sd-info-label">Hari Mengajar</span>
                </div>
                <span class="sd-badge sd-badge-green">{{ $totalHariAktif }} Hari / Minggu</span>
            </div>

            {{-- Schedule Config --}}
            <div class="sd-info-row">
                <div class="sd-info-left">
                    <div class="sd-info-icon sd-icon-amber"><ion-icon name="settings-outline"></ion-icon></div>
                    <span class="sd-info-label">Pengaturan Jadwal</span>
                </div>
                <span class="sd-badge sd-badge-slate">
                    {{ $source == 'individu' ? 'INDIVIDU' : ($source == 'departemen' ? 'DEPARTEMEN' : 'BELUM DIATUR') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ============ Teaching Schedule Card ============ --}}
    <div class="sd-card">
        <div class="sd-card-body">
            <div class="sd-section-title">Jadwal Mengajar</div>

            @if ($source == null)
                <div class="sd-empty-msg">
                    <ion-icon name="calendar-clear-outline"></ion-icon>
                    Jadwal belum dikonfigurasi.<br>
                    <small>Silakan hubungi admin untuk mengatur jadwal.</small>
                </div>
            @else
                <div class="sd-table-wrap">
                    <table class="sd-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Hari</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hariMengajar as $hari)
                                @php $jam = $jamKerja[$hari] ?? null; @endphp
                                <tr>
                                    <td class="sd-day">{{ $hari }}</td>
                                    @if($jam)
                                        <td style="text-align:center;">{{ $jam->jam_masuk }}</td>
                                        <td style="text-align:center;">{{ $jam->jam_pulang }}</td>
                                        <td style="text-align:center;">
                                            <span class="sd-status-active">Aktif</span>
                                        </td>
                                    @else
                                        <td colspan="3" style="text-align:center;">
                                            <span class="sd-status-off">Tidak Ada Jadwal</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnToggle = document.getElementById('btnToggleEditMapel');
        const btnCancel = document.getElementById('btnCancelEditMapel');
        const viewSection = document.getElementById('mapelViewSection');
        const formSection = document.getElementById('mapelFormSection');

        btnToggle.addEventListener('click', function() {
            viewSection.style.display = 'none';
            formSection.classList.add('active');
            formSection.style.display = 'block';
            document.getElementById('jabatan').focus();
            this.style.display = 'none';
        });

        btnCancel.addEventListener('click', function() {
            formSection.classList.remove('active');
            formSection.style.display = 'none';
            viewSection.style.display = 'block';
            btnToggle.style.display = '';
        });
    });
</script>
@endsection
