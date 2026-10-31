<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --bg-dark: #0f172a;
            --text-main: #f8fafc;
            --text-sub: #94a3b8;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.15) 0%, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            overflow-x: hidden;
            padding: 1.5rem;
        }

        .maintenance-wrapper {
            max-width: 600px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 2.5rem;
            padding: 3.5rem 2.5rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .illustration {
            margin-bottom: 2.5rem;
            position: relative;
            display: inline-block;
        }

        .icon-circle {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 0 40px rgba(79, 70, 229, 0.4);
            animation: pulse 3s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(79, 70, 229, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 60px rgba(79, 70, 229, 0.6); }
        }

        .icon-circle svg {
            width: 56px;
            height: 56px;
            color: white;
        }

        .badge {
            display: inline-block;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-light);
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }

        h1 {
            font-size: clamp(2rem, 5vw, 2.75rem);
            font-weight: 700;
            margin-bottom: 1.25rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        p {
            color: var(--text-sub);
            font-size: 1.125rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: #fff;
            color: var(--bg-dark);
            padding: 1rem 2.5rem;
            border-radius: 1.25rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            background: var(--primary-light);
            color: #fff;
        }

        .footer {
            margin-top: 3rem;
            font-size: 0.875rem;
            color: var(--text-sub);
            opacity: 0.7;
        }

        /* Ambient elements */
        .ambient {
            position: absolute;
            background: var(--primary);
            border-radius: 50%;
            filter: blur(100px);
            z-index: 1;
            opacity: 0.2;
            pointer-events: none;
        }

        .amb-1 { width: 300px; height: 300px; top: -50px; left: -50px; }
        .amb-2 { width: 250px; height: 250px; bottom: -50px; right: -50px; background: var(--secondary); }

        @media (max-width: 480px) {
            .card {
                padding: 2.5rem 1.5rem;
                border-radius: 2rem;
            }
            .icon-circle {
                width: 100px;
                height: 100px;
            }
            .icon-circle svg {
                width: 48px;
                height: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="ambient amb-1"></div>
    <div class="ambient amb-2"></div>

    <div class="maintenance-wrapper">
        <div class="card">
            <div class="illustration">
                <div class="icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.67 2.67 0 1113.5 17.25l-5.83-5.83m5.83 5.83l3.07 3.07M9.75 13.5L3.92 7.67A2.67 2.67 0 117.67 3.92l5.83 5.83m-5.83-5.83V3.75c0-1.242.88-2.25 1.967-2.25h.142c1.087 0 1.967 1.008 1.967 2.25V5.25m-3.935 0c0 1.242.88 2.25 1.967 2.25h.142c1.087 0 1.967-1.008 1.967-2.25M6.75 20.25v.75m0-1.5v.75m0-1.5v.75M6.75 6.75v.75m0-1.5v.75m0-1.5v.75M17.25 17.25v.75m0-1.5v.75m0-1.5v.75M17.25 6.75v.75m0-1.5v.75m0-1.5v.75M12 12v.75m0-1.5v.75m0-1.5v.75" />
                    </svg>
                </div>
            </div>
            
            <div class="badge">System Update</div>
            <h1>Sedang Pemeliharaan</h1>
            
            <p>
                Mohon maaf, SiGuru sedang dalam proses pembaruan sistem dan backup data rutin untuk meningkatkan performa layanan bagi Anda.
            </p>

            <a href="javascript:location.reload()" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="display:inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Muat Ulang Halaman
            </a>

            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                <em>Silakan kembali dalam beberapa saat lagi.</em>
            </div>
        </div>
    </div>
</body>
</html>
