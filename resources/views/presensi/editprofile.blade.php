@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profil</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
@endsection

@section('content')
<style>
    /* === Profile Page Professional Styles === */
    .profile-wrapper {
        margin-top: 60px;
        padding: 0 0 100px 0;
        background: #f4f6f9;
    }

    /* --- Avatar Hero Section --- */
    .profile-hero {
        background: #ffffff;
        padding: 25px 20px 25px;
        text-align: center;
        position: relative;
        border-radius: 0 0 25px 25px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid #e2e8f0;
    }

    .profile-hero::before,
    .profile-hero::after {
        display: none;
    }

    .avatar-wrapper {
        position: relative;
        width: 90px;
        height: 90px;
        margin: 0 auto 12px;
        cursor: pointer;
        z-index: 2;
    }

    .avatar-wrapper img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .avatar-wrapper:hover img {
        transform: scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .avatar-camera {
        position: absolute;
        bottom: 0px;
        right: -5px;
        width: 30px;
        height: 30px;
        background: #f1f5f9;
        border: 2px solid #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--theme-primary, #092c9f);
        font-size: 16px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
        transition: all 0.3s ease;
        z-index: 3;
    }

    .avatar-camera:hover {
        background: #e2e8f0;
        color: var(--theme-primary-light, #004aad);
    }

    .avatar-wrapper input[type="file"] {
        display: none;
    }

    .profile-hero .profile-name {
        color: #1e293b;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.3px;
        z-index: 2;
        position: relative;
        line-height: 1.3;
        padding: 0 15px;
    }

    .profile-hero .profile-subtitle {
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        margin-top: 3px;
        z-index: 2;
        position: relative;
    }

    .profile-hero .profile-file-info {
        color: #475569;
        font-size: 12px;
        margin-top: 10px;
        display: none;
        z-index: 2;
        position: relative;
    }

    .profile-hero .profile-file-info.show {
        display: block;
        animation: fadeSlideUp 0.3s ease;
    }

    /* --- Cards Section --- */
    .profile-cards {
        padding: 24px 16px 0;
    }

    .profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 0;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
        overflow: hidden;
        animation: fadeSlideUp 0.5s ease both;
    }

    .profile-card:nth-child(2) {
        animation-delay: 0.1s;
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 20px 10px;
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
        border-bottom: 1px solid #f4f6f9;
        margin-bottom: 10px;
    }

    .profile-card-header ion-icon {
        font-size: 18px;
        color: #2a5298;
    }

    .profile-card-body {
        padding: 0 20px 20px;
    }

    /* --- Modern Input Fields --- */
    .modern-input-group {
        position: relative;
        margin-bottom: 18px;
    }

    .modern-input-group:last-child {
        margin-bottom: 0;
    }

    .modern-input-group .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #95a5a6;
        transition: color 0.3s ease;
        z-index: 2;
        pointer-events: none;
    }

    .modern-input-group input {
        width: 100%;
        padding: 12px 12px 12px 40px;
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        font-size: 14px;
        color: #2c3e50;
        background: #fdfdfe;
        transition: all 0.3s ease;
        outline: none;
        box-sizing: border-box;
    }

    .modern-input-group input:focus {
        border-color: #2a5298;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .modern-input-group input:focus ~ .input-icon {
        color: #2a5298;
    }

    .modern-input-group input::placeholder {
        color: #bdc3c7;
        font-weight: 400;
    }

    .modern-input-group .input-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #7f8c8d;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- Password Toggle --- */
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #95a5a6;
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
        transition: color 0.3s ease;
        z-index: 2;
        display: flex;
        align-items: center;
    }

    .password-toggle:hover {
        color: #2a5298;
    }

    .password-field-wrapper {
        position: relative;
    }

    .password-field-wrapper input {
        padding-right: 44px;
    }

    /* --- Submit Button --- */
    .profile-submit-wrapper {
        padding: 10px 16px 0;
    }

    .btn-profile-update {
        width: 100%;
        padding: 14px 24px;
        border: none;
        border-radius: 8px;
        background: #2a5298;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(42, 82, 152, 0.2);
        letter-spacing: 0.3px;
    }

    .btn-profile-update:hover {
        background: #1e3c72;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(42, 82, 152, 0.3);
    }

    .btn-profile-update:active {
        transform: translateY(1px);
        box-shadow: 0 2px 5px rgba(42, 82, 152, 0.2);
    }

    .btn-profile-update ion-icon {
        font-size: 20px;
    }

    /* --- Toast Alert --- */
    .profile-toast {
        padding: 12px 16px;
        border-radius: 12px;
        margin: 0 16px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        animation: fadeSlideDown 0.4s ease;
        position: relative;
        top: 66px;
    }

    .profile-toast.toast-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .profile-toast.toast-error {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .profile-toast.toast-warning {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .profile-toast ion-icon {
        font-size: 22px;
        flex-shrink: 0;
    }

    /* --- Animations --- */
    @keyframes fadeSlideUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeSlideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* --- Password Hint --- */
    .password-hint {
        font-size: 11px;
        color: #b2bec3;
        margin-top: 6px;
        padding-left: 2px;
    }

    /* --- File selected badge --- */
    .file-selected-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        color: #fff;
        margin-top: 6px;
        backdrop-filter: blur(4px);
    }
</style>

@php
    $messagesuccess = Session::get('success');
    $messageerror = Session::get('error');
@endphp

{{-- Toast Notifications --}}
@if (Session::get('success'))
    <div class="profile-toast toast-success">
        <ion-icon name="checkmark-circle"></ion-icon>
        <span>{{ $messagesuccess }}</span>
    </div>
@endif
@if (Session::get('error'))
    <div class="profile-toast toast-error">
        <ion-icon name="close-circle"></ion-icon>
        <span>{{ $messageerror }}</span>
    </div>
@endif
@error('foto')
    <div class="profile-toast toast-warning">
        <ion-icon name="warning"></ion-icon>
        <span>{{ $message }}</span>
    </div>
@enderror

<form action="/presensi/{{ $karyawan->nik }}/updateprofile" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="profile-wrapper">

        {{-- Avatar Hero Section --}}
        <div class="profile-hero">
            <div class="avatar-wrapper" onclick="document.getElementById('avatarUpload').click()">
                @if (!empty($karyawan->foto))
                    @php
                        $path = Storage::url('uploads/karyawan/' . $karyawan->foto);
                        $fullPath = storage_path('app/public/uploads/karyawan/' . $karyawan->foto);
                        $cacheBust = file_exists($fullPath) ? filemtime($fullPath) : time();
                    @endphp
                    <img src="{{ url($path) }}?v={{ $cacheBust }}" alt="Foto Profil" id="avatarPreview">
                @else
                    <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="Foto Profil" id="avatarPreview">
                @endif
                <div class="avatar-camera">
                    <ion-icon name="camera"></ion-icon>
                </div>
                <input type="file" name="foto" id="avatarUpload" accept=".png, .jpg, .jpeg">
            </div>
            <h3 class="profile-name">{{ $karyawan->nama_lengkap }}</h3>
            <div class="profile-subtitle">{{ $karyawan->jabatan ?? 'Not Set' }}</div>
            <div class="profile-file-info" id="fileInfo">
                <span class="file-selected-badge">
                    <ion-icon name="image-outline"></ion-icon>
                    <span id="fileName"></span>
                </span>
            </div>
        </div>

        {{-- Informasi Pribadi Card --}}
        <div class="profile-cards">
            <div class="profile-card">
                <div class="profile-card-header">
                    <ion-icon name="person-circle-outline"></ion-icon>
                    Personal Information
                </div>
                <div class="profile-card-body">
                    <div class="modern-input-group">
                        <label class="input-label">Full Name</label>
                        <div style="position: relative;">
                            <ion-icon name="person-outline" class="input-icon"></ion-icon>
                            <input type="text" value="{{ $karyawan->nama_lengkap }}" name="nama_lengkap"
                                   placeholder="Enter full name" autocomplete="off" maxlength="50">
                        </div>
                    </div>
                    <div class="modern-input-group">
                        <label class="input-label">Phone Number</label>
                        <div style="position: relative;">
                            <ion-icon name="call-outline" class="input-icon"></ion-icon>
                            <input type="text" value="{{ $karyawan->no_hp }}" name="no_hp"
                                   placeholder="Enter phone number" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keamanan Card --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                    Security
                </div>
                <div class="profile-card-body">
                    <div class="modern-input-group">
                        <label class="input-label">New Password</label>
                        <div class="password-field-wrapper" style="position: relative;">
                            <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                            <input type="password" name="password" id="passwordInput"
                                   placeholder="Enter new password" autocomplete="off">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <ion-icon name="eye-outline" id="toggleIcon"></ion-icon>
                            </button>
                        </div>
                        <div class="password-hint">Leave blank to keep current password</div>
                    </div>
                </div>
            </div>
            {{-- Card Face ID --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <ion-icon name="scan-outline"></ion-icon>
                    Face ID Attendance
                </div>
                <div class="profile-card-body">
                    <div id="face-id-status" class="alert alert-info" style="font-size: 13px;">
                        @if(!empty($karyawan->face_descriptor))
                            <ion-icon name="checkmark-circle"></ion-icon> Face ID Registered 
                            <small class="d-block text-muted">Last updated: {{ date('d M Y', strtotime($karyawan->face_updated_at)) }}</small>
                        @else
                            <ion-icon name="warning"></ion-icon> Face Not Registered
                        @endif
                    </div>
                    
                    <!-- Camera View -->
                    <div id="webcam-container" style="display:none; margin-bottom: 15px; position: relative;">
                        <video id="video" width="100%" height="auto" autoplay muted playsinline style="border-radius: 15px; background: #000;"></video>
                        <canvas id="faceOverlayCanvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 15px; pointer-events: none;"></canvas>
                        <div id="face-detect-status" style="position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; color: white; background: rgba(255,193,7,0.8); z-index: 10;">
                            Detecting face...
                        </div>
                    </div>

                    <!-- Preview View -->
                    <div id="preview-container" style="display:none; margin-bottom: 15px;">
                        <p style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; text-align: center;">Face Capture Result:</p>
                        <canvas id="previewCanvas" style="width: 100%; border-radius: 15px; border: 3px solid #28a745; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></canvas>
                        <p style="font-size: 12px; color: #666; text-align: center; margin-top: 8px;">Ensure face is clearly visible and well-lit</p>
                    </div>

                    <!-- Buttons -->
                    <button type="button" id="btn-start-scan" class="btn btn-outline-primary btn-block">
                        <ion-icon name="camera-outline"></ion-icon>
                        {{ !empty($karyawan->face_descriptor) ? 'Update Face ID' : 'Register Face' }}
                    </button>
                    
                    <button type="button" id="btn-capture-face" class="btn btn-success btn-block" style="display:none;">
                        <ion-icon name="scan-outline"></ion-icon>
                        Capture Face
                    </button>

                    <div id="preview-buttons" style="display:none;">
                        <button type="button" id="btn-save-face" class="btn btn-success btn-block" style="margin-bottom: 8px;">
                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                            Save Face ID
                        </button>
                        <button type="button" id="btn-retake-face" class="btn btn-outline-secondary btn-block">
                            <ion-icon name="refresh-outline"></ion-icon>
                            Retake
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="profile-submit-wrapper">
            <button type="submit" class="btn-profile-update">
                <ion-icon name="checkmark-done-outline"></ion-icon>
                Update Profile
            </button>
        </div>

    </div>
</form>

<script>
    // File upload preview
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileInfo').classList.add('show');
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Password toggle
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('name', 'eye-off-outline');
        } else {
            input.type = 'password';
            icon.setAttribute('name', 'eye-outline');
        }
    }

    // Auto-hide toast after 4 seconds
    document.querySelectorAll('.profile-toast').forEach(function(toast) {
        setTimeout(function() {
            toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                toast.remove();
            }, 500);
        }, 4000);
    });

    // ========== Face ID Logic ==========
    const btnStartScan = document.getElementById('btn-start-scan');
    const btnCaptureFace = document.getElementById('btn-capture-face');
    const btnSaveFace = document.getElementById('btn-save-face');
    const btnRetakeFace = document.getElementById('btn-retake-face');
    const webcamContainer = document.getElementById('webcam-container');
    const previewContainer = document.getElementById('preview-container');
    const previewButtons = document.getElementById('preview-buttons');
    const video = document.getElementById('video');
    const faceOverlayCanvas = document.getElementById('faceOverlayCanvas');
    const previewCanvas = document.getElementById('previewCanvas');
    const faceDetectStatus = document.getElementById('face-detect-status');
    let modelsLoaded = false;
    let detectionInterval = null;
    let capturedDescriptor = null;
    let cameraStream = null;

    async function loadModels() {
        if (modelsLoaded) return;
        try {
            const MODEL_URL = 'https://cdn.jsdelivr.net/gh/vladmandic/face-api/model/';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            modelsLoaded = true;
        } catch (e) {
            console.error('Model load error', e);
            Swal.fire('Error', 'Gagal memuat sistem deteksi wajah', 'error');
        }
    }

    // Draw face detection overlay on canvas
    function startFaceOverlay() {
        const ctx = faceOverlayCanvas.getContext('2d');
        
        detectionInterval = setInterval(async () => {
            if (video.paused || video.ended) return;

            // Match canvas size to video display size
            faceOverlayCanvas.width = video.videoWidth;
            faceOverlayCanvas.height = video.videoHeight;

            ctx.clearRect(0, 0, faceOverlayCanvas.width, faceOverlayCanvas.height);

            const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 }))
                .withFaceLandmarks();

            if (detection) {
                const box = detection.detection.box;
                
                // Draw rounded rectangle around face
                ctx.strokeStyle = '#28a745';
                ctx.lineWidth = 3;
                ctx.setLineDash([]);
                
                // Draw corner brackets instead of full box for modern look
                const cornerLen = 20;
                const x = box.x, y = box.y, w = box.width, h = box.height;
                
                ctx.beginPath();
                // Top-left
                ctx.moveTo(x, y + cornerLen); ctx.lineTo(x, y); ctx.lineTo(x + cornerLen, y);
                // Top-right
                ctx.moveTo(x + w - cornerLen, y); ctx.lineTo(x + w, y); ctx.lineTo(x + w, y + cornerLen);
                // Bottom-right
                ctx.moveTo(x + w, y + h - cornerLen); ctx.lineTo(x + w, y + h); ctx.lineTo(x + w - cornerLen, y + h);
                // Bottom-left
                ctx.moveTo(x + cornerLen, y + h); ctx.lineTo(x, y + h); ctx.lineTo(x, y + h - cornerLen);
                ctx.stroke();

                faceDetectStatus.textContent = '✓ Wajah Terdeteksi';
                faceDetectStatus.style.background = 'rgba(40, 167, 69, 0.8)';
                btnCaptureFace.disabled = false;
            } else {
                faceDetectStatus.textContent = 'Posisikan wajah ke kamera';
                faceDetectStatus.style.background = 'rgba(255, 193, 7, 0.8)';
                btnCaptureFace.disabled = true;
            }
        }, 200);
    }

    function stopFaceOverlay() {
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }

    // Start Scan button
    btnStartScan.addEventListener('click', async () => {
        btnStartScan.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat kamera...';
        btnStartScan.disabled = true;

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } });
            video.srcObject = cameraStream;
            await video.play();

            // Show camera immediately
            webcamContainer.style.display = 'block';
            btnStartScan.style.display = 'none';
            btnCaptureFace.style.display = 'block';
            btnCaptureFace.disabled = true;
            btnCaptureFace.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat model...';

            // Load models after camera is visible
            await loadModels();

            btnCaptureFace.innerHTML = '<ion-icon name="scan-outline"></ion-icon> Ambil Wajah';
            startFaceOverlay();
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Gagal mengakses kamera. Pastikan izin kamera diaktifkan.', 'error');
            btnStartScan.innerHTML = '<ion-icon name="camera-outline"></ion-icon> Daftarkan Wajah';
            btnStartScan.disabled = false;
        }
    });

    // Capture button - take snapshot and show preview
    btnCaptureFace.addEventListener('click', async () => {
        btnCaptureFace.disabled = true;
        btnCaptureFace.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengambil wajah...';

        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 }))
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detection) {
            capturedDescriptor = Array.from(detection.descriptor);

            // Capture the current video frame to preview canvas
            previewCanvas.width = video.videoWidth;
            previewCanvas.height = video.videoHeight;
            const ctx = previewCanvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            // Draw face box on preview
            const box = detection.detection.box;
            ctx.strokeStyle = '#28a745';
            ctx.lineWidth = 3;
            const cornerLen = 20;
            const x = box.x, y = box.y, w = box.width, h = box.height;
            ctx.beginPath();
            ctx.moveTo(x, y + cornerLen); ctx.lineTo(x, y); ctx.lineTo(x + cornerLen, y);
            ctx.moveTo(x + w - cornerLen, y); ctx.lineTo(x + w, y); ctx.lineTo(x + w, y + cornerLen);
            ctx.moveTo(x + w, y + h - cornerLen); ctx.lineTo(x + w, y + h); ctx.lineTo(x + w - cornerLen, y + h);
            ctx.moveTo(x + cornerLen, y + h); ctx.lineTo(x, y + h); ctx.lineTo(x, y + h - cornerLen);
            ctx.stroke();

            // Stop camera & overlay, show preview
            stopFaceOverlay();
            stopCamera();
            webcamContainer.style.display = 'none';
            btnCaptureFace.style.display = 'none';
            previewContainer.style.display = 'block';
            previewButtons.style.display = 'block';
        } else {
            Swal.fire('Peringatan', 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup dan wajah terlihat jelas.', 'warning');
            btnCaptureFace.disabled = false;
            btnCaptureFace.innerHTML = '<ion-icon name="scan-outline"></ion-icon> Ambil Wajah';
        }
    });

    // Save button - confirm and send to server
    btnSaveFace.addEventListener('click', async () => {
        if (!capturedDescriptor) return;

        btnSaveFace.disabled = true;
        btnSaveFace.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
        btnRetakeFace.disabled = true;

        $.ajax({
            type: 'POST',
            url: '/presensi/storeface',
            data: {
                _token: "{{ csrf_token() }}",
                descriptor: JSON.stringify(capturedDescriptor)
            },
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                    btnSaveFace.disabled = false;
                    btnSaveFace.innerHTML = '<ion-icon name="checkmark-circle-outline"></ion-icon> Simpan Face ID';
                    btnRetakeFace.disabled = false;
                }
            },
            error: function() {
                Swal.fire('Error', 'Koneksi bermasalah', 'error');
                btnSaveFace.disabled = false;
                btnSaveFace.innerHTML = '<ion-icon name="checkmark-circle-outline"></ion-icon> Simpan Face ID';
                btnRetakeFace.disabled = false;
            }
        });
    });

    // Retake button - go back to camera
    btnRetakeFace.addEventListener('click', async () => {
        previewContainer.style.display = 'none';
        previewButtons.style.display = 'none';
        capturedDescriptor = null;

        // Restart camera
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } });
            video.srcObject = cameraStream;
            await video.play();

            webcamContainer.style.display = 'block';
            btnCaptureFace.style.display = 'block';
            btnCaptureFace.disabled = true;
            btnCaptureFace.innerHTML = '<ion-icon name="scan-outline"></ion-icon> Ambil Wajah';
            startFaceOverlay();
        } catch (err) {
            Swal.fire('Error', 'Gagal mengakses kamera', 'error');
            btnStartScan.style.display = 'block';
            btnStartScan.disabled = false;
            btnStartScan.innerHTML = '<ion-icon name="camera-outline"></ion-icon> Daftarkan Wajah';
        }
    });
</script>
@endsection

