<!-- App Bottom Menu -->
<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ request()->is('dashboard') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="home-outline"></ion-icon>
            <strong>Beranda</strong>
        </div>
    </a>
    <a href="/presensi/histori" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="time-outline" role="img" class="md hydrated" aria-label="time outline"></ion-icon>
            <strong>Riwayat</strong>
        </div>
    </a>

    @if (Auth::guard('karyawan')->user()->status_jam_kerja == 1)
        <a href="/presensi/null/create" class="item ">
            <div class="col">
                <div class="action-button large">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
            </div>
        </a>
    @else
        <a href="/presensi/pilihjamkerja" class="item ">
            <div class=     "col">
                <div class="action-button large">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
            </div>
        </a>
    @endif

    <a href="/pengajuan" class="item {{ request()->is('pengajuan') || request()->is('presensi/izin') || request()->is('izinabsen') || request()->is('izincuti') || request()->is('izinsakit') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="document-text-outline"></ion-icon>
            <strong>Pengajuan</strong>
        </div>
    </a>
    <a href="/editprofile" class="item {{ request()->is('editprofile') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
            <strong>Profil</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu -->
