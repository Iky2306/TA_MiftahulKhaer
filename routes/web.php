<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\IzinabsenController;
use App\Http\Controllers\IzincutiController;
use App\Http\Controllers\IzinsakitController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\KonfirmasiKehadiranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\MasterKelasController;
use App\Http\Controllers\MasterSiswaController;
use App\Http\Controllers\PayrollController;
// use App\Http\Controllers\FakeGPSController;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Psy\VarDumper\Presenter;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\LaporanController;


// Route::resource('Fakegps', FakeGPSController::class); 
Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});


Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');

    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);
    Route::post('/ai/chat/send', [\App\Http\Controllers\AiChatController::class, 'send']);

    //Presensi
    Route::get('/presensi/{kode_jam_kerja}/create', [PresensiController::class, 'create']);
    Route::get('/presensi/pilihjamkerja', [PresensiController::class, 'pilihjamkerja']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
    Route::get('/presensi/scanqr', [PresensiController::class, 'scanqr']);
    Route::post('/presensi/storeqr', [PresensiController::class, 'storeqr']);
    //Edit Profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class, 'updateprofile']);
    Route::post('/presensi/storeface', [PresensiController::class, 'storeFaceDescriptor']);

    //Histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    //Pengajuan (landing page)
    Route::get('/pengajuan', [PresensiController::class, 'pengajuan']);

    // Konfirmasi Kehadiran (Guru)
    Route::get('/konfirmasikehadiran', [KonfirmasiKehadiranController::class, 'create']);
    Route::post('/konfirmasikehadiran/cek-tanggal', [KonfirmasiKehadiranController::class, 'cekTanggal']);
    Route::post('/konfirmasikehadiran/store', [KonfirmasiKehadiranController::class, 'store']);
    Route::get('/konfirmasikehadiran/riwayat', [KonfirmasiKehadiranController::class, 'riwayat']);

    //Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin']);


    //Izin Absen

    Route::get('/izinabsen', [IzinabsenController::class, 'create']);
    Route::post('/izinabsen/store', [IzinabsenController::class, 'store']);
    Route::get('/izinabsen/{kode_izin}/edit', [IzinabsenController::class, 'edit']);
    Route::post('/izinabsen/{kode_izin}/update', [IzinabsenController::class, 'update']);
    //Izin Sakit
    Route::get('/izinsakit', [IzinsakitController::class, 'create']);
    Route::post('/izinsakit/store', [IzinsakitController::class, 'store']);
    Route::get('/izinsakit/{kode_izin}/edit', [IzinsakitController::class, 'edit']);
    Route::post('/izinsakit/{kode_izin}/update', [IzinsakitController::class, 'update']);
    //Izin Cuti
    Route::get('/izincuti', [IzincutiController::class, 'create']);
    Route::post('/izincuti/store', [IzincutiController::class, 'store']);
    Route::get('/izincuti/{kode_izin}/edit', [IzincutiController::class, 'edit']);
    Route::post('/izincuti/{kode_izin}/update', [IzincutiController::class, 'update']);
    Route::post('/izincuti/getmaxcuti', [IzincutiController::class, 'getmaxcuti']);

    Route::get('/izin/{kode_izin}/showact', [PresensiController::class, 'showact']);
    Route::get('/izin/{kode_izin}/delete', [PresensiController::class, 'deleteizin']);

    //Slip Gaji
    Route::get('/presensi/slipgaji', [PresensiController::class, 'slipgaji']);
    Route::get('/presensi/downloadslipgaji', [PresensiController::class, 'downloadSlipGaji']);

    //Rekap Absensi Bulanan Guru
    Route::get('/presensi/rekapabsensi', [PresensiController::class, 'rekapabsensiguru']);
    Route::get('/presensi/downloadrekapabsensi', [PresensiController::class, 'downloadrekapabsensiguru']);

    // Jurnal Mengajar
    Route::get('/guru/jurnal', [JurnalController::class, 'index']);
    Route::get('/guru/jurnal/create', [JurnalController::class, 'create']);
    Route::post('/guru/jurnal/scan-process', [JurnalController::class, 'scanProcess']);
    Route::post('/guru/jurnal/store', [JurnalController::class, 'store']);
    Route::get('/guru/jurnal/cetak-pdf', [JurnalController::class, 'cetakPdf']);
    Route::get('/guru/jurnal/{id}/show', [JurnalController::class, 'show']);

    // Monitoring Wali Kelas (Kelasku Hub & Sub-Menu)
    Route::get('/guru/walikelas/monitoring', [WaliKelasController::class, 'index']);
    Route::get('/guru/walikelas/jurnal', [WaliKelasController::class, 'jurnal']);
    Route::get('/guru/walikelas/siswa', [WaliKelasController::class, 'siswa']);
    Route::get('/guru/walikelas/siswa/export-pdf', [WaliKelasController::class, 'exportSiswaPdf']);
    Route::get('/guru/walikelas/siswa/export-excel', [WaliKelasController::class, 'exportSiswaExcel']);

    // Fallback GET → redirect (hindari error 405 di WebView saat tekan back)
    Route::get('/guru/jurnal/scan-process', fn() => redirect('/guru/jurnal/create'));
    Route::get('/guru/jurnal/store', fn() => redirect('/guru/jurnal'));

    // Ruang Diskusi Guru (Live Chat)
    Route::get('/chat', [App\Http\Controllers\ChatMessageController::class, 'index']);
    Route::get('/chat/fetch', [App\Http\Controllers\ChatMessageController::class, 'fetch']);
    Route::post('/chat/store', [App\Http\Controllers\ChatMessageController::class, 'store']);
    Route::post('/chat/destroy/{id}', [App\Http\Controllers\ChatMessageController::class, 'destroy']);
    Route::post('/chat/mark-as-read', [App\Http\Controllers\ChatMessageController::class, 'markAsRead']);
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatMessageController::class, 'getUnreadCount']);

    // Status Profil Guru
    Route::get('/guru/status-profil', [GuruController::class, 'statusProfil'])->name('guru.status.profil');
    Route::post('/guru/status-profil/update-mapel', [GuruController::class, 'updateMapel'])->name('guru.status.profil.update-mapel');

    // ==================== PETUGAS PIKET ====================
    Route::get('/piket/monitoring', [App\Http\Controllers\PiketController::class, 'monitoring'])->name('piket.monitoring');
    Route::get('/piket/monitoring-data', [App\Http\Controllers\PiketController::class, 'monitoringData'])->name('piket.monitoring.data');
    Route::post('/piket/kirim-wa', [App\Http\Controllers\PiketController::class, 'kirimWaReminder'])->name('piket.kirim_wa');
});


// Public Quick Approval WA Routes
Route::get('/quick-approve/izin/{token}', [PresensiController::class, 'confirmQuickApproveIzin']);
Route::post('/quick-approve/izin/{token}', [PresensiController::class, 'quickApproveIzin']);
Route::get('/quick-approve/kehadiran/{token}', [PresensiController::class, 'confirmQuickApproveKehadiran']);
Route::post('/quick-approve/kehadiran/{token}', [PresensiController::class, 'quickApproveKehadiran']);


//Route Yang Bisa di AKses Oleh Administrator dan Staff
Route::group(['middleware' => ['auth:user', 'role:administrator|staff,user']], function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //Presensi
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::match(['get', 'post'], '/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::match(['get', 'post'], '/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);


    /** ==================== PAYROLL GURU ==================== **/
    Route::prefix('payroll/guru')->name('payroll.guru.')->group(function () {
        Route::get('/jabatan', [PayrollController::class, 'dataJabatanGuru'])->name('jabatan');
        Route::get('/seting-gaji', [PayrollController::class, 'setingGajiGuru'])->name('setingGaji');
        Route::post('/seting-gaji', [PayrollController::class, 'simpanSetingGajiGuru'])->name('setingGaji.simpan');
        Route::get('/data', [PayrollController::class, 'dataGuru'])->name('data');
        Route::post('/store', [PayrollController::class, 'storeGuru'])->name('store');
        Route::delete('/delete/{id}', [PayrollController::class, 'deleteGuru'])->name('delete');
        Route::get('/edit/{id}', [PayrollController::class, 'editGuru'])->name('edit');
        Route::put('/update/{id}', [PayrollController::class, 'updateGuru'])->name('update');
        Route::get('/cetak', [PayrollController::class, 'cetakRekapGajiGuru'])->name('cetak');
        Route::get('/cetak-slip', [PayrollController::class, 'cetakSlip'])->name('cetak.slip');
        Route::get('/download-pdf', [PayrollController::class, 'downloadPdf'])->name('download.pdf');
        Route::get('/download-excel', [PayrollController::class, 'downloadExcel'])->name('download.excel');
    });
    Route::get('/rekap', [PayrollController::class, 'rekapGajiGuru'])->name('payroll.guru.rekap');

    //Departemen / Jabatan
    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::post('/departemen/store', [DepartemenController::class, 'store']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete']);

    /** ==================== PAYROLL PENGABDIAN ==================== **/
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/pengabdian', [PayrollController::class, 'pengabdianData'])->name('pengabdian.data_pengabdian');
        Route::post('/pengabdian/store', [PayrollController::class, 'pengabdianStore'])->name('pengabdian.store');
        Route::get('/pengabdian/edit/{id}', [PayrollController::class, 'pengabdianEdit'])->name('pengabdian.edit');
        Route::put('/pengabdian/update/{id}', [PayrollController::class, 'pengabdianUpdate'])->name('pengabdian.update');
        Route::delete('/pengabdian/delete/{id}', [PayrollController::class, 'pengabdianDelete'])->name('pengabdian.delete');
        Route::get('/pengabdian/rekap', [PayrollController::class, 'rekapPengabdian'])->name('pengabdian.rekap');
        Route::get('/pengabdian/cetak', [PayrollController::class, 'cetakPengabdian'])->name('pengabdian.cetak');
        Route::get('/pengabdian/slipgaji', [PayrollController::class, 'slipgajiPengabdian'])->name('pengabdian.slipgaji');
        Route::get('/pengabdian/pdf', [PayrollController::class, 'pdfPengabdian'])->name('pengabdian.pdf');
        Route::get('/pengabdian/excel', [PayrollController::class, 'excelPengabdian'])->name('pengabdian.excel');
    });

    Route::post('/payroll/hitung', [PayrollController::class, 'hitungGaji'])->name('payroll.hitung');
    Route::post('/payroll/rekapbulanan', [PayrollController::class, 'rekapBulanan'])->name('payroll.rekapbulanan');

    //Pengumuman
    Route::get('/pengumuman', [\App\Http\Controllers\PengumumanController::class, 'index']);
    Route::post('/pengumuman/store', [\App\Http\Controllers\PengumumanController::class, 'store']);
    Route::post('/pengumuman/edit', [\App\Http\Controllers\PengumumanController::class, 'edit']);
    Route::post('/pengumuman/{id}/update', [\App\Http\Controllers\PengumumanController::class, 'update']);
    Route::post('/pengumuman/{id}/delete', [\App\Http\Controllers\PengumumanController::class, 'delete']);
    Route::get('/pengumuman/{id}/setstatus', [\App\Http\Controllers\PengumumanController::class, 'setstatus']);
    // Ganti Password Admin/Staff
    Route::get('/panel/gantipassword', [UserController::class, 'gantiPasswordForm'])->name('admin.ganti_password');
    Route::post('/panel/gantipassword', [UserController::class, 'updatePassword'])->name('admin.update_password');
});


//ROute yang Hanya bisa di akses oleh Administrator
Route::group(['middleware' => ['auth:user', 'role:administrator,user']], function () {

    //Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::get('/karyawan/{nik}/resetpassword', [KaryawanController::class, 'resetpassword']);
    Route::get('/karyawan/{nik}/resetfaceid', [KaryawanController::class, 'resetfaceid']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update']);
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete']);
    Route::get('/karyawan/{nik}/lockandunlocklocation', [KaryawanController::class, 'lockandunlocklocation']);
    Route::get('/karyawan/{nik}/lockandunlockjamkerja', [KaryawanController::class, 'lockandunlockjamkerja']);
    Route::get('/karyawan/{nik}/lockandunlockfaceid', [KaryawanController::class, 'lockandunlockfaceid']);

    //Konfigruasi Jam Kerja
    Route::get('/konfigurasi/{nik}/setjamkerja', [KonfigurasiController::class, 'setjamkerja']);
    Route::post('/konfigurasi/storesetjamkerja', [KonfigurasiController::class, 'storesetjamkerja']);
    Route::post('/konfigurasi/updatesetjamkerja', [KonfigurasiController::class, 'updatesetjamkerja']);
    Route::post('/konfigurasi/storesetjamkerjabydate', [KonfigurasiController::class, 'storesetjamkerjabydate']);
    Route::get('/konfigurasi/{nik}/{bulan}/{tahun}/getjamkerjabydate', [KonfigurasiController::class, 'getjamkerjabydate']);
    Route::post('/konfigurasi/deletejamkerjabydate', [KonfigurasiController::class, 'deletejamkerjabydate']);

    //Presensi Monitoring
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta']);
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit']);

    Route::post('/koreksipresensi', [PresensiController::class, 'koreksipresensi']);
    Route::post('/storekoreksipresensi', [PresensiController::class, 'storekoreksipresensi']);
    
    // Master Kelas & Jurnal
    Route::get('/panel/masterkelas', [MasterKelasController::class, 'index']);
    Route::get('/panel/masterkelas/generate-code', [MasterKelasController::class, 'generateCode']);
    Route::post('/panel/masterkelas/store', [MasterKelasController::class, 'store']);
    Route::post('/panel/masterkelas/update', [MasterKelasController::class, 'update']);
    Route::post('/panel/masterkelas/{kode_kelas}/delete', [MasterKelasController::class, 'delete']);
    Route::get('/panel/masterkelas/{kode_kelas}/mapel', [MasterKelasController::class, 'mapel']);
    Route::get('/panel/masterkelas/mapel/template', [MasterKelasController::class, 'downloadTemplateMapel']);
    Route::post('/panel/masterkelas/{kode_kelas}/mapel/store', [MasterKelasController::class, 'storeMapel']);
    Route::post('/panel/masterkelas/{kode_kelas}/mapel/import', [MasterKelasController::class, 'importMapel']);
    Route::post('/panel/masterkelas/mapel/{id}/delete', [MasterKelasController::class, 'deleteMapel']);
    Route::get('/panel/masterkelas/{kode_kelas}/preview-qr', [MasterKelasController::class, 'showQrPreview']);
    
    // Monitoring Kelas
    Route::get('/panel/monitoringkelas', [MasterKelasController::class, 'monitoring']);

    // Master Siswa
    Route::get('/panel/mastersiswa', [MasterSiswaController::class, 'index']);
    Route::get('/panel/mastersiswa/get-by-kelas/{kode_kelas}', [MasterSiswaController::class, 'getSiswaByKelas']);
    Route::get('/panel/mastersiswa/count-by-kelas/{kode_kelas}', [MasterSiswaController::class, 'countSiswaByKelas']);
    Route::get('/panel/mastersiswa/export-excel', [MasterSiswaController::class, 'exportExcel']);
    Route::get('/panel/mastersiswa/export-pdf', [MasterSiswaController::class, 'exportPdf']);
    Route::post('/panel/mastersiswa/store', [MasterSiswaController::class, 'store']);
    Route::post('/panel/mastersiswa/update', [MasterSiswaController::class, 'update']);
    Route::post('/panel/mastersiswa/bulk-promote', [MasterSiswaController::class, 'bulkPromote']);
    Route::post('/panel/mastersiswa/{id}/delete', [MasterSiswaController::class, 'delete']);
    Route::get('/panel/mastersiswa/template', [MasterSiswaController::class, 'downloadTemplate']);
    Route::post('/panel/mastersiswa/import', [MasterSiswaController::class, 'import']);

    // Laporan Jurnal (Administrator)
    Route::get('/panel/jurnal/laporan', [PresensiController::class, 'laporanjurnal'])->name('jurnal.laporan');
    Route::post('/panel/jurnal/cetaklaporan', [PresensiController::class, 'cetaklaporanjurnal'])->name('jurnal.cetaklaporan');

    // Konfirmasi Kehadiran (Admin)
    Route::get('/panel/konfirmasikehadiran', [KonfirmasiKehadiranController::class, 'adminIndex']);
    Route::post('/panel/konfirmasikehadiran/{id}/approve', [KonfirmasiKehadiranController::class, 'approve']);
    Route::post('/panel/konfirmasikehadiran/{id}/reject', [KonfirmasiKehadiranController::class, 'reject']);

    //Presensi

    Route::post('/presensi/approveizinsakit', [PresensiController::class, 'approveizinsakit']);
    Route::get('/presensi/{kode_izin}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit']);


    //Cabang
    Route::get('/cabang', [CabangController::class, 'index']);
    Route::post('/cabang/store', [CabangController::class, 'store']);
    Route::post('/cabang/edit', [CabangController::class, 'edit']);
    Route::post('/cabang/update', [CabangController::class, 'update']);
    Route::post('/cabang/{kode_cabang}/delete', [CabangController::class, 'delete']);

    //Konfigurasi

    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);

    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/storejamkerja', [KonfigurasiController::class, 'storejamkerja']);
    Route::post('/konfigurasi/editjamkerja', [KonfigurasiController::class, 'editjamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);

    Route::post('/konfigurasi/jamkerja/{kode_jam_kerja}/delete', [KonfigurasiController::class, 'deletejamkerja']);


    Route::get('/konfigurasi/jamkerjadept', [KonfigurasiController::class, 'jamkerjadept']);
    Route::get('/konfigurasi/jamkerjadept/create', [KonfigurasiController::class, 'createjamkerjadept']);
    Route::post('/konfigurasi/jamkerjadept/store', [KonfigurasiController::class, 'storejamkerjadept']);
    Route::get('/konfigurasi/jamkerjadept/{kode_jk_dept}/edit', [KonfigurasiController::class, 'editjamkerjadept']);
    Route::post('/konfigurasi/jamkerjadept/{kode_jk_dept}/update', [KonfigurasiController::class, 'updatejamkerjadept']);
    Route::get('/konfigurasi/jamkerjadept/{kode_jk_dept}/show', [KonfigurasiController::class, 'showjamkerjadept']);
    Route::get('/konfigurasi/jamkerjadept/{kode_jk_dept}/delete', [KonfigurasiController::class, 'deletejamkerjadept']);

    // Konfigurasi Tema Dashboard & Leaderboard
    Route::get('/konfigurasi/temadashboard', [KonfigurasiController::class, 'temaDashboard']);
    Route::post('/konfigurasi/updatetemadashboard', [KonfigurasiController::class, 'updateTemaDashboard']);
    Route::get('/konfigurasi/leaderboard', [KonfigurasiController::class, 'leaderboard']);
    Route::post('/konfigurasi/updateleaderboard', [KonfigurasiController::class, 'updateLeaderboard']);
    Route::get('/konfigurasi/wagateway', [KonfigurasiController::class, 'waGateway']);
    Route::post('/konfigurasi/updatewagateway', [KonfigurasiController::class, 'updateWaGateway']);

    //User
    Route::get('/konfigurasi/admins', [UserController::class, 'index']);
    Route::post('/konfigurasi/admins/store', [UserController::class, 'store']);
    Route::post('/konfigurasi/admins/edit', [UserController::class, 'edit']);
    Route::post('/konfigurasi/admins/{id_user}/update', [UserController::class, 'update']);
    Route::post('/konfigurasi/admins/{id_user}/delete', [UserController::class, 'delete']);


    //Harilibur

    Route::get('/konfigurasi/harilibur', [HariliburController::class, 'index']);
    Route::get('/konfigurasi/harilibur/create', [HariliburController::class, 'create']);
    Route::post('/konfigurasi/harilibur/store', [HariliburController::class, 'store']);
    Route::post('/konfigurasi/harilibur/edit', [HariliburController::class, 'edit']);
    Route::post('/konfigurasi/harilibur/{kode_libur}/update', [HariliburController::class, 'update']);
    Route::post('/konfigurasi/harilibur/{kode_libur}/delete', [HariliburController::class, 'delete']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/setkaryawanlibur', [HariliburController::class, 'setkaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/setlistkaryawanlibur', [HariliburController::class, 'setlistkaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/getsetlistkaryawanlibur', [HariliburController::class, 'getsetlistkaryawanlibur']);
    Route::post('/konfigurasi/harilibur/storekaryawanlibur', [HariliburController::class, 'storekaryawanlibur']);
    Route::post('/konfigurasi/harilibur/removekaryawanlibur', [HariliburController::class, 'removekaryawanlibur']);
    Route::get('/konfigurasi/harilibur/{kode_libur}/getkaryawanlibur', [HariliburController::class, 'getkaryawanlibur']);
    //Cuti

    Route::get('/cuti', [CutiController::class, 'index']);
    Route::post('/cuti/store', [CutiController::class, 'store']);
    Route::post('/cuti/edit', [CutiController::class, 'edit']);
    Route::post('/cuti/{kode_cuti}/update', [CutiController::class, 'update']);
    Route::post('/cuti/{kode_cuti}/delete', [CutiController::class, 'delete']);
});


Route::get('/createrolepermission', function () {

    try {
        Role::create(['name' => 'staff']);
        // Permission::create(['name' => 'view-karyawan']);
        // Permission::create(['name' => 'view-departemen']);
        echo "Sukses";
    } catch (\Exception $e) {
        echo "Error";
    }
});


Route::get('/give-user-role', function () {
    try {
        $user = User::findorfail(1);
        $user->assignRole('administrator');
        echo "Sukses";
    } catch (\Exception $e) {
        //throw $th;
        echo "Error";
    }
});


Route::get('/give-role-permission', function () {
    try {
        $role = Role::findorfail(1);
        $role->givePermissionTo('view-departemen');
        echo "Sukses";
    } catch (\Exception $e) {
        //throw $th;
        echo "Error";
    }
});
Route::get('/guru/jadwal-mengajar', [GuruController::class, 'jadwalMengajar'])->name('guru.jadwal.mengajar');
Route::middleware(['auth:karyawan'])->group(function() {
    Route::get('/laporanguru', [LaporanController::class, 'index'])->name('laporan.guru');
    Route::post('/laporanguru/cetak', [LaporanController::class, 'cetak'])->name('laporan.guru.cetak');
});


