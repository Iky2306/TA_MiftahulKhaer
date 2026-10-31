<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Akses Ditolak</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            max-width: 380px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .error-icon { font-size: 64px; margin-bottom: 15px; }
        .error-code { font-size: 48px; font-weight: 800; color: #1e293b; line-height: 1; }
        .error-title { font-size: 16px; color: #64748b; margin-top: 8px; font-weight: 500; }
        .error-message { font-size: 13px; color: #94a3b8; margin-top: 12px; line-height: 1.5; }
        .btn-group { margin-top: 25px; display: flex; gap: 10px; justify-content: center; }
        .btn {
            display: inline-flex; align-items: center; padding: 12px 24px;
            border-radius: 30px; font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all 0.2s; border: none; cursor: pointer;
        }
        .btn-primary { background: linear-gradient(135deg, #f59e0b, #ef4444); color: white; box-shadow: 0 4px 12px rgba(245,158,11,0.4); }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .countdown { font-size: 11px; color: #94a3b8; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <div class="error-message">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </div>
        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-secondary">← Kembali</a>
            <a href="/dashboard" class="btn btn-primary">🏠 Dashboard</a>
        </div>
        <div class="countdown">Otomatis kembali dalam <strong id="timer">5</strong> detik...</div>
    </div>
    <script>
        let sec = 5;
        const interval = setInterval(() => { sec--; document.getElementById('timer').textContent = sec; if (sec <= 0) { clearInterval(interval); history.back(); } }, 1000);
    </script>
</body>
</html>
