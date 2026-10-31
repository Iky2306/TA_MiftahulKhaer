@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/guru/jurnal" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Pindai QR Kelas</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section" style="margin-top: 70px; margin-bottom: 80px; padding: 0 10px;">
    <div class="card" style="border-radius: 15px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <div class="card-body text-center" style="padding: 15px;">
            <h3 style="color:#1e293b; font-size:16px; margin-bottom: 15px; font-weight:700;">Arahkan Kamera ke Kode QR Kelas</h3>
            
            <div id="qr-reader" style="width:100%; border-radius: 12px; overflow:hidden; border:2px dashed #cbd5e1; margin-bottom: 10px; background: #0f172a;"></div>
            
            <form id="scanForm" action="/guru/jurnal/scan-process" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="qr_token" id="qr_token">
            </form>

            <audio id="beep_sound">
                <source src="{{ asset('assets/sound/audio_scanner.mp3') }}" type="audio/mpeg">
            </audio>
            
            <div class="alert alert-warning d-flex align-items-center justify-content-center p-2 mt-3 mb-0" style="border-radius: 12px; font-size: 12px; font-weight: 500; text-align: center; word-break: break-word;">
                <ion-icon name="alert-circle-outline" style="font-size: 18px; margin-right: 6px; flex-shrink: 0;"></ion-icon>
                <span>Silakan izinkan akses kamera jika diminta</span>
            </div>
        </div>
    </div>
</div>

<style>
    #qr-reader {
        position: relative;
        width: 100% !important;
        max-width: 100% !important;
    }
    #qr-reader video, 
    #qr-reader canvas {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        border-radius: 10px;
        object-fit: cover !important;
    }
    #qr-reader__scan_region {
        background: transparent !important;
    }
    #qr-reader__scan_region img {
        display: none !important;
    }
</style>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode;
    let isScanning = true;

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;
        isScanning = false;

        const sound = document.getElementById('beep_sound');
        if (sound) sound.play();

        document.getElementById('qr_token').value = decodedText;

        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('scanForm').submit();
            }).catch(() => {
                document.getElementById('scanForm').submit();
            });
        } else {
            document.getElementById('scanForm').submit();
        }
    }

    function onScanFailure(error) {
        // Ignore scan failures during active scanning
    }

    document.addEventListener("DOMContentLoaded", function () {
        html5QrCode = new Html5Qrcode("qr-reader");

        // Ukuran QR Box yang fleksibel & adaptif dengan layar perangkat
        const qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
            let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
            let size = Math.floor(minEdge * 0.75);
            return {
                width: Math.min(size, 250),
                height: Math.min(size, 250)
            };
        };

        const config = { 
            fps: 10, 
            qrbox: qrboxFunction,
            aspectRatio: 1.0
        };

        // Langsung jalankan kamera belakang (environment) tanpa tombol izin manual
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.warn("Gagal membuka kamera belakang dengan facingMode, mencari dari daftar kamera device...", err);
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length > 0) {
                    const backCamera = cameras.find(cam => {
                        const label = cam.label.toLowerCase();
                        return label.includes('back') || label.includes('belakang') || label.includes('environment') || label.includes('rear');
                    }) || cameras[cameras.length - 1];

                    html5QrCode.start(
                        backCamera.id,
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(err2 => {
                        console.error("Gagal membuka kamera:", err2);
                    });
                }
            }).catch(err3 => {
                console.error("Akses kamera ditolak atau tidak ditemukan:", err3);
            });
        });
    });
</script>
@endsection

