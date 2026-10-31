<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Guru {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 297mm;
            height: 210mm;
            margin: 0 auto;
            padding: 0;
            background: white;
            box-sizing: border-box;
            page-break-after: always;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* === GRID 5 kolom × 2 baris (10 slip per halaman) === */
        .slip-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 3mm;
            width: 287mm;
            height: 200mm;
        }

        .slip-gaji {
            border: 1px solid #2c3e50;
            border-radius: 6px;
            padding: 2mm;
            background: white;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 5pt;
            overflow: hidden;
        }

        .kop-laporan {
            text-align: center;
            border-bottom: 1px solid #3498db;
            margin-bottom: 1.2mm;
            padding-bottom: 0.8mm;
        }

        .kop-title {
            font-size: 6.5pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            line-height: 1.3;
        }

        .kop-subtitle {
            font-size: 5.5pt;
            font-weight: bold;
            color: #e74c3c;
            margin: 0.5mm 0;
            line-height: 1.3;
        }

        .kop-alamat {
            font-size: 4.2pt;
            color: #7f8c8d;
            margin: 0;
            line-height: 1.3;
        }

        .periode-box {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-align: center;
            padding: 1.2mm;
            font-size: 5.2pt;
            font-weight: bold;
            border-radius: 3px;
            margin-bottom: 1.5mm;
        }

        .employee-info {
            background: #ecf0f1;
            border-radius: 3px;
            border-left: 3px solid #3498db;
            padding: 1.2mm;
            margin-bottom: 1.5mm;
        }

        .info-item {
            display: flex;
            font-size: 4.8pt;
            margin-bottom: 0.6mm;
            line-height: 1.4;
        }

        .info-label {
            width: 13mm;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-value {
            flex: 1;
            color: #34495e;
            font-weight: bold;
        }

        .salary-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .section-header {
            color: white;
            text-align: center;
            padding: 0.6mm;
            font-size: 4.8pt;
            font-weight: bold;
            border-radius: 2px;
        }

        .earnings-section {
            background: #d5f4e6;
            border: 1px solid #27ae60;
            border-radius: 3px;
            padding: 1mm;
            margin-bottom: 1.5mm;
        }

        .deductions-section {
            background: #fadbd8;
            border: 1px solid #e74c3c;
            border-radius: 3px;
            padding: 1mm;
            margin-bottom: 1.5mm;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 4.5pt;
        }

        .salary-table td {
            padding: 0.5mm 0.7mm;
            border-bottom: 1px solid #bdc3c7;
            line-height: 1.5;
        }

        .salary-table .item-name {
            width: 65%;
            font-weight: bold;
        }

        .salary-table .amount {
            width: 35%;
            text-align: right;
            font-weight: bold;
        }

        .total-section {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border-radius: 3px;
            padding: 1.5mm;
            font-size: 5pt;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.7mm;
            line-height: 1.4;
        }

        .net-pay {
            font-size: 5.5pt;
            font-weight: bold;
            border-top: 1px solid #95a5a6;
            padding-top: 1mm;
            margin-top: 1mm;
        }

        .no-print {
            display: block;
        }

        @media print {
            body {
                background: none;
            }

            .page {
                box-shadow: none;
                margin: 0;
                padding: 0;
                page-break-after: always;
            }

            .no-print {
                display: none;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; padding:8px; background:white;">
        <button onclick="window.print()" style="padding:6px 14px; background:#3498db; color:white; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Cetak
        </button>
        <button onclick="downloadPDF()" style="padding:6px 14px; background:#27ae60; color:white; border:none; border-radius:4px; cursor:pointer;">
            📄 Download PDF
        </button>
    </div>

    @php
        function formatOrDash($v) {
            return ($v == null || $v === '' || $v == 0) ? '-' : number_format($v, 0, ',', '.');
        }
        function formatRupiahOrDash($v) {
            return ($v == null || $v === '' || $v == 0) ? '-' : 'Rp. '.number_format($v, 0, ',', '.');
        }

        // agar pas 10 per halaman
        $filledRekap = $rekap;
        $remainder = count($rekap) % 10;
        if ($remainder != 0) {
            $fillCount = 10 - $remainder;
            for ($i = 0; $i < $fillCount; $i++) {
                $filledRekap[] = (object)[
                    'nama_lengkap' => '',
                    'jabatan' => '',
                    'lokasi_sekolah' => '',
                    'hadir' => '',
                    'tjm' => '',
                    'gaji_pokok' => '',
                    'jml_gaji_pokok' => '',
                    'transport' => '',
                    'jml_transport' => '',
                    'tunjangan_jabatan' => '',
                    'dana_sosial' => '',
                    'simpanan_koperasi' => '',
                    'potongan' => '',
                    'total_gaji' => ''
                ];
            }
        }
        $chunks = array_chunk($filledRekap, 10);
    @endphp

    @foreach($chunks as $chunk)
    <div class="page">
        <div class="slip-container">
            @foreach($chunk as $gaji)
            <div class="slip-gaji" style="@if(empty($gaji->nama_lengkap)) visibility:hidden; @endif">
                @if(!empty($gaji->nama_lengkap))
                <div class="kop-laporan">
                    <div class="kop-title">PONDOK PESANTREN MIFTAHUL KHAER</div>
                    <div class="kop-subtitle">SLIP GAJI GURU</div>
                    <div class="kop-alamat">Jl. Diklat Pemda, RT. 02 / RW. 15, Babakan, Sukabakti, Curug</div>
                </div>

                <div class="periode-box">PERIODE GAJI: {{ strtoupper($namaBulan) }} {{ $tahun }}</div>

                <div class="employee-info">
                    <div class="info-item"><div class="info-label">Nama</div><div class="info-value">: {{ $gaji->nama_lengkap }}</div></div>
                    <div class="info-item"><div class="info-label">Jabatan</div><div class="info-value">: {{ $gaji->jabatan }}</div></div>
                    <div class="info-item"><div class="info-label">Unit</div><div class="info-value">: {{ $gaji->lokasi_sekolah }}</div></div>
                </div>

                <div class="salary-section">
                    <div class="earnings-section">
                        <div class="section-header" style="background-color:#27ae60;">PENDAPATAN</div>
                        <table class="salary-table">
                            <tr><td class="item-name">Gaji Pokok ({{ formatOrDash($gaji->tjm) }} x {{ formatOrDash($gaji->gaji_pokok) }})</td><td class="amount">{{ formatOrDash($gaji->jml_gaji_pokok) }}</td></tr>
                            <tr><td class="item-name">Kehadiran ({{ formatOrDash($gaji->hadir) }} x {{ formatOrDash($gaji->transport) }})</td><td class="amount">{{ formatOrDash($gaji->jml_transport) }}</td></tr>
                            <tr><td class="item-name">Tunjangan Jabatan</td><td class="amount">{{ formatOrDash($gaji->tunjangan_jabatan) }}</td></tr>
                            <tr><td class="item-name"><b>TOTAL PENDAPATAN</b></td><td class="amount"><b>{{ formatOrDash($gaji->jml_gaji_pokok + $gaji->jml_transport + $gaji->tunjangan_jabatan) }}</b></td></tr>
                        </table>
                    </div>

                    <div class="deductions-section">
                        <div class="section-header" style="background-color:#e74c3c;">POTONGAN</div>
                        <table class="salary-table">
                            <tr><td class="item-name">Dana Sosial</td><td class="amount">{{ formatOrDash($gaji->dana_sosial) }}</td></tr>
                            <tr><td class="item-name">Potongan Lain-lain</td><td class="amount">{{ formatOrDash($gaji->potongan) }}</td></tr>
                            <tr><td class="item-name"><b>TOTAL POTONGAN</b></td><td class="amount"><b>{{ formatOrDash($gaji->dana_sosial + $gaji->potongan) }}</b></td></tr>
                        </table>
                    </div>
                </div>

                <div class="total-section">
                    <div class="total-item"><span>Total Pendapatan:</span><span>{{ formatRupiahOrDash($gaji->jml_gaji_pokok + $gaji->jml_transport + $gaji->tunjangan_jabatan) }}</span></div>
                    <div class="total-item"><span>Total Potongan:</span><span>{{ formatRupiahOrDash($gaji->dana_sosial + $gaji->potongan) }}</span></div>
                    <div class="net-pay">
                        <div class="total-item"><span>GAJI BERSIH:</span><span>{{ formatRupiahOrDash($gaji->total_gaji) }}</span></div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const pages = document.querySelectorAll('.page');
            let pdf = null;

            for (let i = 0; i < pages.length; i++) {
                const canvas = await html2canvas(pages[i], { scale: 1.2, useCORS: true, backgroundColor: '#fff' });
                const imgData = canvas.toDataURL('image/png', 0.8);
                const pdfWidth = 297;
                const pdfHeight = 210;

                if (i === 0) pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                else pdf.addPage();

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            }

            const now = new Date();
            pdf.save(`Slip_Gaji_Guru_${now.getFullYear()}_${String(now.getMonth()+1).padStart(2,'0')}.pdf`);
        }
    </script>
</body>
</html>