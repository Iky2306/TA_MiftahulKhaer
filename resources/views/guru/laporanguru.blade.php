@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Rekap Presensi</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
<style>
    /* Main Container Adjustments */
    .main-content {
        padding-top: 80px; /* Memberi jarak dari header fixed */
        min-height: calc(100vh - 80px);
    }
    
    .laporan-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .laporan-title {
        text-align: center;
        color: #2196F3;
        margin-bottom: 25px;
        font-weight: 600;
        font-size: 1.4rem;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
        font-size: 0.9rem;
    }
    
    .form-select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        background-color: #f9f9f9;
        font-size: 15px;
        transition: border 0.3s;
    }
    
    .form-select:focus {
        border-color: #2196F3;
        outline: none;
    }
    
    .btn-print {
        background: #2196F3;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 10px;
        width: 100%;
        font-weight: 500;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 25px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .btn-print:hover {
        background: #0D47A1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }
    
    .info-text {
        text-align: center;
        font-size: 13px;
        color: #777;
        margin-top: 20px;
        line-height: 1.5;
    }
    
    /* Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .card-body {
        padding: 0;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .laporan-container {
            padding: 20px;
            margin: 0 15px;
        }
        
        .main-content {
            padding-top: 70px;
        }
    }
        .print-container {
        display: none;
    }
</style>

<div class="main-content">
    <div class="section">
        <div class="card">
            <div class="card-body">
                <div class="laporan-container">
                    <h3 class="laporan-title">
                        <ion-icon name="calendar-outline" style="vertical-align: middle;"></ion-icon>
                        LAPORAN PRESENSI
                    </h3>
                    
                    <!-- Form untuk submit data -->
                    <form id="formLaporan" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select" required>
                                <option value="">- Pilih Bulan -</option>
                                @foreach ($namabulan as $key => $bulan)
                                    @if ($key != 0)
                                        <option value="{{ $key }}" {{ $key == $bulanini ? 'selected' : '' }}>
                                            {{ $bulan }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select" required>
                                <option value="">- Pilih Tahun -</option>
                                @for($tahun = date('Y'); $tahun >= 2025; $tahun--)
                                    <option value="{{ $tahun }}" {{ $tahun == $tahunini ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <button type="button" onclick="handleCetak()" class="btn-print">
                            <ion-icon name="print-outline"></ion-icon>
                            CETAK LAPORAN
                        </button>
                    </form>

                    <!-- Hidden form untuk pembukaan tab baru -->
                    <form id="printForm" action="{{ route('laporan.guru.cetak') }}" method="POST" class="print-container">
                        @csrf
                        <input type="hidden" name="bulan" id="printBulan">
                        <input type="hidden" name="tahun" id="printTahun">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleCetak() {
    // Validasi form
    const form = document.getElementById('formLaporan');
    const bulan = form.elements['bulan'].value;
    const tahun = form.elements['tahun'].value;
    
    if (!bulan || !tahun) {
        alert('Harap pilih bulan dan tahun terlebih dahulu');
        return;
    }

    // Set nilai ke hidden form
    document.getElementById('printBulan').value = bulan;
    document.getElementById('printTahun').value = tahun;
    
    // Submit form untuk cetak
    document.getElementById('printForm').submit();
    
    // Alternatif untuk mobile (jika masih bermasalah)
    // cetakViaAjax(bulan, tahun);
}

// Alternatif menggunakan AJAX jika diperlukan
function cetakViaAjax(bulan, tahun) {
    fetch("{{ route('laporan.guru.cetak') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: `bulan=${bulan}&tahun=${tahun}`
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Network response was not ok.');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal mencetak laporan');
    });
}
</script>
@endsection