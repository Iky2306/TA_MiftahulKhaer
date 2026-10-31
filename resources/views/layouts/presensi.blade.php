<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Dashboard</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/website.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/webiste.png">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css?v=20251016') }}">


    <style>
        /* === Dynamic Theme System: CSS Variables === */
        .theme-default {
            --theme-header-bg: linear-gradient(135deg, #092c9f 0%, #004aad 100%);
            --theme-primary: #092c9f;
            --theme-primary-dark: #072179;
            --theme-primary-light: #004aad;
            --theme-body-bg: #e9ecef;
            --theme-gradasi-green: linear-gradient(to bottom right, rgba(21, 189, 74, 1), rgba(21, 189, 74, 0.8));
            --theme-gradasi-red: linear-gradient(to bottom right, rgba(184, 13, 45, 1), rgba(184, 13, 45, 0.8));
            --theme-accent: #004aad;
            --theme-presence-bg: #e9ecef;
            --theme-nav-active: #092c9f;
        }

        .theme-ramadan {
            --theme-header-bg: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            --theme-primary: #1b5e20;
            --theme-primary-dark: #144618;
            --theme-primary-light: #2e7d32;
            --theme-body-bg: #f4f7f0;
            --theme-gradasi-green: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            --theme-gradasi-red: linear-gradient(135deg, #c62828 0%, #ef5350 100%);
            --theme-accent: #4caf50;
            --theme-presence-bg: #f4f7f0;
            --theme-nav-active: #1b5e20;
        }

        .theme-merdeka {
            --theme-header-bg: linear-gradient(135deg, #b71c1c 0%, #d32f2f 100%);
            --theme-primary: #b71c1c;
            --theme-primary-dark: #8e0000;
            --theme-primary-light: #d32f2f;
            --theme-body-bg: #fdf2f2;
            --theme-gradasi-green: linear-gradient(135deg, #c62828 0%, #e53935 100%);
            --theme-gradasi-red: linear-gradient(135deg, #bf360c 0%, #ff5722 100%);
            --theme-accent: #d32f2f;
            --theme-presence-bg: #fdf2f2;
            --theme-nav-active: #b71c1c;
        }

        /* --- Automatic Theme Element Bindings --- */
        .appHeader, div.appHeader, #user-section {
            background: var(--theme-header-bg) !important;
        }

        .bg-primary {
            background-color: var(--theme-primary) !important;
        }

        .btn-primary {
            background-color: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--theme-primary-light) !important;
            border-color: var(--theme-primary-light) !important;
        }

        .text-primary {
            color: var(--theme-primary) !important;
        }

        .badge-primary {
            background-color: var(--theme-primary) !important;
        }

        /* Bottom Menu Binding */
        .appBottomMenu .item.active ion-icon,
        .appBottomMenu .item.active strong,
        .appBottomMenu .item.active span {
            color: var(--theme-nav-active) !important;
        }

        .appBottomMenu .item.active .action-button,
        .appBottomMenu .item .action-button.large {
            background: var(--theme-primary) !important;
        }

        /* Section Titles & Accent Borders */
        .section-title, .section-title strong {
            color: var(--theme-primary);
        }

        /* Modern Loader Styles */
        #loader {
            position: fixed;
            inset: 0;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        .loading-bar {
            width: 200px;
            height: 4px;
            background: #f1f5f9;
            border-radius: 2px;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
        }

        .loading-bar::after {
            content: '';
            position: absolute;
            left: -50%;
            width: 50%;
            height: 100%;
            background: var(--theme-primary, #092c9f);
            animation: loading 1.5s infinite linear;
        }

        @keyframes loading {
            to { left: 100%; }
        }

        .selectmaterialize {
            display: block;
            background-color: transparent !important;
            border: 0px !important;
            border-bottom: 1px solid #9e9e9e !important;
            border-radius: 0 !important;
            outline: none !important;
            height: 3rem !important;
            width: 100% !important;
            font-size: 16px !important;
            margin: 0 0 8px 0 !important;
            padding: 0 !important;
            color: #495057;
        }

        .historicontent {
            display: flex;
            margin-top: 10px;
        }

        .datapresensi {
            margin-left: 10px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>

<body class="theme-{{ $tema ?? 'default' }}" style="background-color: var(--theme-body-bg, #e9ecef);">

    <!-- loader -->
    <div id="loader">
        <img src="{{ asset('assets/img/spinnerlogo.gif') }}" alt="SiGuru" style="width: 100px;">
        <div class="loading-bar"></div>
    </div>
    <!-- * loader -->

    @yield('header')

    <!-- App Capsule -->
    <div id="appCapsule">
        @yield('content')
    </div>
    <!-- * App Capsule -->


    @include('layouts.bottomNav')


    @include('layouts.script')



</body>

</html>
