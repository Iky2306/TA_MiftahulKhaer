@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/guru/walikelas/monitoring?kode_kelas={{ $kelasAktif->kode_kelas }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Data Siswa {{ $kelasAktif->nama_kelas }}</div>
    <div class="right"></div>
</div>

<style>
    .wk-container {
        padding: 16px;
        margin-top: 65px;
        padding-bottom: 180px !important;
        background-color: #f1f5f9;
        min-height: 100vh;
    }

    .wk-profile-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        padding: 24px 16px;
        text-align: center;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .wk-profile-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: #38bdf8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px auto;
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        position: relative;
        overflow: hidden;
    }

    .wk-profile-circle svg, .wk-profile-circle ion-icon {
        font-size: 56px;
        color: #ffffff;
    }

    .wk-profile-title {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .wk-profile-subtitle {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .wk-profile-teacher {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
    }

    .wk-detail-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        height: 100%;
        overflow: hidden;
    }

    .wk-detail-header {
        padding: 14px 18px;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .wk-detail-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .wk-detail-table tr td {
        padding: 11px 16px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .wk-detail-table tr td:first-child {
        color: #475569;
        font-weight: 500;
        white-space: nowrap;
        width: 45%;
    }

    .wk-detail-table tr td:last-child {
        text-align: right;
        font-weight: 700;
        color: #2563eb;
        word-break: normal;
    }

    @media (max-width: 576px) {
        .wk-container {
            padding: 10px;
            margin-top: 55px;
            padding-bottom: 190px !important;
        }
        .wk-detail-header {
            padding: 12px 14px;
            font-size: 13.5px;
        }
        .wk-detail-table tr td {
            padding: 8px 10px;
            font-size: 11.5px;
        }
        .wk-detail-table tr td:first-child ion-icon {
            font-size: 13px !important;
            margin-right: 4px !important;
        }
        .custom-table th {
            font-size: 11.5px !important;
            padding: 7px 8px !important;
        }
        .custom-table td {
            font-size: 11.5px !important;
            padding: 7px 8px !important;
        }
        .dt-search-input {
            width: 130px;
            font-size: 11.5px;
            padding: 3px 8px;
        }
        .dt-toolbar {
            gap: 6px;
        }
        .dt-buttons .btn-dt {
            font-size: 11px;
            padding: 3px 8px;
        }
        .table-container-card {
            padding: 14px !important;
            margin-top: 12px !important;
            margin-bottom: 30px !important;
        }
        .empty-banner {
            padding: 20px 12px !important;
            margin-top: 10px !important;
        }
        .empty-banner-icon ion-icon {
            font-size: 28px !important;
        }
        .dt-footer {
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center;
            gap: 6px !important;
            width: 100%;
        }
        .dt-pagination {
            width: 100%;
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 3px !important;
        }
        .dt-pg-btn {
            font-size: 11px !important;
            padding: 3px 6px !important;
        }
        .dt-pg-num {
            font-size: 11px !important;
            padding: 2px 6px !important;
            min-width: 24px !important;
        }
        .dt-pg-btn-hide-mobile {
            display: none !important;
        }
    }

    .table-container-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        margin-top: 18px;
        margin-bottom: 24px;
        padding: 18px;
    }

    .dt-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }

    .dt-buttons .btn-dt {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .dt-buttons .btn-dt:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .dt-search-input {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12.5px;
        outline: none;
        width: 180px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th {
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        text-align: left;
    }

    .custom-table td {
        padding: 10px 14px;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
    }

    .custom-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    .empty-banner {
        background: #ffffff;
        border-radius: 14px;
        padding: 28px 18px;
        text-align: center;
        border: 1px solid #e2e8f0;
        margin-top: 10px;
    }

    .empty-banner-icon {
        width: 60px;
        height: 60px;
        background: #fef3c7;
        color: #d97706;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }

    .dt-pg-btn {
        background: transparent;
        border: none;
        color: #475569;
        font-size: 12px;
        padding: 3px 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dt-pg-btn:disabled {
        color: #cbd5e1;
        cursor: not-allowed;
    }
    .dt-pg-btn:hover:not(:disabled) {
        color: #0f172a;
        text-decoration: underline;
    }
    .dt-pg-num {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #334155;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 3px;
        cursor: pointer;
        min-width: 28px;
        text-align: center;
        transition: all 0.15s ease;
    }
    .dt-pg-num:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .dt-pg-num.active {
        background: #e2e8f0;
        border-color: #94a3b8;
        color: #0f172a;
        font-weight: 700;
    }
    .dt-sort-icon {
        font-size: 10px;
        color: #94a3b8;
        margin-left: 4px;
    }

    @media print {
        .appHeader, .dt-toolbar, .dt-footer, .headerButton, .btn-list {
            display: none !important;
        }
        .wk-container {
            margin-top: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
    }
</style>
@endsection

@section('content')
<div class="wk-container">

    {{-- Switcher Kelas jika mengampu > 1 Kelas --}}
    @if($kelasDiampu->count() > 1)
        <div class="mb-3">
            <form action="/guru/walikelas/siswa" method="GET" id="formSwitchKelas">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-bold text-dark mb-0" style="font-size: 12px; flex-shrink: 0;">Pilih Kelas:</label>
                    <select name="kode_kelas" class="form-select form-select-sm" onchange="document.getElementById('formSwitchKelas').submit();" style="font-size: 12px; border-radius: 8px;">
                        @foreach($kelasDiampu as $k)
                            <option value="{{ $k->kode_kelas }}" {{ $k->kode_kelas == $kelasAktif->kode_kelas ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }} ({{ $k->lembaga }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    @endif

    {{-- Top Section Grid --}}
    <div class="row g-3">
        {{-- Card Kiri: Profile Wali Kelas --}}
        <div class="col-12 col-md-5">
            <div class="wk-profile-card">
                <div class="wk-profile-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                        <path d="M6 6h10"></path>
                        <path d="M6 10h10"></path>
                        <path d="M6 14h10"></path>
                    </svg>
                </div>
                <div class="wk-profile-title">{{ $kelasAktif->nama_kelas }}</div>
                <div class="wk-profile-subtitle">Wali Kelas</div>
                <div class="wk-profile-teacher">{{ $kelasAktif->waliKelas->nama_lengkap ?? Auth::guard('karyawan')->user()->nama_lengkap }}</div>
            </div>
        </div>

        {{-- Card Kanan: Rincian Kelas --}}
        <div class="col-12 col-md-7">
            <div class="wk-detail-card">
                <div class="wk-detail-header">
                    Rincian Kelas
                </div>
                <table class="wk-detail-table">
                    <tr>
                        <td>
                            <ion-icon name="book-outline" style="color: #2563eb; font-size: 16px; margin-right: 6px; vertical-align: -2px;"></ion-icon>
                            Lembaga
                        </td>
                        <td>{{ $kelasAktif->lembaga }} Miftahul Khaer</td>
                    </tr>
                    <tr>
                        <td>
                            <ion-icon name="people-outline" style="color: #2563eb; font-size: 16px; margin-right: 6px; vertical-align: -2px;"></ion-icon>
                            Jumlah Siswa
                        </td>
                        <td>{{ $siswas->count() }}</td>
                    </tr>
                    <tr>
                        <td>
                            <ion-icon name="calendar-outline" style="color: #2563eb; font-size: 16px; margin-right: 6px; vertical-align: -2px;"></ion-icon>
                            Tahun Ajaran
                        </td>
                        <td>{{ date('Y') }}/{{ date('Y') + 1 }}</td>
                    </tr>
                    <tr>
                        <td>
                            <ion-icon name="location-outline" style="color: #2563eb; font-size: 16px; margin-right: 6px; vertical-align: -2px;"></ion-icon>
                            Lokasi Sekolah
                        </td>
                        <td>{{ $namaCabang }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Siswa Mapel / Data Siswa Table --}}
    <div class="table-container-card">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 17px; color: #1e293b;">Data Siswa</h4>
                <div class="text-muted" style="font-size: 12px;">{{ $kelasAktif->nama_kelas }}</div>
            </div>
        </div>

        @if($siswas->isNotEmpty())
            {{-- Toolbar Datatables --}}
            <div class="dt-toolbar mt-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="dt-buttons d-flex gap-1 me-2">
                        <a href="/guru/walikelas/siswa/export-pdf?kode_kelas={{ $kelasAktif->kode_kelas }}" class="btn-dt d-inline-flex align-items-center gap-1 text-decoration-none" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            PDF
                        </a>
                        <a href="/guru/walikelas/siswa/export-excel?kode_kelas={{ $kelasAktif->kode_kelas }}" class="btn-dt d-inline-flex align-items-center gap-1 text-decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                            Excel
                        </a>
                    </div>
                    <div class="d-flex align-items-center" style="font-size: 12.5px; color: #475569;">
                        Show
                        <select id="dtLengthSelect" class="form-select form-select-sm mx-1" style="width: auto; padding: 2px 24px 2px 8px; font-size: 12px;" onchange="changePageSize()">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 12.5px; color: #475569;">Search:</span>
                    <input type="text" id="dtSearchInput" class="dt-search-input" placeholder="" onkeyup="onSearchInput()">
                </div>
            </div>

            {{-- Custom Data Table --}}
            <div class="table-responsive">
                <table class="custom-table" id="siswaTable">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center; cursor: pointer;" onclick="sortTable(0)">
                                No <span class="dt-sort-icon" id="sortIcon0">▲</span>
                            </th>
                            <th style="width: 160px; cursor: pointer;" onclick="sortTable(1)">
                                NISN <span class="dt-sort-icon" id="sortIcon1">⇅</span>
                            </th>
                            <th style="cursor: pointer;" onclick="sortTable(2)">
                                Nama <span class="dt-sort-icon" id="sortIcon2">⇅</span>
                            </th>
                            <th style="width: 80px; text-align: center; cursor: pointer;" onclick="sortTable(3)">
                                L/P <span class="dt-sort-icon" id="sortIcon3">⇅</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="siswaTableBody">
                        @foreach($siswas as $s)
                            <tr data-no="{{ $loop->iteration }}" data-nisn="{{ $s->nisn ?? '' }}" data-nama="{{ strtolower($s->nama_siswa) }}" data-jk="{{ $s->jenis_kelamin }}">
                                <td style="text-align: center; color: #475569;">{{ $loop->iteration }}</td>
                                <td style="font-family: monospace; color: #334155;">{{ $s->nisn ?? '-' }}</td>
                                <td style="font-weight: 600; color: #0f172a;">{{ $s->nama_siswa }}</td>
                                <td style="text-align: center; font-weight: 700; color: {{ $s->jenis_kelamin == 'L' ? '#2563eb' : '#db2777' }};">
                                    {{ $s->jenis_kelamin }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Datatables Footer Pagination --}}
            <div class="dt-footer d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 pt-2 border-top">
                <div id="dtInfoText" style="font-size: 12.5px; color: #475569;">
                    Showing 1 to 10 of {{ $siswas->count() }} entries
                </div>
                <div class="dt-pagination d-flex align-items-center gap-1">
                    <button class="dt-pg-btn dt-pg-btn-hide-mobile" id="btnFirst" onclick="goToPage(1)">First</button>
                    <button class="dt-pg-btn" id="btnPrev" onclick="goToPage(currentPage - 1)">Previous</button>
                    <div id="dtPageNumbers" class="d-flex align-items-center gap-1"></div>
                    <button class="dt-pg-btn" id="btnNext" onclick="goToPage(currentPage + 1)">Next</button>
                    <button class="dt-pg-btn dt-pg-btn-hide-mobile" id="btnLast" onclick="goToPage(totalPages)">Last</button>
                </div>
            </div>
        @else
            {{-- Empty State jika data siswa belum di-input admin --}}
            <div class="empty-banner">
                <div class="empty-banner-icon">
                    <ion-icon name="alert-circle-outline" style="font-size: 36px;"></ion-icon>
                </div>
                <h4 class="fw-bold text-dark mb-2" style="font-size: 16px;">Data Siswa Belum Tersedia</h4>
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.5;">
                    Administrator Sekolah belum memasukkan/mengimpor data siswa untuk <strong>Kelas {{ $kelasAktif->nama_kelas }}</strong>. Silakan hubungi pihak admin sekolah untuk mengunggah data siswa kelas ini.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection

@push('myscript')
<script>
    let currentPage = 1;
    let pageSize = 10;
    let totalEntries = 0;
    let totalPages = 1;
    let filteredRows = [];
    let sortCol = 0;
    let sortAsc = true;

    document.addEventListener("DOMContentLoaded", function () {
        initDataTable();
    });

    function initDataTable() {
        const tbody = document.getElementById("siswaTableBody");
        if (!tbody) return;
        
        filteredRows = Array.from(tbody.querySelectorAll("tr"));
        updateTableDisplay();
    }

    function onSearchInput() {
        const query = document.getElementById("dtSearchInput").value.toLowerCase().trim();
        const tbody = document.getElementById("siswaTableBody");
        if (!tbody) return;

        const allRows = Array.from(tbody.querySelectorAll("tr"));
        filteredRows = allRows.filter(row => {
            const nisn = row.getAttribute("data-nisn").toLowerCase();
            const nama = row.getAttribute("data-nama").toLowerCase();
            return nisn.includes(query) || nama.includes(query);
        });

        currentPage = 1;
        updateTableDisplay();
    }

    function changePageSize() {
        const select = document.getElementById("dtLengthSelect");
        pageSize = parseInt(select.value) || 10;
        currentPage = 1;
        updateTableDisplay();
    }

    function sortTable(colIndex) {
        if (sortCol === colIndex) {
            sortAsc = !sortAsc;
        } else {
            sortCol = colIndex;
            sortAsc = true;
        }

        // Update Sort Icons
        for (let i = 0; i <= 3; i++) {
            const icon = document.getElementById(`sortIcon${i}`);
            if (icon) {
                if (i === sortCol) {
                    icon.innerText = sortAsc ? "▲" : "▼";
                } else {
                    icon.innerText = "⇅";
                }
            }
        }

        filteredRows.sort((a, b) => {
            let valA = "";
            let valB = "";

            if (colIndex === 0) {
                valA = parseInt(a.getAttribute("data-no")) || 0;
                valB = parseInt(b.getAttribute("data-no")) || 0;
            } else if (colIndex === 1) {
                valA = a.getAttribute("data-nisn");
                valB = b.getAttribute("data-nisn");
            } else if (colIndex === 2) {
                valA = a.getAttribute("data-nama");
                valB = b.getAttribute("data-nama");
            } else if (colIndex === 3) {
                valA = a.getAttribute("data-jk");
                valB = b.getAttribute("data-jk");
            }

            if (valA < valB) return sortAsc ? -1 : 1;
            if (valA > valB) return sortAsc ? 1 : -1;
            return 0;
        });

        updateTableDisplay();
    }

    function updateTableDisplay() {
        totalEntries = filteredRows.length;
        totalPages = Math.ceil(totalEntries / pageSize) || 1;

        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, totalEntries);

        const tbody = document.getElementById("siswaTableBody");
        if (!tbody) return;

        // Hide all rows first
        const allRows = Array.from(tbody.querySelectorAll("tr"));
        allRows.forEach(r => r.style.display = "none");

        // Show only active page rows in sorted order
        filteredRows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = "";
                tbody.appendChild(row); // Maintain sorted DOM order
            }
        });

        // Update Info Text (e.g. Showing 1 to 10 of 11 entries)
        const dtInfoText = document.getElementById("dtInfoText");
        if (dtInfoText) {
            if (totalEntries === 0) {
                dtInfoText.innerText = "Showing 0 to 0 of 0 entries";
            } else {
                dtInfoText.innerText = `Showing ${startIndex + 1} to ${endIndex} of ${totalEntries} entries`;
            }
        }

        renderPaginationButtons();
    }

    function renderPaginationButtons() {
        const btnFirst = document.getElementById("btnFirst");
        const btnPrev = document.getElementById("btnPrev");
        const btnNext = document.getElementById("btnNext");
        const btnLast = document.getElementById("btnLast");

        if (btnFirst) btnFirst.disabled = (currentPage === 1);
        if (btnPrev) btnPrev.disabled = (currentPage === 1);
        if (btnNext) btnNext.disabled = (currentPage === totalPages || totalEntries === 0);
        if (btnLast) btnLast.disabled = (currentPage === totalPages || totalEntries === 0);

        const container = document.getElementById("dtPageNumbers");
        if (!container) return;

        container.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const numBtn = document.createElement("button");
            numBtn.className = `dt-pg-num ${i === currentPage ? 'active' : ''}`;
            numBtn.innerText = i;
            numBtn.onclick = () => goToPage(i);
            container.appendChild(numBtn);
        }
    }

    function goToPage(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        updateTableDisplay();
    }
</script>
@endpush
