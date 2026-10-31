@extends('layouts.presensi')
@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
    .datepicker-modal { max-height: 100% !important; height: auto !important; }
    .datepicker-date-display { background-color: #0f3a7e !important; }
    .datepicker-container { height: auto !important; }
    .datepicker-controls { position: static !important; }

    .info-box {
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 10px;
        font-size: 12px;
        display: none;
    }
    .info-box.success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
    .info-box.error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
    .info-box.warning { background: #fef9c3; border: 1px solid #fde047; color: #854d0e; }
    .info-box .info-title { font-weight: 700; margin-bottom: 4px; }
    .info-box .info-row   { display: flex; justify-content: space-between; font-size: 11px; }

    .form-control:disabled { background: #f1f5f9; color: #64748b; }

    #loading-cek { display: none; font-size: 11px; color: #94a3b8; margin-top: 5px; }
</style>
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/pengajuan" class="headerButton">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Konfirmasi Kehadiran</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section" style="margin-top: 70px; padding-bottom: 100px;">

    @if(Session::get('error'))
        <div class="alert alert-danger" style="border-radius:8px; font-size:12px;">{{ Session::get('error') }}</div>
    @endif
    @if(Session::get('success'))
        <div class="alert alert-success" style="border-radius:8px; font-size:12px;">{{ Session::get('success') }}</div>
    @endif

    {{-- Laravel Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius:8px; font-size:12px; margin-bottom:10px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="border-radius:14px; border:none; box-shadow:0 2px 10px rgba(0,0,0,0.06);">
        <div class="card-body">

            {{-- Info Workdays --}}
            @if(!empty($hari_kerja))
            <div style="background:#f0fdf4; border-radius:8px; padding:10px 12px; margin-bottom:10px; font-size:11px; color:#166534; border: 1px solid #bbf7d0;">
                <ion-icon name="calendar-outline" style="vertical-align:middle; margin-right:4px;"></ion-icon>
                <strong>Hari Kerja Anda:</strong> {{ implode(', ', $hari_kerja) }}
            </div>
            @else
            <div style="background:#fef2f2; border-radius:8px; padding:10px 12px; margin-bottom:10px; font-size:11px; color:#991b1b; border: 1px solid #fecaca;">
                <ion-icon name="warning-outline" style="vertical-align:middle; margin-right:4px;"></ion-icon>
                Jadwal hari kerja Anda belum dikonfigurasi. Silakan hubungi administrator.
            </div>
            @endif

            {{-- Info Description --}}
            <div style="background:#eff6ff; border-radius:8px; padding:10px 12px; margin-bottom:14px; font-size:11px; color:#1e40af; border: 1px solid #bfdbfe;">
                <ion-icon name="information-circle-outline" style="vertical-align:middle; margin-right:4px;"></ion-icon>
                Pilih tanggal saat Anda lupa melakukan presensi. Anda dapat mengajukan untuk hari ini atau tanggal sebelumnya yang sesuai dengan jadwal kerja Anda.
            </div>

            <form action="/konfirmasikehadiran/store" method="POST" enctype="multipart/form-data" id="frmKonfirmasi">
                @csrf

                {{-- Date --}}
                <div class="form-group">
                    <label style="font-size:11px; color:#64748b; font-weight:600;">Tanggal Presensi</label>
                    <input type="text" id="tanggal_display" class="form-control datepicker"
                           placeholder="Pilih tanggal..." autocomplete="off"
                           style="border-radius:8px; background:#f8fafc;">
                    <input type="hidden" name="tanggal" id="tanggal">
                    <div id="loading-cek">
                        <ion-icon name="reload-outline"></ion-icon> Memverifikasi jadwal...
                    </div>
                </div>

                {{-- Checked Info Box --}}
                <div class="info-box success" id="info-jadwal">
                    <div class="info-title">
                        <ion-icon name="checkmark-circle-outline"></ion-icon> Jadwal Ditemukan
                    </div>
                    <div class="info-row"><span>Hari</span><span id="info-hari" style="font-weight:700;"></span></div>
                </div>

                <div class="info-box error" id="info-tidakada">
                    <div class="info-title"><ion-icon name="close-circle-outline"></ion-icon> Jadwal Tidak Ditemukan</div>
                    <div>Anda tidak memiliki jadwal kerja pada hari tersebut. Silakan pilih tanggal lain.</div>
                </div>

                <div class="info-box error" id="info-sudahabsen">
                    <div class="info-title"><ion-icon name="alert-circle-outline"></ion-icon> Sudah Melakukan Absensi</div>
                    <div>Anda sudah memiliki data presensi untuk tanggal ini.</div>
                </div>

                <div class="info-box warning" id="info-sudahdiajukan">
                    <div class="info-title"><ion-icon name="time-outline"></ion-icon> Sudah Diajukan</div>
                    <div>Anda sudah mengajukan konfirmasi kehadiran untuk tanggal ini (menunggu/disetujui).</div>
                </div>

                <div class="info-box error" id="info-harilibur">
                    <div class="info-title"><ion-icon name="calendar-clear-outline"></ion-icon> Hari Libur</div>
                    <div id="info-harilibur-detail">Tanggal ini dinyatakan sebagai hari libur. Anda tidak dapat mengajukan konfirmasi kehadiran.</div>
                </div>

                {{-- Hidden fields from check result --}}
                <input type="hidden" name="kode_jam_kerja" id="kode_jam_kerja">
                <input type="hidden" name="jam_masuk" id="jam_masuk">
                <input type="hidden" name="jam_keluar" id="jam_keluar">

                {{-- Reason --}}
                <div class="form-group" id="form-alasan" style="display:none;">
                    <label style="font-size:11px; color:#64748b; font-weight:600;">Alasan / Keterangan</label>
                    <textarea name="alasan" id="alasan" class="form-control" rows="3"
                               placeholder="Jelaskan alasan mengapa Anda lupa/tidak melakukan presensi pada tanggal tersebut..."
                               style="border-radius:8px; background:#f8fafc; font-size:13px;"></textarea>
                </div>

                {{-- Upload Evidence --}}
                <div class="form-group" id="form-bukti" style="display:none;">
                    <label style="font-size:11px; color:#64748b; font-weight:600;">Bukti Pendukung (Opsional)</label>
                    <input type="file" name="bukti" class="form-control"
                           accept=".jpg,.jpeg,.png,.pdf"
                           style="border-radius:8px; background:#f8fafc; font-size:12px;">
                    <small class="text-muted">Format JPG/PNG/PDF, maksimal 2MB</small>
                </div>

                {{-- Submit Button --}}
                <div id="form-submit" style="display:none; margin-top:16px;">
                    <button type="submit" class="btn btn-primary btn-block"
                            style="border-radius:30px; font-weight:700; height:46px;"
                            id="btnSubmit">
                        <ion-icon name="send-outline" style="vertical-align:middle;"></ion-icon>
                        Kirim Pengajuan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Confirmation Request History Shortcut --}}
    <div style="margin-top:14px;">
        <a href="/konfirmasikehadiran/riwayat"
           style="display:flex; align-items:center; gap:8px; background:#f8fafc;
                  border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;
                  text-decoration:none; color:#1e293b;">
            <ion-icon name="list-outline" style="font-size:20px; color:#3b82f6;"></ion-icon>
            <div>
                <div style="font-size:13px; font-weight:600;">Riwayat Konfirmasi Kehadiran</div>
                <div style="font-size:10px; color:#64748b;">Lihat status pengajuan Anda</div>
            </div>
            <ion-icon name="chevron-forward-outline" style="margin-left:auto; color:#94a3b8;"></ion-icon>
        </a>
    </div>
</div>
@endsection

@push('myscript')
<script>
$(document).ready(function () {

    // Init Materialize Datepicker – max = today (guru boleh ajukan hari ini juga)
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    $("#tanggal_display").datepicker({
        format: "yyyy-mm-dd",
        maxDate: today,  // ← diubah: sebelumnya yesterday, sekarang today
        i18n: {
            months: ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
            monthsShort: ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
            weekdays: ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
            weekdaysShort: ["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
            weekdaysAbbrev: ["M","S","S","R","K","J","S"]
        },
        onSelect: function(date) {
            // Convert Date object to YYYY-MM-DD string
            var d = new Date(date);
            var year = d.getFullYear();
            var month = ("0" + (d.getMonth() + 1)).slice(-2);
            var day = ("0" + d.getDate()).slice(-2);
            var formatted = year + "-" + month + "-" + day;

            $("#tanggal").val(formatted);
            cekTanggal(formatted);
        }
    });

    function resetForm() {
        $(".info-box").hide();
        $("#form-alasan, #form-bukti, #form-submit").hide();
        $("#kode_jam_kerja, #jam_masuk, #jam_keluar").val('');
    }

    function namaHariDari(tanggal) {
        var hari = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        return hari[new Date(tanggal).getDay()];
    }

    // Workday list from server (for client-side JS validation)
    var hariKerjaGuru = @json($hari_kerja ?? []);

    // Translate day names from server array if they are in Indonesian
    var indToEngDays = {
        'Senin': 'Monday', 'Selasa': 'Tuesday', 'Rabu': 'Wednesday',
        'Kamis': 'Thursday', 'Jumat': 'Friday', 'Sabtu': 'Saturday', 'Minggu': 'Sunday'
    };
    
    hariKerjaGuru = hariKerjaGuru.map(function(day) {
        return indToEngDays[day] || day;
    });

    function cekTanggal(tanggal) {
        if (!tanggal) return;
        resetForm();

        var selectedDate = new Date(tanggal);
        selectedDate.setHours(0, 0, 0, 0);

        var todayCheck = new Date();
        todayCheck.setHours(0, 0, 0, 0);

        // 1. Client-side Validation: tanggal tidak boleh masa depan (lebih dari hari ini)
        if (selectedDate > todayCheck) {
            $("#info-tidakada").find('div:last').html(
                'The date <strong>' + tanggal + '</strong> is in the future. Only today or past dates are allowed.'
            );
            $("#info-tidakada").show();
            return;
        }

        // 2. Client-side Validation: Check workdays
        var hariDipilih = namaHariDari(tanggal);
        if (hariKerjaGuru.length > 0 && hariKerjaGuru.indexOf(hariDipilih) === -1) {
            $("#info-tidakada").find('div:last').html(
                '<strong>' + hariDipilih + '</strong> is not your workday.<br>Your workdays: <strong>' + hariKerjaGuru.join(', ') + '</strong>'
            );
            $("#info-tidakada").show();
            return;
        }

        $("#loading-cek").show();

        $.ajax({
            url: '/konfirmasikehadiran/cek-tanggal',
            type: 'POST',
            data: { _token: "{{ csrf_token() }}", tanggal: tanggal },
            success: function (res) {
                $("#loading-cek").hide();

                // 3. Server-side Validation: Holiday check
                if (res.hari_libur) {
                    var keterangan = res.keterangan_libur || 'Hari Libur';
                    $("#info-harilibur-detail").html(
                        'This date is marked as a holiday: <strong>' + keterangan + '</strong>.<br>You cannot submit attendance confirmation for this date.'
                    );
                    $("#info-harilibur").show();
                    return;
                }

                // 4. Server-side Validation: Already checked in/out/leave/sick
                if (res.sudah_absen) {
                    $("#info-sudahabsen").show();
                    return;
                }

                // 5. Server-side Validation: Already submitted confirmation for this date
                if (res.sudah_diajukan) {
                    $("#info-sudahdiajukan").show();
                    return;
                }

                // 5. Server-side Validation: No work schedule configured
                if (!res.jam_kerja) {
                    var hariValid = res.hari_kerja && res.hari_kerja.length
                        ? 'Your workdays: <strong>' + res.hari_kerja.map(d => indToEngDays[d] || d).join(', ') + '</strong>'
                        : 'Your workday schedule has not been configured yet.';
                    $("#info-tidakada").find('div:last').html(
                        '<strong>' + namaHariDari(tanggal) + '</strong> is not your workday.<br>' + hariValid
                    );
                    $("#info-tidakada").show();
                    return;
                }

                // Show form if all valid
                var jk = res.jam_kerja;
                $("#info-hari").text(namaHariDari(tanggal));
                $("#info-jammasuk").text(jk.jam_masuk);
                $("#info-jampulang").text(jk.jam_pulang);
                
                $("#kode_jam_kerja").val(jk.kode_jam_kerja);
                $("#jam_masuk").val(jk.jam_masuk);
                $("#jam_keluar").val(jk.jam_pulang);

                $("#info-jadwal").show();
                $("#form-alasan, #form-bukti, #form-submit").show();
            },
            error: function () {
                $("#loading-cek").hide();
                alert("Failed to verify data. Please try again.");
            }
        });
    }

    // Validate before submit
    $("#frmKonfirmasi").submit(function (e) {
        var alasan = $.trim($("#alasan").val());
        if (alasan.length < 10) {
            e.preventDefault();
            Swal.fire({ title: 'Oops!', text: 'Reason must be at least 10 characters.', icon: 'warning' });
            return false;
        }

        // Anti double-submit
        if ($(this).data('submitted') === true) {
            e.preventDefault();
            return false;
        }
        $(this).data('submitted', true);
        $("#btnSubmit").prop('disabled', true).html('<ion-icon name="hourglass-outline" style="vertical-align:middle;"></ion-icon> Sending...');
    });
});
</script>
@endpush