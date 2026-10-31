<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="{{ ($tema ?? 'default') == 'ramadan' ? '#1B5E20' : (($tema ?? 'default') == 'merdeka' ? '#B71C1C' : '#092c9f') }}">
    <title>SiGuru - Login Guru | Miftahul Khaer</title>
    <link rel="icon" type="image/png" href="{{ asset('icon-512.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon-512.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Dashboard standard colors fallback */
            --primary: #092c9f;
            --primary-light: #004AAD;
            --primary-fade: rgba(9, 44, 159, 0.08);
            
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --surface: #ffffff;
            
            --radius-md: 12px;
            --radius-lg: 24px;
            
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 25px -5px rgba(9, 44, 159, 0.15);
        }

        /* === Theme Classes === */
        body.theme-default {
            --primary: #092c9f;
            --primary-light: #004AAD;
            --primary-fade: rgba(9, 44, 159, 0.08);
            --shadow-md: 0 10px 25px -5px rgba(9, 44, 159, 0.15);
        }

        body.theme-ramadan {
            --primary: #1B5E20;
            --primary-light: #2E7D32;
            --primary-fade: rgba(27, 94, 32, 0.08);
            --shadow-md: 0 10px 25px -5px rgba(27, 94, 32, 0.15);
        }

        body.theme-merdeka {
            --primary: #B71C1C;
            --primary-light: #D32F2F;
            --primary-fade: rgba(183, 28, 28, 0.08);
            --shadow-md: 0 10px 25px -5px rgba(183, 28, 28, 0.15);
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ===========================================
           LAYOUT
        =========================================== */
        .layout-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ===========================================
           LEFT PANEL: BRAND & VISUAL
        =========================================== */
        .panel-brand {
            flex: 1.2;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
        }

        /* Geometric Accents */
        .panel-brand::before {
            content: '';
            position: absolute;
            top: -20%; left: -10%;
            width: 70%; height: 70%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            border-radius: 50%;
        }
        .panel-brand::after {
            content: '';
            position: absolute;
            bottom: -20%; right: -10%;
            width: 80%; height: 80%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand-content {
            position: relative;
            z-index: 10;
            max-width: 500px;
            animation: fadeIn 1s ease-out;
        }

        .brand-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 32px;
            background: white;
            padding: 12px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .brand-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.85);
            font-weight: 400;
        }

        /* ===========================================
           RIGHT PANEL: FORM
        =========================================== */
        .panel-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light); /* Soft gray background on desktop */
            padding: 40px;
            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 460px;
            background: var(--surface); /* White Card style on desktop */
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .form-header p {
            font-size: 1rem;
            color: var(--text-muted);
        }

        /* Alert */
        .alert-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fef2f2;
            border-left: 4px solid #e11d48;
            color: #be123c;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Form Inputs */
        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .input-control {
            width: 100%;
            height: 54px;
            background: var(--bg-light);
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0 48px;
            font-size: 0.95rem;
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .input-control:focus {
            background: var(--surface);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-fade);
            outline: none;
        }

        .input-control:focus ~ .input-icon {
            color: var(--primary);
        }

        .icon-toggle {
            position: absolute;
            right: 16px;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-toggle:hover {
            color: var(--primary);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            height: 54px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-light);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ===========================================
           MOBILE RESPONSIVE
        =========================================== */
        .mobile-brand {
            display: none;
        }

        @media (max-width: 992px) {
            .layout-wrapper {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }
            .panel-brand {
                display: none;
            }
            .panel-form {
                padding: 30px 20px;
                background: var(--bg-light);
                align-items: flex-start; /* Better for scrolling on mobile */
            }
            .form-container {
                background: var(--surface);
                padding: 32px 24px;
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-md);
                border: 1px solid var(--border-color);
                margin: auto; /* Centers when space is available */
                max-width: 100%;
            }
            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 24px;
                padding-bottom: 24px;
                border-bottom: 1px solid var(--border-color);
            }
            .mobile-brand img {
                width: 44px;
                height: 44px;
                background: var(--bg-light);
                padding: 6px;
                border-radius: 12px;
            }
            .mobile-brand h1 {
                font-size: 1.4rem;
                font-weight: 800;
                color: var(--primary);
                letter-spacing: -0.02em;
            }
            .form-header {
                margin-bottom: 28px;
            }
            .form-header h2 {
                font-size: 1.6rem;
                margin-bottom: 8px;
            }
            .form-header p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .panel-form {
                padding: 16px 12px;
                background: var(--surface); /* Merge background for cleaner look on small screens */
            }
            .form-container {
                padding: 24px 16px;
                border-radius: 16px;
                box-shadow: none; /* Flatten shadow for minimal mobile feel */
                border: none;
            }
            .input-control {
                height: 50px;
                font-size: 0.9rem;
            }
            .btn-submit {
                height: 50px;
                font-size: 0.95rem;
            }
            .form-header h2 {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 360px) {
            .form-container {
                padding: 16px 12px;
            }
            .input-label {
                font-size: 0.8rem;
            }
            .form-header h2 {
                font-size: 1.25rem;
            }
            .form-header p {
                font-size: 0.85rem;
            }
            .mobile-brand img {
                width: 38px;
                height: 38px;
            }
            .mobile-brand h1 {
                font-size: 1.25rem;
            }
        }
        
        /* Loading spinner animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="theme-{{ $tema ?? 'default' }}">

    <div class="layout-wrapper">
        
        <!-- Left Panel: Brand & Visual -->
        <div class="panel-brand">
            <div class="brand-content">
                <img src="{{ asset('assets/img/login/website.png') }}" alt="SiGuru" class="brand-logo">
                <h1 class="brand-title">Portal<br>SiGuru</h1>
                <p class="brand-subtitle">Platform terintegrasi untuk administrasi sekolah modern dan manajemen presensi guru di Sekolah Miftahul Khaer.</p>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="panel-form">
            <div class="form-container">
                
                <div class="mobile-brand">
                    <img src="{{ asset('assets/img/login/website.png') }}" alt="SiGuru">
                    <h1>SiGuru</h1>
                </div>

                <div class="form-header">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk ke akun Anda.</p>
                </div>

                @if (Session::get('warning'))
                    <div class="alert-box">
                        <ion-icon name="alert-circle-outline" style="font-size: 1.5rem;"></ion-icon>
                        <span>{{ Session::get('warning') }}</span>
                    </div>
                @endif

                <form action="/proseslogin" method="POST" id="loginForm">
                    @csrf

                    <div class="input-group">
                        <label class="input-label" for="nik">Nama Pengguna</label>
                        <div class="input-wrapper">
                            <ion-icon name="person-outline" class="input-icon"></ion-icon>
                            <input type="text" name="nik" id="nik" class="input-control" placeholder="Masukkan nama pengguna" required maxlength="15" autocomplete="username">
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="password">Kata Sandi</label>
                        <div class="input-wrapper">
                            <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                            <input type="password" name="password" id="password" class="input-control" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                            <div class="icon-toggle" id="toggle-pw" role="button" aria-label="Tampilkan kata sandi">
                                <ion-icon name="eye-off-outline"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <span>Masuk</span>
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </button>
                </form>

                <div class="footer">
                    &copy; {{ date('Y') }} Miftahul Khaer School.
                </div>
            </div>
        </div>

    </div>

    <!-- JS -->
    <script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        $(function() {
            // Password Visibility Toggle
            $('#toggle-pw').on('click', function() {
                const input = $('#password');
                const icon = $(this).find('ion-icon');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.attr('name', 'eye-outline');
                } else {
                    input.attr('type', 'password');
                    icon.attr('name', 'eye-off-outline');
                }
            });

            // Submit Loading State
            $('#loginForm').on('submit', function() {
                const btn = $('#btnSubmit');
                btn.prop('disabled', true);
                btn.html('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> <span>Memproses...</span>');
                btn.css({ opacity: '0.85', cursor: 'not-allowed' });
            });
        });
    </script>
</body>

</html>
