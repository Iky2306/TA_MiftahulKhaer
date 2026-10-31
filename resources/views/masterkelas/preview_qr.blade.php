@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Preview Cetak QR: {{ $kelas->nama_kelas }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-danger" id="btn-download-pdf" onclick="downloadPDF()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-type-pdf" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-1" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1.5a2.5 2.5 0 0 0 2.5 -2.5v-1a2.5 2.5 0 0 0 -2.5 -2.5h-1.5z" /></svg>
                        Unduh PDF
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                        Cetak / Print
                    </button>
                    <a href="/panel/masterkelas" class="btn btn-link link-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="qr-print-container">
            <div class="qr-card" id="qr-card-element">
                <div class="logo-top">
                    <img src="{{ asset('assets/img/website.png') }}" alt="Logo Sekolah">
                </div>
                <div class="school-header">
                    SCAN E-JURNAL MENGAJAR
                </div>
                <div class="class-name">
                    KELAS {{ $kelas->nama_kelas }}
                </div>

                <div class="qr-body">
                    <div class="qr-wrapper">
                        <img src="{{ $qr_base64 }}" alt="QR Code" class="qr-code-img">
                        <div class="qr-logo-overlay">
                            <img src="{{ asset('assets/img/website.png') }}" alt="Logo Sekolah">
                        </div>
                    </div>
                </div>

                <div class="qr-footer">
                    <p>Harap scan QR Code ini menggunakan aplikasi untuk memulai & log kehadiran Anda hari ini.</p>
                    <div class="class-code">
                        <strong>KODE: {{ $kelas->kode_kelas }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .qr-print-container {
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    .qr-card {
        width: 400px;
        background: #ffffff;
        padding: 35px 30px;
        border: 10px solid #206bc4;
        text-align: center;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        position: relative;
    }
    .logo-top img {
        height: 55px;
        width: auto;
        margin-bottom: 10px;
    }
    .school-header {
        font-size: 13px;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 2px;
        margin-bottom: 5px;
    }
    .class-name {
        font-size: 30px;
        font-weight: 900;
        color: #206bc4;
        margin-bottom: 20px;
        line-height: 1.1;
    }

    /* QR Body & Central Logo Styling */
    .qr-body {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .qr-wrapper {
        position: relative;
        display: inline-block;
        margin: 5px 0;
    }
    .qr-code-img {
        width: 100%;
        max-width: 240px;
        height: auto;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .qr-logo-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50px;
        height: 50px;
        background: #ffffff;
        border-radius: 50%;
        padding: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2.5px solid #206bc4;
        z-index: 10;
    }
    .qr-logo-overlay img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }

    .qr-footer {
        margin-top: 20px;
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
    }
    .class-code {
        margin-top: 15px;
        border-top: 2px dashed #cbd5e1;
        padding-top: 15px;
        color: #1e293b;
        font-size: 20px;
    }

    @media print {
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .page-header, .navbar, .footer, .btn-list, .d-print-none {
            display: none !important;
        }
        .page-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .qr-print-container {
            padding: 0.5cm !important;
            display: block !important;
        }
        .qr-card {
            box-shadow: none !important;
            margin: auto !important;
            width: 14cm !important;
            border-width: 0.8cm !important;
            padding: 1.2cm 1cm !important;
            border-radius: 0 !important;
        }
        .logo-top img {
            height: 1.8cm !important;
            margin-bottom: 0.3cm !important;
        }
        .class-name {
            font-size: 1.1cm !important;
            margin-bottom: 0.6cm !important;
            white-space: nowrap !important;
        }
        .qr-wrapper {
            display: inline-block !important;
            position: relative !important;
        }
        .qr-code-img {
            max-width: 8cm !important;
            padding: 8px !important;
            border-radius: 10px !important;
        }
        .qr-logo-overlay {
            width: 1.7cm !important;
            height: 1.7cm !important;
            padding: 3px !important;
            border: 3px solid #206bc4 !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            background: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            z-index: 100 !important;
        }
        .school-header {
            font-size: 0.45cm !important;
            margin-bottom: 0.4cm !important;
        }
        .qr-footer {
            font-size: 0.35cm !important;
            margin-top: 0.8cm !important;
        }
        .class-code {
            font-size: 0.8cm !important;
            margin-top: 0.8cm !important;
            padding-top: 0.6cm !important;
        }
        @page {
            margin: 1cm;
            size: auto;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const cardEl = document.getElementById('qr-card-element');
        const btn = document.getElementById('btn-download-pdf');
        const origText = btn.innerHTML;

        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses PDF...`;
        btn.disabled = true;

        try {
            const canvas = await html2canvas(cardEl, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false
            });

            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();

            const margin = 20;
            const maxWidth = pdfWidth - (margin * 2);
            const maxHeight = pdfHeight - (margin * 2);

            let imgWidth = maxWidth;
            let imgHeight = (imgProps.height * imgWidth) / imgProps.width;

            if (imgHeight > maxHeight) {
                imgHeight = maxHeight;
                imgWidth = (imgProps.width * imgHeight) / imgProps.height;
            }

            const xPos = (pdfWidth - imgWidth) / 2;
            const yPos = (pdfHeight - imgHeight) / 2;

            pdf.addImage(imgData, 'PNG', xPos, yPos, imgWidth, imgHeight);
            pdf.save("QR_Kelas_{{ Str::slug($kelas->nama_kelas) }}_{{ $kelas->kode_kelas }}.pdf");
        } catch (err) {
            console.error('Error generating PDF:', err);
            alert('Gagal membuat file PDF. Silakan coba lagi.');
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }
</script>
@endsection



