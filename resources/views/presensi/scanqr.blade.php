@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">SiGuru</div>
    <div class="right"></div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="row" style="margin-top: 60px; position: relative;">
    <div class="col-12">
        <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); background: #000;">
            <video id="qr-video" style="width: 100%; display: block;"></video>
            
            <!-- Floating Controls -->
            <div style="position: absolute; bottom: 20px; left: 0; right: 0; display: flex; justify-content: center; gap: 20px; z-index: 10;">
                <button id="switch-camera-btn" class="btn btn-icon btn-light shadow-lg" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none;">
                    <ion-icon name="camera-reverse-outline" style="font-size: 24px; color: #1e3a8a;"></ion-icon>
                </button>
                <button id="flash-toggle" class="btn btn-icon btn-light shadow-lg" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; display: none;">
                    <ion-icon name="flashlight-outline" id="flash-icon" style="font-size: 24px; color: #1e3a8a;"></ion-icon>
                </button>
            </div>
            
            <!-- Scan Overlay -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; border: 2px solid rgba(255,255,255,0.3); pointer-events: none;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 250px; height: 250px; border: 2px solid #3b82f6; border-radius: 20px; box-shadow: 0 0 0 1000px rgba(0,0,0,0.5);"></div>
            </div>
        </div>
        <h4 id="cam-qr-result" class="text-center mt-2" style="font-size: 14px; color: #64748b; font-weight: 500;">Mencari QR Code...</h4>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group d-none">
            <select id="cam-list" class="form-control">
                <option value="environment" selected>Auto</option>
            </select>
        </div>
        <div class="form-group">
            <input type="hidden" id="latitude">
            <input type="hidden" id="longitude">
            <input type="hidden" id="lokasi">
            <input type="hidden" id="status">
            <span id="cam-has-flash" style="visibility: hidden; display: none;"></span>
            <span id="flash-state" style="visibility: hidden; display: none;">off</span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
<audio id="notifikasi_in">
    <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
</audio>
<audio id="notifikasi_out">
    <source src="{{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
</audio>
<audio id="radius_sound">
    <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
</audio>
@endsection
@push('myscript')

<script src="{{ asset('assets/js/lib/qr-scanner.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/qr-scanner.legacy.min.js') }}"></script>
<script type="module">
    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    var radius_sound = document.getElementById('radius_sound');
    //import QrScanner from "../qr-scanner.min.js";
    var x = document.getElementById("demo");

    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + "," + position.coords.longitude;
        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
        var lokasi_kantor = "{{ $lok_kantor->lokasi_cabang }}";
        var lok = lokasi_kantor.split(",");
        var lat_kantor = lok[0];
        var long_kantor = lok[1];
        var radius = "{{ $lok_kantor->radius_cabang }}";
        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20
            , subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);
        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        var circle = L.circle([lat_kantor, long_kantor], {
            color: 'red'
            , fillColor: '#f03'
            , fillOpacity: 0.5
            , radius: radius
        }).addTo(map);
    }

    function errorCallback() {

    }

    const video = document.getElementById('qr-video');
    const videoContainer = document.getElementById('video-container');
    const camHasCamera = document.getElementById('cam-has-camera');
    const camList = document.getElementById('cam-list');
    const camHasFlash = document.getElementById('cam-has-flash');
    const flashToggle = document.getElementById('flash-toggle');
    const flashState = document.getElementById('flash-state');
    const camQrResult = document.getElementById('cam-qr-result');
    const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');
    const fileSelector = document.getElementById('file-selector');
    const fileQrResult = document.getElementById('file-qr-result');
    const statusResult = document.getElementById('status');



    function storeqr(qrcode){
        var lokasi = $("#lokasi").val();
        $.ajax({
            type: 'POST'
            , url: '/presensi/storeqr'
            , data: {
                _token: "{{ csrf_token() }}"
                , image: null
                , lokasi: lokasi
                ,qrcode:qrcode
            }
            , cache: false
            , success: function(respond) {
                var status = respond.split("|");
                console.log(status);
                if (status[0] == "success") {
                    if (status[2] == "in") {
                        notifikasi_in.play();
                    } else {
                        notifikasi_out.play();
                    }
                    Swal.fire({
                        title: 'Berhasil !'
                        , text: status[1]
                        , icon: 'success'
                    })
                    scanner.stop();
                    setTimeout("location.href='/dashboard'", 3000);
                } else {
                    if (status[2] == "radius") {
                        radius_sound.play();
                    }
                    Swal.fire({
                        title: 'Error !'
                        , text: status[1]
                        , icon: 'error'
                    })
                    startscanner();

                }
            }
        });
    }
    function setResult(label, result) {
        //console.log(label);
        console.log(result.data);
        label.textContent = result.data;
        // camQrResultTimestamp.textContent = new Date().toString();
        if(result.data != "No QR code found."){
            $("#status").val(1);
            if(statusResult!=1){
                storeqr(result.data);
            }
            scanner.stop();
        }
        label.style.color = 'red';
        // clearTimeout(label.highlightTimeout);
        // label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
    }

    const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
        onDecodeError: error => {
            camQrResult.textContent = error;
            camQrResult.style.color = 'inherit';
        },
        highlightScanRegion: true,
        highlightCodeOutline: true,
    });

    const updateFlashAvailability = () => {
        scanner.hasFlash().then(hasFlash => {
            camHasFlash.textContent = hasFlash;
            flashToggle.style.display = hasFlash ? 'inline-block' : 'none';

        });
    };


    const switchCameraBtn = document.getElementById('switch-camera-btn');
    var currentCameraIndex = 0;
    var availableCameras = [];

    function startscanner(){
        scanner.start().then(() => {
            updateFlashAvailability();
            
            QrScanner.listCameras(true).then(cameras => {
                availableCameras = cameras;
                // If only one camera, hide the switch button
                if (availableCameras.length <= 1) {
                    switchCameraBtn.style.display = 'none';
                }
            });
        });
    }

    // Camera Switching Logic
    switchCameraBtn.addEventListener('click', () => {
        if (availableCameras.length > 1) {
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            const nextCamera = availableCameras[currentCameraIndex];
            scanner.setCamera(nextCamera.id).then(updateFlashAvailability);
            
            // Subtle feedback
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'info',
                title: 'Kamera Diganti',
                showConfirmButton: false,
                timer: 1000
            });
        }
    });

    // QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);

    // for debugging
    window.scanner_start = startscanner();

    flashToggle.addEventListener('click', () => {
        scanner.toggleFlash().then(() => {
            const isOn = scanner.isFlashOn();
            flashState.textContent = isOn ? 'on' : 'off';
            const icon = document.getElementById('flash-icon');
            icon.style.color = isOn ? '#fbbf24' : '#1e3a8a';
            icon.setAttribute('name', isOn ? 'flashlight' : 'flashlight-outline');
        });
    });
</script>
@endpush