@extends('layouts.presensi')

@section('header')
<style>
    .appHeader {
        background: linear-gradient(135deg, #092c9f 0%, #004aad 100%) !important;
    }
</style>
<!-- App Header -->
<div class="appHeader text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">SiGuru</div>
    <div class="right"></div>
</div>
<!-- * App Header -->

<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;
    }

    #map {
        height: 280px;
        border-radius: 12px;
        border: 2px solid #edebe9;
        margin-bottom: 90px;
    }

    .jam-digital-malasngoding {
        background-color: #27272783;
        position: absolute;
        top: 65px;
        right: 10px;
        z-index: 9999;
        width: 150px;
        border-radius: 10px;
        padding: 5px;
    }

    .jam-digital-malasngoding p {
        color: #fff;
        font-size: 16px;
        text-align: left;
        margin-top: 0;
        margin-bottom: 0;
    }
    
    .webcam-capture {
        position: relative;
    }
    
    .face-status {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 5px;
        z-index: 100;
        font-weight: bold;
        background-color: rgba(255, 193, 7, 0.7);
        color: white;
    }

    /* Disabled button styles */
    #takeabsen:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Tips bar */
    .face-tips {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: white;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12px;
        margin-top: 8px;
        display: none;
        align-items: center;
        gap: 8px;
        animation: slideDown 0.3s ease;
    }

    .face-tips.show {
        display: flex;
    }

    .face-tips .tip-icon {
        font-size: 18px;
        flex-shrink: 0;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Pulsing blue dot for live location */
    .pulsing-blue-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #1e74fd;
        border: 2px solid #ffffff;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.4);
        position: relative;
    }
    .pulsing-blue-dot::after {
        content: '';
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: rgba(30, 116, 253, 0.4);
        position: absolute;
        top: -10px;
        left: -10px;
        animation: pulse-dot-location 1.8s infinite ease-in-out;
    }
    @keyframes pulse-dot-location {
        0% { transform: scale(0.6); opacity: 1; }
        100% { transform: scale(1.6); opacity: 0; }
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
@endsection

@section('content')
<div class="row" style="margin-top: 60px">
    <div class="col">
        <input type="hidden" id="lokasi">
        <div class="webcam-capture"></div>
        <canvas id="faceOverlayCanvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 15px; pointer-events: none; z-index: 50;"></canvas>
        <div class="face-status">Memuat sistem verifikasi...</div>
        <div class="face-tips" id="faceTips">
            <span class="tip-icon">💡</span>
            <span id="tipText"></span>
        </div>
    </div>
</div>

<div class="jam-digital-malasngoding">
    <p>{{ $hariini }}</p>
    <p id="jam"></p>
    <p>{{ $jamkerja->nama_jam_kerja }}</p>
    <p style="font-size: 12px; font-weight: 600; color: #fbbf24; margin-top: 2px;">
        {{ date('H:i', strtotime($jamkerja->jam_masuk)) }} - {{ date('H:i', strtotime($jamkerja->jam_pulang)) }}
    </p>
</div>

<div class="row">
    <div class="col">
        @if ($cek > 0)
            <input type="hidden" id="jenis_absen" value="out">
            <button id="takeabsen" class="btn btn-danger btn-block" disabled>
                <ion-icon name="hourglass-outline"></ion-icon>
                <span class="btn-text">Memuat Sistem...</span>
            </button>
        @else
            <input type="hidden" id="jenis_absen" value="in">
            <button id="takeabsen" class="btn btn-primary btn-block" disabled>
                <ion-icon name="hourglass-outline"></ion-icon>
                <span class="btn-text">Memuat Sistem...</span>
            </button>
        @endif
    </div>
</div>

<div class="row mt-2">
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
<script>
    let statusFaceId = {{ $karyawan->status_face_id ?? 1 }};
    let masterDescriptor = {!! $karyawan->face_descriptor ?? 'null' !!};
    let takeabsenBtn = document.getElementById('takeabsen');
    let isProcessing = false;
    let jenis_absen = document.getElementById('jenis_absen').value;

    // 1. OPTIMASI SUPER CEPAT: Mulai download otak AI *segera* di background (Paralel)
    const MODEL_URL = 'https://cdn.jsdelivr.net/gh/vladmandic/face-api/model/';
    const modelsLoadPromise = Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
    ]);

    let faceVerified = (statusFaceId == 0);
    let locationFetched = false;
    let lastSuccessTime = 0;

    // Update button state dynamically based on BOTH face & GPS readiness
    function updateButtonState() {
        var btnIcon = takeabsenBtn.querySelector('ion-icon');
        var btnText = takeabsenBtn.querySelector('.btn-text');
        
        if (faceVerified && locationFetched) {
            takeabsenBtn.removeAttribute('disabled');
            if (btnIcon && btnText) {
                btnIcon.setAttribute('name', 'camera-outline');
                btnText.textContent = jenis_absen === 'out' ? 'Absen Pulang' : 'Absen Masuk';
            }
        } else {
            takeabsenBtn.setAttribute('disabled', 'disabled');
            if (btnIcon && btnText) {
                btnIcon.setAttribute('name', 'hourglass-outline');
                if (!locationFetched) {
                    btnText.textContent = 'Mendapatkan GPS...';
                } else if (!faceVerified) {
                    btnText.textContent = 'Memverifikasi Wajah...';
                } else {
                    btnText.textContent = 'Memuat Sistem...';
                }
            }
        }
    }

    async function initFaceMatching() {
        const faceStatus = document.querySelector('.face-status');
        
        if (statusFaceId == 0) {
            faceStatus.textContent = 'Verifikasi Face ID Dinonaktifkan';
            faceStatus.style.backgroundColor = 'rgba(13, 110, 253, 0.85)';
            faceVerified = true;
            updateButtonState();
            return;
        }

        if (!masterDescriptor) {
            faceStatus.textContent = 'Face ID belum terdaftar! Silakan daftarkan di menu Profil.';
            faceStatus.style.backgroundColor = 'rgba(220, 53, 69, 0.7)';
            takeabsenBtn.setAttribute('disabled', 'disabled');
            var btnIcon = takeabsenBtn.querySelector('ion-icon');
            var btnText = takeabsenBtn.querySelector('.btn-text');
            if (btnIcon && btnText) {
                btnIcon.setAttribute('name', 'ban-outline');
                btnText.textContent = 'Face ID Diperlukan';
            }
            return;
        }

        faceStatus.textContent = 'Memuat Sistem...';

        // 2. TUNGGU BERSAMAAN: Tunggu video siap DAN model AI selesai di-download
        const [, video] = await Promise.all([
            modelsLoadPromise,
            new Promise((resolve) => {
                const check = setInterval(() => {
                    const v = document.querySelector('.webcam-capture video');
                    if (v && !v.paused && v.readyState >= 2) {
                        clearInterval(check);
                        resolve(v);
                    }
                }, 100);
            })
        ]);

        // 3. WARM-UP (Sangat Penting!): Menghindari freeze saat deteksi pertama
        faceStatus.textContent = 'Menyiapkan deteksi wajah...';
        try {
            await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 }))
                .withFaceLandmarks()
                .withFaceDescriptor();
        } catch(e) { /* Abaikan error awal */ }

        // System ready - update button text
        updateButtonState();
        faceStatus.textContent = 'Mendeteksi wajah...';
        faceStatus.style.backgroundColor = 'rgba(255, 193, 7, 0.7)';

        // Setup face overlay canvas
        const faceCanvas = document.getElementById('faceOverlayCanvas');
        const faceCtx = faceCanvas.getContext('2d');

        // Progressive tips system
        const faceTips = document.getElementById('faceTips');
        const tipText = document.getElementById('tipText');
        let failCount = 0;
        let lastTipIndex = -1;
        const tips = [
            'Pastikan ruangan cukup terang',
            'Hadap ke kamera secara langsung',
            'Hindari posisi membelakangi cahaya (backlight)',
            'Coba mendekat sedikit ke kamera',
            'Lepaskan kacamata hitam atau masker jika ada',
            'Pastikan lensa kamera tidak terhalang atau kotor',
            'Coba berdiri di dekat sumber cahaya',
            'Jangan bergerak terlalu cepat saat deteksi'
        ];

        function showTip() {
            let tipIndex = Math.floor(failCount / 5) % tips.length;
            if (tipIndex !== lastTipIndex) {
                tipText.textContent = tips[tipIndex];
                faceTips.classList.remove('show');
                // Trigger re-animation
                void faceTips.offsetWidth;
                faceTips.classList.add('show');
                lastTipIndex = tipIndex;
            }
        }

        function hideTip() {
            faceTips.classList.remove('show');
            failCount = 0;
            lastTipIndex = -1;
        }

        // Start detection loop immediately
        const runDetection = async () => {
            if (isProcessing) return;
            isProcessing = true;

            // Sync canvas size with video
            if (video.videoWidth && video.videoHeight) {
                faceCanvas.width = video.videoWidth;
                faceCanvas.height = video.videoHeight;
            }
            faceCtx.clearRect(0, 0, faceCanvas.width, faceCanvas.height);

            try {
                const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detections) {
                    const box = detections.detection.box;
                    const distance = faceapi.euclideanDistance(detections.descriptor, masterDescriptor);
                    const isMatch = distance < 0.55;

                    // Draw corner brackets
                    faceCtx.strokeStyle = isMatch ? '#28a745' : '#dc3545';
                    faceCtx.lineWidth = 3;
                    const cornerLen = 25;
                    const x = box.x, y = box.y, w = box.width, h = box.height;

                    faceCtx.beginPath();
                    faceCtx.moveTo(x, y + cornerLen); faceCtx.lineTo(x, y); faceCtx.lineTo(x + cornerLen, y);
                    faceCtx.moveTo(x + w - cornerLen, y); faceCtx.lineTo(x + w, y); faceCtx.lineTo(x + w, y + cornerLen);
                    faceCtx.moveTo(x + w, y + h - cornerLen); faceCtx.lineTo(x + w, y + h); faceCtx.lineTo(x + w - cornerLen, y + h);
                    faceCtx.moveTo(x + cornerLen, y + h); faceCtx.lineTo(x, y + h); faceCtx.lineTo(x, y + h - cornerLen);
                    faceCtx.stroke();

                    if (isMatch) {
                        lastSuccessTime = Date.now();
                        faceStatus.textContent = '✓ Identitas Terverifikasi';
                        faceStatus.style.backgroundColor = 'rgba(40, 167, 69, 0.7)';
                        faceVerified = true;
                        updateButtonState();
                        hideTip();
                    } else {
                        // Only disable after a 1.5 second grace period to prevent flickering
                        if (Date.now() - lastSuccessTime > 1500) {
                            faceStatus.textContent = '⚠ Wajah Tidak Cocok';
                            faceStatus.style.backgroundColor = 'rgba(220, 53, 69, 0.7)';
                            faceVerified = false;
                            updateButtonState();
                            failCount++;
                            if (failCount >= 3) showTip();
                        }
                    }
                } else {
                    // Only disable after a 1.5 second grace period to prevent flickering
                    if (Date.now() - lastSuccessTime > 1500) {
                        faceStatus.textContent = 'Posisikan Wajah ke Kamera';
                        faceStatus.style.backgroundColor = 'rgba(255, 193, 7, 0.7)';
                        faceVerified = false;
                        updateButtonState();
                        failCount++;
                        if (failCount >= 3) showTip();
                    }
                }
            } catch(e) {
                console.log('Detection error:', e);
            }
            
            isProcessing = false;
            setTimeout(runDetection, 150);
        };

        // Start immediately
        runDetection();
    }

    // Inisialisasi Webcam
    Webcam.set({
        height: 480,
        width: 640,
        image_format: 'jpeg',
        jpeg_quality: 80
    });

    // Attach webcam dan mulai deteksi
    Webcam.attach('.webcam-capture');
    
    // Start face matching immediately
    initFaceMatching();

    // Kode untuk jam
    window.onload = function() {
        jam();
    }

    function jam() {
        var e = document.getElementById('jam'),
            d = new Date(),
            h, m, s;
        h = set(d.getHours());
        m = set(d.getMinutes());
        s = set(d.getSeconds());

        e.innerHTML = h + ':' + m + ':' + s;

        setTimeout('jam()', 1000);
    }

    function set(e) {
        e = e < 10 ? '0' + e : e;
        return e;
    }

    // Kode untuk map dan absen
    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    var radius_sound = document.getElementById('radius_sound');
    
    var map, marker, officeMarker, circle;

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // metres
        const phi1 = lat1 * Math.PI/180;
        const phi2 = lat2 * Math.PI/180;
        const deltaPhi = (lat2-lat1) * Math.PI/180;
        const deltaLambda = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(deltaPhi/2) * Math.sin(deltaPhi/2) +
                  Math.cos(phi1) * Math.cos(phi2) *
                  Math.sin(deltaLambda/2) * Math.sin(deltaLambda/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c; // in metres
    }

    function initMap() {
        map = L.map('map').setView([0, 0], 18);
        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        var lokasi_kantor = "{{ $lok_kantor->lokasi_cabang }}";
        var lok = lokasi_kantor.split(",");
        var lat_kantor = parseFloat(lok[0]);
        var long_kantor = parseFloat(lok[1]);
        var radius = "{{ $lok_kantor->radius_cabang }}";

        // Pulsing Blue Dot Icon for User Location
        var blueDotIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div class='pulsing-blue-dot'></div>",
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
        marker = L.marker([0, 0], { icon: blueDotIcon }).addTo(map);

        // Office Marker Pin
        officeMarker = L.marker([lat_kantor, long_kantor]).addTo(map);
        officeMarker.bindPopup("<b>Pusat Presensi Miftahul Khaer</b><br>Gedung Sekolah");

        // Geofence Circle Boundary
        circle = L.circle([lat_kantor, long_kantor], {
            color: '#ef4444',
            fillColor: '#ef4444',
            fillOpacity: 0.15,
            radius: parseFloat(radius)
        }).addTo(map);

        // Force Leaflet to re-calculate size to prevent partially rendered map tiles
        setTimeout(function() {
            if (map) {
                map.invalidateSize();
            }
        }, 500);
    }

    function updatePosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        
        var lokasiInput = document.getElementById('lokasi');
        if (lokasiInput) {
            lokasiInput.value = lat + "," + lng;
        }

        var lokasi_kantor = "{{ $lok_kantor->lokasi_cabang }}";
        var lok = lokasi_kantor.split(",");
        var lat_kantor = parseFloat(lok[0]);
        var long_kantor = parseFloat(lok[1]);
        var radius = parseFloat("{{ $lok_kantor->radius_cabang }}");

        // Compute distance
        var distance = calculateDistance(lat, lng, lat_kantor, long_kantor);
        var insideRadius = distance <= radius;

        // Set location as loaded and refresh button state
        locationFetched = true;
        updateButtonState();

        if (map) {
            // Force Leaflet recalculation to render full map container correctly
            map.invalidateSize();

            // Update User Position Marker
            marker.setLatLng([lat, lng]);
            marker.bindPopup("<b>Lokasi Anda</b><br>Jarak: " + Math.round(distance) + "m dari sekolah").openPopup();

            // Dynamic Geofence Visual Styling
            if (insideRadius) {
                circle.setStyle({
                    color: '#10b981',      // Emerald Green
                    fillColor: '#10b981',
                    fillOpacity: 0.25
                });
                circle.bindPopup("<b>Zona Presensi</b><br>Anda berada di dalam radius presensi! ✓");
            } else {
                circle.setStyle({
                    color: '#ef4444',      // Sleek Red
                    fillColor: '#ef4444',
                    fillOpacity: 0.15
                });
                circle.bindPopup("<b>Zona Presensi</b><br>Anda berada di luar radius presensi! (" + Math.round(distance - radius) + "m lagi)");
            }

            // Center the map precisely on the user's live position to keep the popup perfectly centered and not cut off at the top
            map.setView([lat, lng], 17);
        }
        console.log("Posisi diperbarui: " + lat + ", " + lng + " | Jarak: " + distance + "m");
    }

    function errorCallback(error) {
        console.log("Error: " + error.message);
    }

    if (navigator.geolocation) {
        initMap();
        navigator.geolocation.watchPosition(updatePosition, errorCallback, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    }

    // Event handler untuk tombol absen
    $("#takeabsen").click(function(e) {
        if ($(this).attr('disabled')) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Pastikan wajah dan bahu Anda terdeteksi dengan benar',
                icon: 'warning'
            });
            return;
        }

        // Nonaktifkan tombol sementara
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
        btn.prop('disabled', true);

        Webcam.snap(function(uri) {
            image = uri;
            var lokasi = $("#lokasi").val();
            var jenis_absen = $("#jenis_absen").val();

            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi,
                    kode_jam_kerja: "{{ $kode_jam_kerja }}",
                    jenis_absen: jenis_absen
                },
                cache: false,
                success: function(respond) {
                    var status = respond.split("|");
                    if (status[0] == "success") {
                        if (status[2] == "in") {
                            notifikasi_in.play();
                        } else {
                            notifikasi_out.play();
                        }
                        Swal.fire({
                            title: 'Berhasil !',
                            text: status[1],
                            icon: 'success'
                        })
                        setTimeout("location.href='/dashboard'", 3000);
                    } else {
                        // Aktifkan kembali tombol jika gagal
                        btn.html(originalText);
                        btn.prop('disabled', false);
                        
                        if (status[2] == "radius") {
                            radius_sound.play();
                        }
                        Swal.fire({
                            title: 'Gagal !',
                            text: status[1],
                            icon: 'error'
                        })
                    }
                },
                error: function() {
                    // Aktifkan kembali tombol jika error
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                        icon: 'error'
                    })
                }
            });
        });
    });
</script>
@endpush