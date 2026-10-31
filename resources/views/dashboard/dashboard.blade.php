@extends('layouts.presensi')
@section('content')
    <style>
        .logout {
            position: absolute;
            color: white;
            font-size: 30px;
            text-decoration: none;
            right: 8px;
        }

        .logout:hover {
            color: white;
        }
        
        #user-info {
            color: white;
        }

        #user-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .user-subinfo {
            font-size: 14px;
            display: block;
            line-height: 1.3;
        }

        .user-meta {
            margin-top: 8px;
        }

        @media (max-width: 576px) {
            .user-subinfo {
                font-size: 12px;
            }
        }

        .list-menu a {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .list-menu a:hover {
            transform: scale(1.1);
        }

        .list-menu {
            display: flex;
            justify-content: space-between;
        }
        .list-menu .item-menu {
            flex: 1;
            padding: 0 2px;
        }
        .list-menu .menu-name {
            font-size: 11px;
            line-height: 1.2;
            word-wrap: break-word;
            margin-top: 4px;
        }

        .primary {
            color: #2196F3;
        }

        .primary:hover {
            color: #0D47A1;
        }

        /* === Theme-Aware Overrides === */
        #user-section {
            background: var(--theme-header-bg, #092c9f) !important;
        }

        .gradasigreen {
            background-image: var(--theme-gradasi-green) !important;
        }

        .gradasired {
            background-image: var(--theme-gradasi-red) !important;
        }

        #presence-section {
            background-color: var(--theme-presence-bg, #e9ecef) !important;
        }

        /* Enhanced Chat Styles - 100% Responsive Mobile */
        .chat-container {
            position: fixed;
            bottom: 80px;
            right: 15px;
            left: 15px;
            width: auto;
            max-width: 360px;
            height: calc(100vh - 150px);
            max-height: 480px;
            margin-left: auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.22);
            display: flex;
            flex-direction: column;
            z-index: 10000;
            overflow: hidden;
            font-family: inherit;
            border: 1px solid rgba(0,0,0,0.08);
            transform: translateY(20px) scale(0.95);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .chat-container.active {
            transform: translateY(0) scale(1);
            opacity: 1;
            visibility: visible;
        }

        .chat-header {
            background: #2196F3;
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
        }

        .chat-close-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .chat-body {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .chat-message {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bot-message {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            align-self: flex-start;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .user-message {
            background: #2196F3;
            color: white;
            align-self: flex-end;
        }

        .quick-questions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .quick-question {
            background: #f0f7ff;
            color: #2196F3;
            border: 1px solid #d0e3ff;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-question:hover {
            background: #e0f0ff;
        }

        .chat-input-container {
            padding: 10px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 8px;
        }

        .chat-input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
        }

        .chat-input:focus {
            border-color: #2196F3;
        }

        .send-btn {
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .message-time {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
            text-align: right;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            width: fit-content;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 8px;
        }

        .typing-text {
            font-size: 12px;
            color: #666;
            margin-right: 6px;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background-color: #666;
            border-radius: 50%;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-3px); opacity: 1; }
        }

        /* Floating Chat Toggle & AI Badge */
        .chat-toggle-wrapper {
            position: fixed;
            bottom: 80px;
            right: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            z-index: 9999;
        }

        .chat-toggle-badge {
            background: linear-gradient(135deg, var(--theme-primary, #092c9f) 0%, var(--theme-primary-light, #004aad) 100%);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px 5px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            letter-spacing: 0.2px;
            white-space: nowrap;
            animation: pulseBadge 3s infinite ease-in-out;
            cursor: pointer;
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .chat-badge-close {
            background: rgba(255, 255, 255, 0.25);
            border: none;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            border-radius: 50%;
            width: 17px;
            height: 17px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-left: 6px;
            cursor: pointer;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .chat-badge-close:hover {
            background: rgba(255, 255, 255, 0.6);
            transform: scale(1.15);
        }

        .chat-toggle-badge::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 18px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--theme-primary, #092c9f);
        }

        @keyframes pulseBadge {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .chat-toggle {
            width: 52px;
            height: 52px;
            background: var(--theme-primary, #092c9f);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .chat-toggle:hover {
            transform: scale(1.1);
            background: var(--theme-primary-light, #004aad);
        }

        /* --- Leaderboard Podium --- */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 10px;
            padding: 30px 10px 10px;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .podium-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .podium-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            position: relative;
            margin-bottom: 35px;
            transition: transform 0.3s ease;
        }

        .podium-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .podium-item.rank-1 .podium-avatar {
            width: 85px;
            height: 85px;
            margin-bottom: 45px;
            transform: scale(1.1);
        }

        .podium-rank-badge {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
            color: white;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }

        .podium-item.rank-1 .podium-rank-badge { background: #fbbf24; width: 34px; height: 34px; font-size: 18px; top: -15px; bottom: auto; }
        .podium-item.rank-2 .podium-rank-badge { background: #94a3b8; }
        .podium-item.rank-3 .podium-rank-badge { background: #d97706; }

        .podium-name {
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            color: #1e293b;
            margin-bottom: 2px;
            width: 100%;
            word-break: break-word;
            line-height: 1.3;
        }

        .podium-score {
            font-size: 10px;
            font-weight: 800;
            color: #3b82f6;
            background: #eff6ff;
            padding: 2px 10px;
            border-radius: 12px;
        }

        .crown-icon {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
            color: #fbbf24;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            display: none;
        }

        .podium-item.rank-1 .crown-icon { display: block; animation: crownFloat 2s ease-in-out infinite; }
        
        @keyframes crownFloat {
            0%, 100% { transform: translate(-50%, 0) rotate(0); }
            50% { transform: translate(-50%, -5px) rotate(5deg); }
        }

        /* --- Minimalist Progress --- */
        .mini-progress {
            height: 4px;
            background: #f1f5f9;
            border-radius: 10px;
            width: 40px;
            overflow: hidden;
        }
        
    </style>

    <div class="section" id="user-section">
        <a href="/proseslogout" class="logout">
            <ion-icon name="exit-outline"></ion-icon>
        </a>
        <div id="user-detail">
            <div class="avatar">
                @if (!empty(Auth::guard('karyawan')->user()->foto))
                    @php
                        $path = Storage::url('uploads/karyawan/' . Auth::guard('karyawan')->user()->foto);
                        $fullPath = storage_path('app/public/uploads/karyawan/' . Auth::guard('karyawan')->user()->foto);
                        $cacheBust = file_exists($fullPath) ? filemtime($fullPath) : time();
                    @endphp
                    <img src="{{ url($path) }}?v={{ $cacheBust }}" alt="avatar" class="imaged w64" style="height:60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif
            </div>
            <div id="user-info">
                <h3 id="user-name">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</h3>
                <p class="user-subinfo">
                    <span class="user-meta">({{ $departemen->nama_dept }})</span>
                    <span>{{ Auth::guard('karyawan')->user()->jabatan }}</span>
                    <span class="user-subinfo">({{ $cabang->nama_cabang }})</span>
                </p>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    @if(($tipe_jabatan ?? 'guru') === 'petugas_piket')
                        {{-- Menu Petugas Piket: Monitoring --}}
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/piket/monitoring" class="primary" style="font-size: 40px;">
                                    <ion-icon name="eye-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Monitoring</span>
                            </div>
                        </div>
                    @else
                        {{-- Menu Guru: Teaching + Journal --}}
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/guru/status-profil" class="primary" style="font-size: 40px;">
                                    <ion-icon name="id-card-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Status Mengajar</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/guru/jurnal" class="warning" style="font-size: 40px;">
                                    <ion-icon name="journal-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">E-Jurnal</span>
                            </div>
                        </div>

                        @if(!empty($isWaliKelas) && $isWaliKelas)
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/guru/walikelas/monitoring" class="primary" style="font-size: 40px;">
                                    <ion-icon name="school-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Kelasku</span>
                            </div>
                        </div>
                        @endif
                    @endif


                    {{-- Payslip — selalu tampil --}}
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/slipgaji" class="green" style="font-size: 40px;">
                                <ion-icon name="file-tray-full-outline"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Slip Gaji</span>
                        </div>
                    </div>

                    {{-- Discussion — selalu tampil --}}
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/chat" class="danger" style="font-size: 40px; position: relative; display: inline-block;">
                                <ion-icon name="chatbubbles-outline"></ion-icon>
                                <span class="badge bg-danger" id="chat-notification-badge" 
                                      style="display:none; position: absolute; top: -5px; right: -10px; font-size: 10px; border-radius: 50%; min-width: 18px; height: 18px; border: 2px solid white; display: flex; align-items: center; justify-content: center; padding: 0;">0</span>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Diskusi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">
                <div class="col-6">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null)
                                        @if ($presensihariini->foto_in != null)
                                            @php
                                                $path = Storage::url('uploads/absensi/' . $presensihariini->foto_in);
                                            @endphp
                                            <img src="{{ url($path) }}" alt="" class="imaged w48">
                                        @else
                                            <ion-icon name="camera"></ion-icon>
                                        @endif
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Absen Masuk</h4>
                                    <span style="font-size: 11px;">{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null && $presensihariini->jam_out != null)
                                        @if ($presensihariini->foto_out != null)
                                            @php
                                                $path = Storage::url('uploads/absensi/' . $presensihariini->foto_out);
                                            @endphp
                                            <img src="{{ url($path) }}" alt="" class="imaged w48">
                                        @else
                                            <ion-icon name="camera"></ion-icon>
                                        @endif
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Absen Pulang</h4>
                                    <span style="font-size: 11px;">{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="rekappresensi">
            <h3>Rekap Presensi Bulan {{ $namabulan[(int)$bulanini] }} {{ $tahunini }}</h3>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 10px 5px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekappresensi->jmlhadir }}</span>
                            <ion-icon name="accessibility-outline" style="font-size: 1.6rem;"
                                class="text-primary mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.7rem; font-weight:600; white-space: nowrap; word-break: normal;">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 10px 5px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlizin }}
                            </span>
                            <ion-icon name="newspaper-outline" style="font-size: 1.6rem;"
                                class="text-success mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.7rem; font-weight:600; white-space: nowrap; word-break: normal;">Izin</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 10px 5px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlsakit }}</span>
                            <ion-icon name="medkit-outline" style="font-size: 1.6rem;"
                                class="text-warning mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.7rem; font-weight:600; white-space: nowrap; word-break: normal;">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 10px 5px !important; line-height:0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">
                                {{ $rekappresensi->jmlcuti }}
                            </span>
                            <ion-icon name="document-outline" style="font-size: 1.6rem;"
                                class="text-danger mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.65rem; font-weight:600; white-space: nowrap; word-break: normal;">Cuti</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(($tipe_jabatan ?? 'guru') !== 'petugas_piket')
        <div class="divider mt-4" style="display: flex; align-items: center; text-align: center; padding: 0 16px;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #cbd5e1);"></div>
            <span style="padding: 0 15px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; white-space: nowrap;">
                Informasi Terbaru
            </span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #cbd5e1);"></div>
        </div>

        <div class="section mt-3" id="announcement-section">
            <div class="card" style="background: white; border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 12px 16px; border: none;">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <ion-icon name="megaphone-outline" style="font-size: 20px; display: block;"></ion-icon>
                        </div>
                        <div class="col">
                            <h4 class="mb-0" style="color: white; font-size: 14px; font-weight: 700;">Pengumuman</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 15px;">
                    @if ($pengumuman->count() > 0)
                        @foreach ($pengumuman as $index => $item)
                            <div class="announcement-content {{ $index > 0 ? 'mt-2 pt-2' : '' }}" 
                                 style="{{ $index > 0 ? 'border-top: 1px solid #f3f4f6;' : '' }}">
                                <h3 style="font-size: 15px; font-weight: 700; color: #1e3a8a; margin-bottom: 2px;">{{ $item->judul }}</h3>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 8px;">
                                    <ion-icon name="calendar-outline" style="vertical-align: middle;"></ion-icon> 
                                    {{ date('d M Y', strtotime($item->tgl_pengumuman)) }}
                                </p>
                                <div class="announcement-full-content" id="announcement-content-{{ $index }}" style="display: none;">
                                    <div style="font-size: 13px; color: #374151; line-height: 1.5;">
                                        {!! nl2br($item->konten) !!}
                                    </div>

                                    @if($item->foto)
                                        <div class="mt-2 text-center">
                                            <img src="{{ Storage::url('uploads/pengumuman/' . $item->foto) }}" 
                                                 class="img-fluid rounded" 
                                                 style="width: 100%; height: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        </div>
                                    @endif

                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @if($item->doc_file)
                                            <a href="{{ Storage::url('uploads/pengumuman/' . $item->doc_file) }}" 
                                               download
                                               class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                               style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                                                <ion-icon name="document-attach-outline"></ion-icon>
                                                Unduh Lampiran
                                            </a>
                                        @endif

                                        @if($item->link)
                                            @php
                                                $url = $item->link;
                                                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                                                    $url = "http://" . $url;
                                                }
                                            @endphp
                                            <a href="{{ $url }}" 
                                               target="_blank"
                                               class="btn btn-sm btn-outline-info d-flex align-items-center gap-1"
                                               style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                                                <ion-icon name="link-outline"></ion-icon>
                                                Buka Tautan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <button onclick="toggleAnnouncement('{{ $index }}')" id="btn-readmore-{{ $index }}" style="background: transparent; color: #3b82f6; padding: 0; font-size: 12px; font-weight: 600; border: none; margin-top: 5px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    Baca Selengkapnya <ion-icon name="chevron-down-outline"></ion-icon>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-2">
                            <p class="mb-0" style="font-size: 13px; color: #9ca3af; font-style: italic;">
                                <ion-icon name="information-circle-outline" style="vertical-align: middle;"></ion-icon>
                                Tidak ada pengumuman.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(($tipe_jabatan ?? 'guru') === 'petugas_piket')
        {{-- Widget Ringkasan Piket (Gantikan Leaderboard) --}}
        <div class="divider mt-4" style="display: flex; align-items: center; text-align: center; padding: 0 16px;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #cbd5e1);"></div>
            <span style="padding: 0 15px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; white-space: nowrap;">
                Monitoring Piket Hari Ini
            </span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #cbd5e1);"></div>
        </div>

        <div class="section mt-2">
            <a href="/piket/monitoring" style="text-decoration: none;">
                <div class="card" style="border-radius: 15px; background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); border: none; box-shadow: 0 4px 20px rgba(30, 58, 138, 0.25); overflow: hidden;">
                    <div class="card-body" style="padding: 18px 15px;">
                        <div class="d-flex align-items-center mb-3">
                            <div style="flex-shrink: 0; width: 45px; height: 45px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px; border: 1px solid rgba(255,255,255,0.3);">
                                <ion-icon name="eye-outline" style="font-size: 22px; color: white;"></ion-icon>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700; color: white;">Monitoring Kehadiran</div>
                                <div style="font-size: 11px; color: rgba(255,255,255,0.7);">Lihat status kehadiran guru hari ini</div>
                            </div>
                        </div>
                        @if($ringkasanPiket)
                        <div class="row text-center" style="margin: 0 -4px;">
                            <div class="col-4" style="padding: 0 4px;">
                                <div style="background: rgba(255,255,255,0.15); border-radius: 10px; padding: 10px 4px;">
                                    <div style="font-size: 20px; font-weight: 800; color: white;">{{ $ringkasanPiket->total }}</div>
                                    <div style="font-size: 9px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.5px;">Total</div>
                                </div>
                            </div>
                            <div class="col-4" style="padding: 0 4px;">
                                <div style="background: rgba(34,197,94,0.3); border-radius: 10px; padding: 10px 4px;">
                                    <div style="font-size: 20px; font-weight: 800; color: #86efac;">{{ $ringkasanPiket->hadir }}</div>
                                    <div style="font-size: 9px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.5px;">Hadir</div>
                                </div>
                            </div>
                            <div class="col-4" style="padding: 0 4px;">
                                <div style="background: rgba(239,68,68,0.3); border-radius: 10px; padding: 10px 4px;">
                                    <div style="font-size: 20px; font-weight: 800; color: #fca5a5;">{{ $ringkasanPiket->belum }}</div>
                                    <div style="font-size: 9px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.5px;">Belum</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div style="text-align: center; margin-top: 12px;">
                            <span style="font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 600;">Tap untuk detail <ion-icon name="arrow-forward-outline" style="vertical-align: middle;"></ion-icon></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        {{-- Leaderboard Section --}}
        @if(isset($isPeriodActive) && !$isPeriodActive)
        <div class="divider mt-4" style="display: flex; align-items: center; text-align: center; padding: 0 16px;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #cbd5e1);"></div>
            <span style="padding: 0 15px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; white-space: nowrap;">
                Papan Skor & Peringkat
            </span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #cbd5e1);"></div>
        </div>

        <div class="section mt-2 px-2">
            <div class="card" style="border-radius: 16px; border: 1px dashed #f59e0b; background: #fffbeb; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.08);">
                <div class="card-body" style="padding: 16px;">
                    <div class="d-flex align-items-center mb-2">
                        <div style="flex-shrink: 0; width: 38px; height: 38px; background: rgba(245, 158, 11, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                            <ion-icon name="information-circle" style="font-size: 22px; color: #d97706;"></ion-icon>
                        </div>
                        <div>
                            <strong style="font-size: 13px; color: #92400e; display: block;">Informasi Perhitungan Poin</strong>
                            <span style="font-size: 10px; color: #b45309; font-weight: 600;">Status: Non-Aktif / Belum Dimulai</span>
                        </div>
                    </div>
                    <div style="font-size: 11px; color: #78350f; line-height: 1.5; background: rgba(255, 255, 255, 0.6); padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(245, 158, 11, 0.2);">
                        {{ $periodMessage ?? 'Tanggal mulai perhitungan skor poin belum dimulai atau telah berakhir.' }}
                    </div>
                </div>
            </div>
        </div>
        @else

        @if($tipe_jabatan === 'petugas_piket')
        <div class="divider mt-4" style="display: flex; align-items: center; text-align: center; padding: 0 16px;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #cbd5e1);"></div>
            <span style="padding: 0 15px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; white-space: nowrap;">
                Papan Skor Petugas Piket
            </span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #cbd5e1);"></div>
        </div>

        <div class="section mt-1">
            <div class="alert alert-outline-info" style="border-radius: 12px; padding: 12px; background: #fffbeb; border: 1px dashed #d97706;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center">
                        <ion-icon name="information-circle" style="font-size: 20px; color: #d97706; margin-right: 8px;"></ion-icon>
                        <strong style="font-size: 12px; color: #92400e;">Ketentuan Penilaian Petugas Piket</strong>
                    </div>
                </div>
                <div style="font-size: 10px; color: #475569; line-height: 1.5; margin-left: 28px;">
                    • <b>Komposisi Skor:</b> Kehadiran Presensi (50%) + Monitoring WA Guru (50%).
                </div>
                <div id="piket-sk-detail" style="font-size: 10px; color: #475569; line-height: 1.5; margin-left: 28px; display: none; margin-top: 4px;">
                    • <b>Absen Lengkap:</b> Wajib Absen Masuk & Pulang. Lupa absen pulang (lewat 23:59) memotong 0.5 hari kehadiran.<br>
                    • <b>Monitoring WA:</b> Keaktifan mengirim pesan pengingat presensi ke guru-guru pada hari piket.<br>
                    • <b>Penentu Peringkat Sama (Tie-Breaker):</b> Diurutkan dari Total Skor &rarr; % Kehadiran &rarr; Hari Hadir Fisik Terbanyak &rarr; Total WA Terkirim &rarr; Penalti Lupa Pulang Terendah.<br>
                    • <b>Periode Perhitungan:</b> Diatur secara dinamis oleh Admin via Konfigurasi Poin.
                </div>
                <div style="margin-left: 28px; margin-top: 6px;">
                    <a href="javascript:void(0)" id="piket-sk-btn" onclick="toggleSkRules('piket')" style="font-size: 10px; color: #d97706; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                        Lihat Ketentuan Lengkap <ion-icon name="chevron-down-outline"></ion-icon>
                    </a>
                </div>
            </div>
        </div>

        @if($piketUserRank)
        <div class="section mt-2">
            <div class="card" style="border-radius: 15px; background: linear-gradient(135deg, #92400e 0%, #d97706 100%); border: none; box-shadow: 0 4px 20px rgba(146, 64, 14, 0.25); overflow: hidden;">
                <div class="card-body" style="padding: 18px 15px;">
                    <div class="d-flex align-items-center mb-3">
                        <div style="flex-shrink: 0; width: 45px; height: 45px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px; border: 1px solid rgba(255,255,255,0.3);">
                            <span style="color: white; font-weight: 900; font-size: 18px;">#{{ $piketUserRank['rank'] }}</span>
                        </div>
                        <div style="flex-grow: 1; min-width: 0; padding-right: 10px;">
                            <div style="color: rgba(255,255,255,0.8); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Peringkat Saya</div>
                            <div style="color: white; font-size: 14px; font-weight: 700; line-height: 1.2; word-break: break-word;">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</div>
                        </div>
                        <div style="flex-shrink: 0;" class="text-end">
                            <div style="color: white; font-size: 20px; font-weight: 900; line-height: 1;">{{ $piketUserRank['score'] }}</div>
                            <div style="color: rgba(255,255,255,0.8); font-size: 8px; font-weight: 700; text-transform: uppercase; margin-top: 2px;">Total Skor</div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="row align-items-center g-0 mb-2">
                            <div class="col-4" style="color: rgba(255,255,255,0.9); font-size: 10px; font-weight: 600;">KEHADIRAN</div>
                            <div class="col-6 px-2">
                                <div class="progress" style="height: 6px; background: rgba(0,0,0,0.2); border-radius: 10px;">
                                    <div class="progress-bar bg-success" style="width: <?= $piketUserRank['stats']['attendance_efficiency'] ?>%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            <div class="col-2 text-end" style="color: white; font-size: 11px; font-weight: 800;">{{ round($piketUserRank['stats']['attendance_efficiency']) }}%</div>
                        </div>
                        <div class="row align-items-center g-0">
                            <div class="col-4" style="color: rgba(255,255,255,0.9); font-size: 10px; font-weight: 600;">MONITORING</div>
                            <div class="col-6 px-2">
                                <div class="progress" style="height: 6px; background: rgba(0,0,0,0.2); border-radius: 10px;">
                                    <div class="progress-bar bg-warning" style="width: <?= $piketUserRank['stats']['monitoring_efficiency'] ?>%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            <div class="col-2 text-end" style="color: white; font-size: 11px; font-weight: 800;">{{ round($piketUserRank['stats']['monitoring_efficiency']) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="section mt-2">
            @if($topPiket->count() >= 3)
                @php
                    $p1 = $topPiket->values()->get(0);
                    $p2 = $topPiket->values()->get(1);
                    $p3 = $topPiket->values()->get(2);
                @endphp
                <div class="podium-container">
                    <!-- Rank 2 -->
                    <div class="podium-item rank-2">
                        <div class="podium-avatar">
                            <img src="{{ $p2['foto'] ? Storage::url('uploads/karyawan/' . $p2['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">2</div>
                        </div>
                        <div class="podium-name">{{ $p2['nama_lengkap'] }}</div>
                        <div class="podium-score">{{ $p2['score'] }}</div>
                    </div>
                    <!-- Rank 1 -->
                    <div class="podium-item rank-1">
                        <ion-icon name="ribbon" class="crown-icon"></ion-icon>
                        <div class="podium-avatar">
                            <img src="{{ $p1['foto'] ? Storage::url('uploads/karyawan/' . $p1['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">1</div>
                        </div>
                        <div class="podium-name" style="font-size: 13px;">{{ $p1['nama_lengkap'] }}</div>
                        <div class="podium-score" style="font-size: 12px; background: #fffbeb; color: #d97706;">{{ $p1['score'] }}</div>
                    </div>
                    <!-- Rank 3 -->
                    <div class="podium-item rank-3">
                        <div class="podium-avatar">
                            <img src="{{ $p3['foto'] ? Storage::url('uploads/karyawan/' . $p3['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">3</div>
                        </div>
                        <div class="podium-name">{{ $p3['nama_lengkap'] }}</div>
                        <div class="podium-score">{{ $p3['score'] }}</div>
                    </div>
                </div>
            @endif

            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-0">
                    <div class="leaderboard-container" style="max-height: 400px; overflow-y: auto;">
                        @foreach($topPiket as $rank => $t)
                            @php 
                                $index = $loop->iteration;
                                if ($topPiket->count() >= 3 && $index <= 3) continue;
                                $isCurrentUser = $t['nik'] == Auth::guard('karyawan')->user()->nik;
                            @endphp
                            <div class="d-flex align-items-center py-2 px-3 {{ !$loop->last ? 'border-bottom' : '' }}" 
                                 style="{{ $isCurrentUser ? 'background: #fffbeb;' : '' }}">
                                <div style="width: 25px; font-weight: 800; color: #64748b; font-size: 13px;">#{{ $index }}</div>
                                <img src="{{ $t['foto'] ? Storage::url('uploads/karyawan/' . $t['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}" 
                                     class="imaged w32 rounded-circle mx-2">
                                <div class="flex-grow-1">
                                    <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $t['nama_lengkap'] }}</div>
                                    <div class="d-flex gap-2 mt-1">
                                        <div class="mini-progress"><div class="progress-bar bg-success" style="width: <?= $t['stats']['attendance_efficiency'] ?>%"></div></div>
                                        <div class="mini-progress"><div class="progress-bar bg-warning" style="width: <?= $t['stats']['monitoring_efficiency'] ?>%"></div></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size: 12px; font-weight: 800; color: #92400e;">{{ $t['score'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="divider mt-4" style="display: flex; align-items: center; text-align: center; padding: 0 16px;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #cbd5e1);"></div>
            <span style="padding: 0 15px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; white-space: nowrap;">
                Papan Skor Guru Teladan
            </span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #cbd5e1);"></div>
        </div>

        <div class="section mt-1">
            <div class="alert alert-outline-info" style="border-radius: 12px; padding: 12px; background: #f0f7ff; border: 1px dashed #3b82f6;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center">
                        <ion-icon name="information-circle" style="font-size: 20px; color: #3b82f6; margin-right: 8px;"></ion-icon>
                        <strong style="font-size: 12px; color: #1e3a8a;">Ketentuan Penilaian Guru Teladan</strong>
                    </div>
                </div>
                <div style="font-size: 10px; color: #475569; line-height: 1.5; margin-left: 28px;">
                    • <b>Komposisi Skor:</b> Kehadiran Presensi (50%) + E-Jurnal Mengajar (50%).
                </div>
                <div id="guru-sk-detail" style="font-size: 10px; color: #475569; line-height: 1.5; margin-left: 28px; display: none; margin-top: 4px;">
                    • <b>Absen Lengkap:</b> Wajib Absen Masuk & Pulang. Lupa absen pulang (lewat 23:59) memotong 0.5 hari kehadiran.<br>
                    • <b>Penentu Peringkat Sama (Tie-Breaker):</b> Diurutkan dari Total Skor &rarr; % Kehadiran &rarr; Hari Hadir Fisik Terbanyak &rarr; Keaktifan Jurnal &rarr; Total Jam Mengajar Terbanyak &rarr; Penalti Lupa Pulang Terendah.<br>
                    • <b>Izin, Sakit & Cuti:</b> Yang disetujui resmi oleh Admin/Waka tidak dikenakan denda penalti.<br>
                    • <b>Periode Perhitungan:</b> Diatur secara dinamis oleh Admin via Konfigurasi Poin.
                </div>
                <div style="margin-left: 28px; margin-top: 6px;">
                    <a href="javascript:void(0)" id="guru-sk-btn" onclick="toggleSkRules('guru')" style="font-size: 10px; color: #2563eb; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                        Lihat Ketentuan Lengkap <ion-icon name="chevron-down-outline"></ion-icon>
                    </a>
                </div>
            </div>
        </div>

        <div class="section mt-2">
            <div class="card" style="border-radius: 15px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border: none; box-shadow: 0 4px 20px rgba(30, 58, 138, 0.25); overflow: hidden;">
                <div class="card-body" style="padding: 18px 15px;">
                    <div class="d-flex align-items-center mb-3">
                        <div style="flex-shrink: 0; width: 45px; height: 45px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px; border: 1px solid rgba(255,255,255,0.3);">
                            <span style="color: white; font-weight: 900; font-size: 18px;">#{{ $userRank['rank'] }}</span>
                        </div>
                        <div style="flex-grow: 1; min-width: 0; padding-right: 10px;">
                            <div style="color: rgba(255,255,255,0.8); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Peringkat Saya</div>
                            <div style="color: white; font-size: 14px; font-weight: 700; line-height: 1.2; word-break: break-word;">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</div>
                        </div>
                        <div style="flex-shrink: 0;" class="text-end">
                            <div style="color: white; font-size: 20px; font-weight: 900; line-height: 1;">{{ $userRank['score'] }}</div>
                            <div style="color: rgba(255,255,255,0.8); font-size: 8px; font-weight: 700; text-transform: uppercase; margin-top: 2px;">Total Skor</div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="row align-items-center g-0 mb-2">
                            <div class="col-4" style="color: rgba(255,255,255,0.9); font-size: 10px; font-weight: 600;">KEHADIRAN</div>
                            <div class="col-6 px-2">
                                <div class="progress" style="height: 6px; background: rgba(0,0,0,0.2); border-radius: 10px;">
                                    <div class="progress-bar bg-success" style="width: <?= $userRank['stats']['presence_efficiency'] ?>%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            <div class="col-2 text-end" style="color: white; font-size: 11px; font-weight: 800;">{{ round($userRank['stats']['presence_efficiency']) }}%</div>
                        </div>
                        <div class="row align-items-center g-0">
                            <div class="col-4" style="color: rgba(255,255,255,0.9); font-size: 10px; font-weight: 600;">E-JURNAL</div>
                            <div class="col-6 px-2">
                                <div class="progress" style="height: 6px; background: rgba(0,0,0,0.2); border-radius: 10px;">
                                    <div class="progress-bar bg-info" style="width: <?= $userRank['stats']['journal_efficiency'] ?>%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            <div class="col-2 text-end" style="color: white; font-size: 11px; font-weight: 800;">{{ round($userRank['stats']['journal_efficiency']) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section mt-2">
            @if($topTeachers->count() >= 3)
                @php
                    $t1 = $topTeachers->values()->get(0);
                    $t2 = $topTeachers->values()->get(1);
                    $t3 = $topTeachers->values()->get(2);
                @endphp
                <div class="podium-container">
                    <!-- Rank 2 -->
                    <div class="podium-item rank-2">
                        <div class="podium-avatar">
                            <img src="{{ $t2['foto'] ? Storage::url('uploads/karyawan/' . $t2['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">2</div>
                        </div>
                        <div class="podium-name">{{ $t2['nama_lengkap'] }}</div>
                        <div class="podium-score">{{ $t2['score'] }}</div>
                    </div>
                    <!-- Rank 1 -->
                    <div class="podium-item rank-1">
                        <ion-icon name="ribbon" class="crown-icon"></ion-icon>
                        <div class="podium-avatar">
                            <img src="{{ $t1['foto'] ? Storage::url('uploads/karyawan/' . $t1['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">1</div>
                        </div>
                        <div class="podium-name" style="font-size: 13px;">{{ $t1['nama_lengkap'] }}</div>
                        <div class="podium-score" style="font-size: 12px; background: #fffbeb; color: #d97706;">{{ $t1['score'] }}</div>
                    </div>
                    <!-- Rank 3 -->
                    <div class="podium-item rank-3">
                        <div class="podium-avatar">
                            <img src="{{ $t3['foto'] ? Storage::url('uploads/karyawan/' . $t3['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}">
                            <div class="podium-rank-badge">3</div>
                        </div>
                        <div class="podium-name">{{ $t3['nama_lengkap'] }}</div>
                        <div class="podium-score">{{ $t3['score'] }}</div>
                    </div>
                </div>
            @endif

            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-0">
                    <div class="leaderboard-container" style="max-height: 400px; overflow-y: auto;">
                        @foreach($topTeachers as $rank => $t)
                            @php 
                                $index = $loop->iteration;
                                if ($topTeachers->count() >= 3 && $index <= 3) continue;
                                $isCurrentUser = $t['nik'] == Auth::guard('karyawan')->user()->nik;
                            @endphp
                            <div class="d-flex align-items-center py-2 px-3 {{ !$loop->last ? 'border-bottom' : '' }}" 
                                 style="{{ $isCurrentUser ? 'background: #f8fafc;' : '' }}">
                                <div style="width: 25px; font-weight: 800; color: #64748b; font-size: 13px;">#{{ $index }}</div>
                                <img src="{{ $t['foto'] ? Storage::url('uploads/karyawan/' . $t['foto']) : asset('assets/img/sample/avatar/avatar1.jpg') }}" 
                                     class="imaged w32 rounded-circle mx-2">
                                <div class="flex-grow-1">
                                    <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $t['nama_lengkap'] }}</div>
                                    <div class="d-flex gap-2 mt-1">
                                        <div class="mini-progress"><div class="progress-bar bg-success" style="width: <?= $t['stats']['presence_efficiency'] ?>%"></div></div>
                                        <div class="mini-progress"><div class="progress-bar bg-info" style="width: <?= $t['stats']['journal_efficiency'] ?>%"></div></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size: 12px; font-weight: 800; color: #1e3a8a;">{{ $t['score'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
        
        
        
        
        <!-- Footer with donation info -->
        <footer style="text-align: center; font-size: 11px; color: #999; margin-top: 20px; padding-bottom: 80px;">
            &copy; Copyright by Putriana Dyah Anjani
            <a href="https://instagram.com/ptriidyhhhh" target="_blank" style="color: #999; text-decoration: none; margin-left: 1px;">
                <ion-icon name="logo-instagram"></ion-icon>
            </a>
        </footer>
    </div>

     <!-- Live Chat Widget -->
    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <span>AI Siguru APP</span>
            <button id="closeChat" class="chat-close-btn">&times;</button>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message bot-message">
                Halo <strong>{{ Auth::guard('karyawan')->user()->nama_lengkap }}</strong>! Saya AI Siguru APP. Ada yang bisa saya bantu hari ini?
                <div class="message-time" id="welcomeTime"></div>
            </div>
        </div>
        <div class="chat-input-container">
            <input type="text" id="userMessage" placeholder="Ketik pesan Anda..." class="chat-input">
            <button id="sendMessage" class="send-btn">
                <ion-icon name="send" style="font-size: 18px;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- Chat Toggle Button with Badge Title & Dismiss Option -->
    <div class="chat-toggle-wrapper" id="chatToggleWrapper">
        <div class="chat-toggle-badge" id="chatBadge">
            <span>Tanya AI Siguru</span>
            <button class="chat-badge-close" id="hideChatWidget" title="Sembunyikan Widget">&times;</button>
        </div>
        <div class="chat-toggle" id="chatToggle">
            <ion-icon name="chatbubble-ellipses"></ion-icon>
        </div>
    </div>

    <script>
        // // Function to show rules popup
        // function showRulesPopup() {
        //     if (!sessionStorage.getItem('rulesShown')) {
        //         setTimeout(() => {
        //             document.getElementById('rulesPopup').classList.add('active');
        //         }, 1000);
        //     }
        // }

        // // Close popup functions
        // document.getElementById('closePopup').addEventListener('click', closePopup);
        // document.getElementById('confirmRules').addEventListener('click', closePopup);

        // function closePopup() {
        //     document.getElementById('rulesPopup').classList.remove('active');
        //     sessionStorage.setItem('rulesShown', 'true');
        // }

        //         e.preventDefault();
        //         window.location.href = "geo:0,0";
        //     });
        // });

          // Function to format time as HH:MM
        function getCurrentTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        // Set initial welcome message time
        document.getElementById('welcomeTime').textContent = getCurrentTime();

        // Enhanced chat functionality
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chatToggle');
            const chatBadge = document.getElementById('chatBadge');
            const chatContainer = document.getElementById('chatContainer');
            const closeChat = document.getElementById('closeChat');
            const chatBody = document.getElementById('chatBody');
            const userMessage = document.getElementById('userMessage');
            
            // Toggle chat visibility
            function toggleChat() {
                chatContainer.classList.toggle('active');
            }

            chatToggle?.addEventListener('click', toggleChat);
            chatBadge?.addEventListener('click', toggleChat);

            // Hide chat widget temporarily when user closes badge (Appears back on page refresh)
            const hideChatWidgetBtn = document.getElementById('hideChatWidget');
            const chatToggleWrapper = document.getElementById('chatToggleWrapper');

            hideChatWidgetBtn?.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevents chat window from opening
                if (chatToggleWrapper) chatToggleWrapper.style.display = 'none';
                if (chatContainer) chatContainer.classList.remove('active');
            });
            
            // Close chat
            closeChat.addEventListener('click', function() {
                chatContainer.classList.remove('active');
            });
            
            // Add message to chat with real-time timestamp
            function addMessage(text, sender) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${sender}-message`;
                const timestamp = getCurrentTime();
                
                messageDiv.innerHTML = `
                    ${text}
                    <div class="message-time">${timestamp}</div>
                `;
                
                chatBody.appendChild(messageDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }
            
            // Show typing indicator
            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'typing-indicator';
                typingDiv.id = 'typingIndicator';
                typingDiv.innerHTML = `
                    <span class="typing-text">Asisten mengetik</span>
                    <div class="typing-dots">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                `;
                chatBody.appendChild(typingDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }
            
            // Handle sending messages to Google Gemini AI
            function sendUserMessageToAI(messageText) {
                if (!messageText) return;
                
                addMessage(messageText, 'user');
                userMessage.value = '';
                showTypingIndicator();

                $.ajax({
                    url: '/ai/chat/send',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        message: messageText
                    },
                    success: function(res) {
                        document.getElementById('typingIndicator')?.remove();
                        if (res.status === 'success' && res.reply) {
                            addMessage(res.reply, 'bot');
                        } else {
                            addMessage(res.reply || 'Maaf, terjadi kesalahan saat menghubungi SiGuru AI.', 'bot');
                        }
                    },
                    error: function(xhr) {
                        document.getElementById('typingIndicator')?.remove();
                        addMessage('Maaf, terjadi kendala koneksi ke server AI.', 'bot');
                    }
                });
            }

            // Handle quick questions
            window.sendQuickQuestion = function(question) {
                sendUserMessageToAI(question);
            };
            
            // Handle send button and Enter key
            document.getElementById('sendMessage').addEventListener('click', function() {
                sendUserMessageToAI(userMessage.value.trim());
            });

            userMessage.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendUserMessageToAI(userMessage.value.trim());
                }
            });
        });
    </script>
@endsection

@push('myscript')
    <script>
        $(function() {
            /**
             * Logika Notifikasi Chat
             */
            function updateChatBadge() {
                $.ajax({
                    url: '/chat/unread-count',
                    type: 'GET',
                    success: function(res) {
                        const badge = $('#chat-notification-badge');
                        if (res.unread_count > 0) {
                            badge.text(res.unread_count > 99 ? '99+' : res.unread_count);
                            badge.css('display', 'flex');
                        } else {
                            badge.hide();
                        }
                    },
                    error: function() {
                        console.log('Gagal mengambil jumlah pesan baru');
                    }
                });
            }

            // Jalankan setiap 15 detik
            setInterval(updateChatBadge, 15000);
            updateChatBadge();

            // Toggle pengumuman read more
            window.toggleAnnouncement = function(index) {
                const content = document.getElementById('announcement-content-' + index);
                const btn = document.getElementById('btn-readmore-' + index);
                
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    btn.innerHTML = 'Tutup <ion-icon name="chevron-up-outline"></ion-icon>';
                } else {
                    content.style.display = 'none';
                    btn.innerHTML = 'Baca Selengkapnya <ion-icon name="chevron-down-outline"></ion-icon>';
                }
            }

            // Toggle S&K Ketentuan Penilaian
            window.toggleSkRules = function(type) {
                const content = document.getElementById(type + '-sk-detail');
                const btn = document.getElementById(type + '-sk-btn');
                
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    btn.innerHTML = 'Sembunyikan Ketentuan <ion-icon name="chevron-up-outline"></ion-icon>';
                } else {
                    content.style.display = 'none';
                    btn.innerHTML = 'Lihat Ketentuan Lengkap <ion-icon name="chevron-down-outline"></ion-icon>';
                }
            }
        });
    </script>
@endpush
 