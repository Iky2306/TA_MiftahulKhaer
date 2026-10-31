@extends('layouts.presensi')
@section('content')
    <style>
        /* === Header Monitoring === */
        .monitoring-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            padding: 20px 16px 24px;
            color: white;
            position: relative;
        }

        .monitoring-header .back-btn {
            position: absolute;
            top: 20px;
            left: 16px;
            color: white;
            font-size: 24px;
            text-decoration: none;
            z-index: 2;
        }

        .monitoring-header .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .monitoring-header .header-subtitle {
            text-align: center;
            font-size: 12px;
            opacity: 0.85;
        }

        /* === Ringkasan Cards === */
        .ringkasan-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 0 16px;
            margin-top: -16px;
            position: relative;
            z-index: 2;
        }

        .ringkasan-card {
            background: white;
            border-radius: 16px;
            padding: 14px 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            border: 1px solid #f1f5f9;
        }

        .ringkasan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }

        .ringkasan-card .rc-value {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .ringkasan-card .rc-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        .rc-total { border-top: 4px solid #3b82f6; }
        .rc-hadir { border-top: 4px solid #22c55e; }
        .rc-belum { border-top: 4px solid #ef4444; }
        .rc-izin { border-top: 4px solid #f59e0b; }

        .rc-total .rc-value { color: #1e293b; }
        .rc-hadir .rc-value { color: #22c55e; }
        .rc-belum .rc-value { color: #ef4444; }
        .rc-izin .rc-value { color: #f59e0b; }

        /* === Filter Bar === */
        .filter-bar {
            padding: 14px 16px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .filter-btn {
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
            background: #ffffff;
            color: #64748b;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #e2e8f0;
        }

        .filter-btn ion-icon {
            font-size: 14px;
        }

        .filter-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 4px 10px rgba(37,99,235,0.25);
        }

        .filter-btn.active ion-icon {
            color: white !important;
        }

        /* === Guru Card === */
        .guru-list {
            padding: 0 16px 100px;
        }

        .guru-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
            border-left: 5px solid #cbd5e1;
            transition: all 0.3s ease;
        }

        .guru-card.status-hadir { border-left-color: #22c55e; }
        .guru-card.status-belum_hadir { border-left-color: #ef4444; }
        .guru-card.status-i, .guru-card.status-s, .guru-card.status-c { border-left-color: #f59e0b; }
        .guru-card.status-libur { border-left-color: #a855f7; }

        .guru-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .guru-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1.3;
        }

        .guru-mapel {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .guru-jadwal {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .guru-jadwal ion-icon {
            font-size: 13px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-badge ion-icon {
            font-size: 12px;
        }

        .badge-hadir { background: #dcfce7; color: #16a34a; }
        .badge-belum { background: #fef2f2; color: #ef4444; }
        .badge-izin { background: #fffbeb; color: #d97706; }
        .badge-libur { background: #f3e8ff; color: #8b5cf6; }

        /* Jam Info */
        .guru-jam-info {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e2e8f0;
        }

        .jam-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #475569;
        }

        .jam-item ion-icon {
            font-size: 14px;
        }

        .jam-item.jam-masuk ion-icon { color: #22c55e; }
        .jam-item.jam-pulang ion-icon { color: #ef4444; }

        /* WA Button */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 7px 14px;
            border-radius: 8px;
            border: none;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #25D366;
            color: white;
        }

        .btn-wa:hover {
            background: #1DA851;
            transform: translateY(-1px);
        }

        .btn-wa:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        .btn-wa ion-icon {
            font-size: 16px;
        }

        .btn-wa-manual {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 7px 14px;
            border-radius: 8px;
            border: none;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #2563eb;
            color: white;
            text-decoration: none;
        }

        .btn-wa-manual:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            color: white;
        }

        .btn-wa-manual ion-icon {
            font-size: 16px;
        }

        .wa-sent-info {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
            font-size: 11px;
            color: #16a34a;
            font-weight: 600;
        }

        .wa-sent-info ion-icon {
            font-size: 14px;
        }

        .wa-no-hp-info {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
            font-size: 10px;
            color: #94a3b8;
        }

        /* Keterangan Izin */
        .keterangan-izin {
            font-size: 11px;
            color: #b45309;
            margin-top: 4px;
            font-style: italic;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }

        .empty-state ion-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .empty-state p {
            font-size: 13px;
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            text-align: center;
            padding: 6px;
            font-size: 10px;
            color: #94a3b8;
        }

        .refresh-indicator .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            margin-right: 4px;
            animation: blink 2s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }
    </style>

    {{-- Header --}}
    <div class="monitoring-header">
        <a href="/dashboard" class="back-btn">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>
        <div class="header-title">Monitoring Kehadiran</div>
        <div class="header-subtitle">
            {{ $namaHari }}, {{ date('d F Y', strtotime($hariIni)) }} • {{ $cabang->nama_cabang ?? '-' }}
        </div>
    </div>



    {{-- Ringkasan --}}
    <div class="ringkasan-grid">
        <div class="ringkasan-card rc-total">
            <div class="rc-value" id="rc-total">{{ $ringkasan->total }}</div>
            <div class="rc-label">Total</div>
        </div>
        <div class="ringkasan-card rc-hadir">
            <div class="rc-value" id="rc-hadir">{{ $ringkasan->hadir }}</div>
            <div class="rc-label">Hadir</div>
        </div>
        <div class="ringkasan-card rc-belum">
            <div class="rc-value" id="rc-belum">{{ $ringkasan->belum_hadir }}</div>
            <div class="rc-label">Belum</div>
        </div>
        <div class="ringkasan-card rc-izin">
            <div class="rc-value" id="rc-izin">{{ $ringkasan->izin + $ringkasan->sakit + $ringkasan->cuti }}</div>
            <div class="rc-label">Izin/Skt</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <button class="filter-btn active" data-filter="semua">
            <ion-icon name="grid-outline"></ion-icon> Semua (<span class="count">{{ $ringkasan->total }}</span>)
        </button>
        <button class="filter-btn" data-filter="belum_hadir">
            <ion-icon name="close-circle-outline" style="color: #ef4444;"></ion-icon> Belum (<span class="count">{{ $ringkasan->belum_hadir }}</span>)
        </button>
        <button class="filter-btn" data-filter="hadir">
            <ion-icon name="checkmark-circle-outline" style="color: #22c55e;"></ion-icon> Hadir (<span class="count">{{ $ringkasan->hadir }}</span>)
        </button>
        <button class="filter-btn" data-filter="izin">
            <ion-icon name="document-text-outline" style="color: #f59e0b;"></ion-icon> Izin/Skt (<span class="count">{{ $ringkasan->izin + $ringkasan->sakit + $ringkasan->cuti }}</span>)
        </button>
    </div>

    {{-- Auto-refresh indicator --}}
    <div class="refresh-indicator">
        <span class="dot"></span>
    </div>

    {{-- Guru List --}}
    <div class="guru-list" id="guru-list">
        @if($guruHariIni->isEmpty())
            <div class="empty-state">
                <ion-icon name="people-outline"></ion-icon>
                <p>Tidak ada guru dengan jadwal hari ini</p>
            </div>
        @else
            @foreach($guruHariIni as $guru)
                <div class="guru-card status-{{ $guru->status }}" data-nik="{{ $guru->nik }}" data-status="{{ $guru->status }}">
                    <div class="guru-card-top">
                        <div>
                            <p class="guru-name">{{ $guru->nama_lengkap }}</p>
                            <div class="guru-mapel">{{ $guru->jabatan }}</div>
                            @if($guru->jam_kerja)
                                <div class="guru-jadwal">
                                    <ion-icon name="time-outline"></ion-icon>
                                    Jadwal: {{ substr($guru->jam_kerja->jam_masuk ?? '-', 0, 5) }} - {{ substr($guru->jam_kerja->jam_pulang ?? '-', 0, 5) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($guru->status === 'hadir')
                                <span class="status-badge badge-hadir">
                                    <ion-icon name="checkmark-circle"></ion-icon> Hadir
                                </span>
                            @elseif($guru->status === 'belum_hadir')
                                <span class="status-badge badge-belum">
                                    <ion-icon name="close-circle"></ion-icon> Belum
                                </span>
                            @elseif($guru->status === 'i')
                                <span class="status-badge badge-izin">
                                    <ion-icon name="document-text"></ion-icon> Izin
                                </span>
                            @elseif($guru->status === 's')
                                <span class="status-badge badge-izin">
                                    <ion-icon name="medkit"></ion-icon> Sakit
                                </span>
                            @elseif($guru->status === 'c')
                                <span class="status-badge badge-izin">
                                    <ion-icon name="calendar"></ion-icon> Cuti
                                </span>
                            @elseif($guru->status === 'libur')
                                <span class="status-badge badge-libur">
                                    <ion-icon name="ribbon"></ion-icon> Libur
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Jam Masuk/Pulang (hanya untuk yang hadir) --}}
                    @if($guru->status === 'hadir')
                        <div class="guru-jam-info">
                            <div class="jam-item jam-masuk">
                                <ion-icon name="log-in-outline"></ion-icon>
                                Masuk: {{ $guru->jam_in ? substr($guru->jam_in, 0, 5) : '-' }}
                            </div>
                            <div class="jam-item jam-pulang">
                                <ion-icon name="log-out-outline"></ion-icon>
                                Pulang: {{ $guru->jam_out ? substr($guru->jam_out, 0, 5) : '-' }}
                            </div>
                        </div>
                    @endif

                    {{-- Keterangan Izin --}}
                    @if(in_array($guru->status, ['i','s','c']) && $guru->keterangan_status)
                        <div class="keterangan-izin">
                            {{ $guru->keterangan_status }}
                        </div>
                    @endif

                    {{-- Tombol WA (hanya untuk belum_hadir) --}}
                    @if($guru->status === 'belum_hadir')
                        <div class="guru-wa-action" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            @php
                                // Simple check: is phone number not dummy?
                                $cleanHp = preg_replace('/[^0-9]/', '', $guru->no_hp ?? '');
                                $isDummy = empty($cleanHp) || strlen($cleanHp) < 9 || in_array($cleanHp, ['123456','1234567','12345678','123456789','1234567890','12345678900']);
                            @endphp

                            @if($isDummy)
                                <div class="wa-no-hp-info">
                                    <ion-icon name="alert-circle-outline"></ion-icon>
                                    No. HP belum dicantumkan
                                </div>
                            @elseif(!$piketPunyaJadwal)
                                <div class="wa-no-hp-info text-danger" style="color: #ef4444; font-weight: 600;">
                                    <ion-icon name="lock-closed-outline"></ion-icon>
                                    Dikunci (Tidak memiliki jam piket hari ini)
                                </div>
                            @else
                                <div id="wa-action-{{ $guru->nik }}" style="display: inline-block;">
                                    @if($guru->wa_sudah_dikirim)
                                        <div class="wa-sent-info">
                                            <ion-icon name="checkmark-circle"></ion-icon>
                                            WA sudah dikirim ({{ $guru->wa_jam_kirim }})
                                        </div>
                                    @else
                                        <button class="btn-wa" onclick="kirimWa('{{ $guru->nik }}', '{{ addslashes($guru->nama_lengkap) }}')">
                                            <ion-icon name="logo-whatsapp"></ion-icon>
                                            Kirim WA Reminder
                                        </button>
                                    @endif
                                </div>

                                @php
                                    $formattedHp = $cleanHp;
                                    if (strpos($formattedHp, '0') === 0) {
                                        $formattedHp = '62' . substr($formattedHp, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $formattedHp }}" target="_blank" class="btn-wa-manual">
                                    <ion-icon name="chatbubbles-outline"></ion-icon>
                                    Chat WA
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@endsection

@push('myscript')
<script>
    // CSRF token for AJAX
    var csrfToken = '{{ csrf_token() }}';
    var piketPunyaJadwal = {{ $piketPunyaJadwal ? 'true' : 'false' }};

    // === Filter ===
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');

            var filter = this.getAttribute('data-filter');
            document.querySelectorAll('.guru-card').forEach(function(card) {
                var status = card.getAttribute('data-status');
                if (filter === 'semua') {
                    card.style.display = '';
                } else if (filter === 'izin') {
                    card.style.display = (status === 'i' || status === 's' || status === 'c') ? '' : 'none';
                } else {
                    card.style.display = (status === filter) ? '' : 'none';
                }
            });
        });
    });

    // === Kirim WA Reminder ===
    function kirimWa(nikGuru, namaGuru) {
        if (!piketPunyaJadwal) {
            Swal.fire({
                title: 'Akses Dibatasi',
                text: 'Anda tidak memiliki jadwal piket pada hari ini! Anda tidak dapat mengklik atau mengirim pesan konfirmasi WA.',
                icon: 'warning',
                confirmButtonText: 'Saya Mengerti'
            });
            return;
        }

        Swal.fire({
            title: 'Kirim WA Reminder?',
            text: 'Kirim pesan WhatsApp ke ' + namaGuru + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#25D366',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                // Disable button
                var actionDiv = document.getElementById('wa-action-' + nikGuru);
                if (actionDiv) {
                    actionDiv.innerHTML = '<div class="wa-sent-info"><ion-icon name="hourglass-outline"></ion-icon> Mengirim...</div>';
                }

                $.ajax({
                    url: '/piket/kirim-wa',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        nik_guru: nikGuru
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            if (actionDiv) {
                                actionDiv.innerHTML = '<div class="wa-sent-info"><ion-icon name="checkmark-circle"></ion-icon> WA sudah dikirim (' + res.jam_kirim + ')</div>';
                            }
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: res.message,
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                            if (actionDiv) {
                                actionDiv.innerHTML = '<button class="btn-wa" onclick="kirimWa(\'' + nikGuru + '\', \'' + namaGuru + '\')"><ion-icon name="logo-whatsapp"></ion-icon> Coba Kirim Lagi</button>';
                            }
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan jaringan.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        if (actionDiv) {
                            actionDiv.innerHTML = '<button class="btn-wa" onclick="kirimWa(\'' + nikGuru + '\', \'' + namaGuru + '\')"><ion-icon name="logo-whatsapp"></ion-icon> Coba Kirim Lagi</button>';
                        }
                    }
                });
            }
        });
    }

    // === Auto-Refresh setiap 30 detik ===
    setInterval(function() {
        $.ajax({
            url: '/piket/monitoring-data',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                // Update ringkasan
                $('#rc-total').text(res.ringkasan.total);
                $('#rc-hadir').text(res.ringkasan.hadir);
                $('#rc-belum').text(res.ringkasan.belum_hadir);
                $('#rc-izin').text(res.ringkasan.izin + res.ringkasan.sakit + res.ringkasan.cuti);

                // Update status per guru card
                res.guru.forEach(function(g) {
                    var card = document.querySelector('.guru-card[data-nik="' + g.nik + '"]');
                    if (!card) return;

                    var oldStatus = card.getAttribute('data-status');
                    if (oldStatus === g.status) {
                        // Update jam jika status hadir
                        if (g.status === 'hadir') {
                            var jamInfo = card.querySelector('.guru-jam-info');
                            if (jamInfo) {
                                var masuk = g.jam_in ? g.jam_in.substring(0,5) : '-';
                                var pulang = g.jam_out ? g.jam_out.substring(0,5) : '-';
                                jamInfo.querySelector('.jam-masuk').innerHTML = '<ion-icon name="log-in-outline"></ion-icon> Masuk: ' + masuk;
                                jamInfo.querySelector('.jam-pulang').innerHTML = '<ion-icon name="log-out-outline"></ion-icon> Pulang: ' + pulang;
                            }
                        }
                        // Update WA status
                        if (g.wa_sudah_dikirim) {
                            var waAction = document.getElementById('wa-action-' + g.nik);
                            if (waAction && !waAction.querySelector('.wa-sent-info')) {
                                waAction.innerHTML = '<div class="wa-sent-info"><ion-icon name="checkmark-circle"></ion-icon> WA sudah dikirim (' + g.wa_jam_kirim + ')</div>';
                            }
                        }
                        return;
                    }

                    // Status berubah! → update card class dan badge
                    card.className = 'guru-card status-' + g.status;
                    card.setAttribute('data-status', g.status);

                    // Update badge
                    var badgeContainer = card.querySelector('.guru-card-top > div:last-child');
                    if (g.status === 'hadir') {
                        badgeContainer.innerHTML = '<span class="status-badge badge-hadir"><ion-icon name="checkmark-circle"></ion-icon> Hadir</span>';
                        // Add jam info
                        var masuk = g.jam_in ? g.jam_in.substring(0,5) : '-';
                        var pulang = g.jam_out ? g.jam_out.substring(0,5) : '-';
                        var existingJamInfo = card.querySelector('.guru-jam-info');
                        if (!existingJamInfo) {
                            var jamHtml = '<div class="guru-jam-info">'
                                + '<div class="jam-item jam-masuk"><ion-icon name="log-in-outline"></ion-icon> Masuk: ' + masuk + '</div>'
                                + '<div class="jam-item jam-pulang"><ion-icon name="log-out-outline"></ion-icon> Pulang: ' + pulang + '</div>'
                                + '</div>';
                            card.insertAdjacentHTML('beforeend', jamHtml);
                        }
                        // Remove WA action wrapper if exists
                        var waAction = document.getElementById('wa-action-' + g.nik);
                        if (waAction) {
                            var parentWa = waAction.closest('.guru-wa-action');
                            if (parentWa) parentWa.remove();
                        }
                    } else if (g.status === 'belum_hadir') {
                        badgeContainer.innerHTML = '<span class="status-badge badge-belum"><ion-icon name="close-circle"></ion-icon> Belum</span>';
                    } else if (g.status === 'i') {
                        badgeContainer.innerHTML = '<span class="status-badge badge-izin"><ion-icon name="document-text"></ion-icon> Izin</span>';
                    } else if (g.status === 's') {
                        badgeContainer.innerHTML = '<span class="status-badge badge-izin"><ion-icon name="medkit"></ion-icon> Sakit</span>';
                    } else if (g.status === 'c') {
                        badgeContainer.innerHTML = '<span class="status-badge badge-izin"><ion-icon name="calendar"></ion-icon> Cuti</span>';
                    }
                });

                // Update filter button counts
                var filterBtns = document.querySelectorAll('.filter-btn');
                filterBtns[0].querySelector('.count').textContent = res.ringkasan.total;
                filterBtns[1].querySelector('.count').textContent = res.ringkasan.belum_hadir;
                filterBtns[2].querySelector('.count').textContent = res.ringkasan.hadir;
                filterBtns[3].querySelector('.count').textContent = (res.ringkasan.izin + res.ringkasan.sakit + res.ringkasan.cuti);
            }
        });
    }, 30000);
</script>
@endpush
