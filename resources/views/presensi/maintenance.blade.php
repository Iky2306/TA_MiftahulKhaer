@extends('layouts.presensi')

@section('header')
<style>
    /* === Professional Maintenance UI === */
    .appHeader {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;
        border-bottom: none !important;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2) !important;
    }

    .maintenance-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px 100px; /* Increased from 40px */
        min-height: 80vh;
        text-align: center;
    }

    /* --- Animated Illustration --- */
    .illus-wrapper {
        position: relative;
        width: 180px;
        height: 180px;
        margin-bottom: 30px;
        background: #f0f7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: float 4s ease-in-out infinite;
    }

    .illus-wrapper ion-icon {
        font-size: 100px;
        color: #3b82f6;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }

    /* --- Typography & Content --- */
    .headline {
        font-size: 20px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .sub-headline {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        max-width: 280px;
        margin: 0 auto 30px;
    }

    /* --- Modern Glass Card --- */
    .timer-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        width: 100%;
        max-width: 320px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        border: 1px solid rgba(241, 245, 249, 0.8);
    }

    .timer-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 1.5px;
        margin-bottom: 15px;
    }

    #countdown {
        font-family: 'Courier New', Courier, monospace;
        font-size: 18px;
        font-weight: 900;
        color: #3b82f6;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .unit-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 50px;
    }

    .unit-box span {
        font-size: 10px;
        color: #cbd5e1;
        margin-top: 4px;
    }
</style>

<!-- App Header -->
<div class="appHeader text-light">
    <div class="left">
        <a href="{{ url('/dashboard') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">SiGuru Status</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection

@section('content')
<div class="maintenance-container">
    {{-- Illustration --}}
    <div class="illus-wrapper">
        <ion-icon name="cafe-outline"></ion-icon>
    </div>

    {{-- Content --}}
    <div class="headline">Waduh, Servernya Lagi Ujian Juga Nih!</div>
    <p class="sub-headline">
        Tenang, fitur absen lagi sembunyi di balik kertas ujian <b>STS Genap 2026</b>. Nanti balik lagi kalau pengawasnya sudah lengah.
    </p>

    {{-- Tiny Info Badge --}}
    <div style="margin-bottom: 25px;">
        <span style="background: #fee2e2; color: #ef4444; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
            Status: Hibernasi Ujian
        </span>
    </div>

    {{-- Countdown Card --}}
    <div class="timer-card">
        <div class="timer-title">Buka Kembali Dalam:</div>
        <div id="countdown">
            <div class="unit-box"><span id="days">0</span><span>Hari</span></div>
            <div class="unit-box"><span id="hours">0</span><span>Jam</span></div>
            <div class="unit-box"><span id="minutes">0</span><span>Menit</span></div>
            <div class="unit-box"><span id="seconds">0</span><span>Detik</span></div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    // Target waktu: 11 April 2026 23:59:59
    var countDownDate = new Date("Apr 11, 2026 23:59:59").getTime();

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;

        if (distance < 0) {
            clearInterval(x);
             document.querySelector(".timer-card").innerHTML = 
                "<div style='color: #10b981; font-weight: 700; font-size: 14px;'>🎉 Ujian Selesai! Absen sudah bisa dibuka!</div>";
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;

    }, 1000);
</script>
@endpush
