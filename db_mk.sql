-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2026 at 03:54 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mk`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_dept` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_cabang` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `email_verified_at`, `password`, `kode_dept`, `kode_cabang`, `remember_token`, `created_at`, `updated_at`) VALUES
(22, 'Miftahul Khaer', 'admin@miftahulkhaer.sch.id', NULL, '$2y$10$WvxpG7wUJp2tN8Axd9x0a.jpQkR/Ufs7tZ6Hzqdm4BV8BeyC7q/EK', 'ADM', 'PST', NULL, '2025-06-25 10:27:19', '2026-08-02 13:18:12'),
(36, 'Staff Pusat', 'staffpst@miftahulkhaer.sch.id', NULL, '$2y$10$ptu/08vuME4hnmKptwRISeQTrf2t3O3yQ/rahg1Z4DIoVkempkV.6', NULL, 'PST', NULL, '2026-02-17 16:14:22', '2026-02-17 16:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `kode_cabang` char(3) NOT NULL,
  `nama_cabang` varchar(50) NOT NULL,
  `lokasi_cabang` varchar(255) NOT NULL,
  `radius_cabang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`kode_cabang`, `nama_cabang`, `lokasi_cabang`, `radius_cabang`) VALUES
('PST', 'Pusat', '-6.3132417, 106.5184817', 500);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply_to_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `nik`, `message`, `file`, `reply_to_id`, `created_at`, `updated_at`) VALUES
(1, 'PST260817', 'pppp', NULL, NULL, '2026-10-04 11:12:23', '2026-10-04 11:12:23');

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `kode_dept` char(3) NOT NULL,
  `nama_dept` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`kode_dept`, `nama_dept`) VALUES
('GRU', 'Guru'),
('KPS', 'Kepala Sekolah'),
('TU', 'Tata Usaha');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fake_g_p_s`
--

CREATE TABLE `fake_g_p_s` (
  `id` bigint UNSIGNED NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` bigint UNSIGNED NOT NULL,
  `karyawan_id` varchar(50) DEFAULT NULL,
  `karyawan_nik` char(18) NOT NULL,
  `lokasi_sekolah` enum('Pusat','Cabang') NOT NULL DEFAULT 'Pusat',
  `jabatan` varchar(100) NOT NULL,
  `tunjangan_jabatan` bigint DEFAULT '0',
  `jam_mengajar` int NOT NULL,
  `potongan` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `golongan` tinyint DEFAULT '1',
  `gelombang` enum('Gelombang 1','Gelombang 2','Gelombang 3') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `karyawan_id`, `karyawan_nik`, `lokasi_sekolah`, `jabatan`, `tunjangan_jabatan`, `jam_mengajar`, `potongan`, `created_at`, `updated_at`, `golongan`, `gelombang`) VALUES
(1, NULL, 'PST260817', 'Pusat', 'Guru', 150000, 23, 0, '2026-08-17 09:13:07', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `harilibur`
--

CREATE TABLE `harilibur` (
  `kode_libur` char(7) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_libur` date NOT NULL,
  `tanggal_libur_akhir` date NOT NULL,
  `kode_cabang` char(3) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `harilibur`
--

INSERT INTO `harilibur` (`kode_libur`, `tanggal_libur`, `tanggal_libur_akhir`, `kode_cabang`, `keterangan`) VALUES
('LB26001', '2026-10-07', '2026-10-10', 'PST', 'HARI LIBUR');

-- --------------------------------------------------------

--
-- Table structure for table `harilibur_detail`
--

CREATE TABLE `harilibur_detail` (
  `kode_libur` char(7) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nik` char(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `jam_kerja`
--

CREATE TABLE `jam_kerja` (
  `kode_jam_kerja` char(4) NOT NULL,
  `nama_jam_kerja` varchar(15) NOT NULL,
  `awal_jam_masuk` time NOT NULL,
  `jam_masuk` time NOT NULL,
  `akhir_jam_masuk` time NOT NULL,
  `awal_jam_istirahat` time DEFAULT NULL,
  `akhir_jam_istirahat` time DEFAULT NULL,
  `status_istirahat` char(1) DEFAULT NULL,
  `jam_pulang` time NOT NULL,
  `total_jam` smallint NOT NULL,
  `lintashari` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `jam_kerja`
--

INSERT INTO `jam_kerja` (`kode_jam_kerja`, `nama_jam_kerja`, `awal_jam_masuk`, `jam_masuk`, `akhir_jam_masuk`, `awal_jam_istirahat`, `akhir_jam_istirahat`, `status_istirahat`, `jam_pulang`, `total_jam`, `lintashari`) VALUES
('JK01', 'SENIN - KAMIS', '07:00:00', '07:00:00', '12:40:00', NULL, NULL, '0', '12:40:00', 6, '0'),
('JK02', 'JUMAT', '07:00:00', '07:00:00', '10:40:00', NULL, NULL, '0', '10:40:00', 4, '0'),
('JK03', 'SABTU-MINGGU', '18:00:00', '18:00:00', '00:00:00', NULL, NULL, '0', '00:00:00', 6, '0');

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_absensi_siswa`
--

CREATE TABLE `jurnal_absensi_siswa` (
  `id` int NOT NULL,
  `jurnal_id` int NOT NULL,
  `nis` varchar(20) NOT NULL,
  `status` enum('H','S','I','A') NOT NULL DEFAULT 'H',
  `keterangan` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_mengajar`
--

CREATE TABLE `jurnal_mengajar` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time DEFAULT NULL,
  `kode_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materi` text COLLATE utf8mb4_unicode_ci,
  `siswa_absen` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurnal_mengajar`
--

INSERT INTO `jurnal_mengajar` (`id`, `nik`, `tanggal`, `jam_mulai`, `jam_selesai`, `kode_kelas`, `nama_mapel`, `materi`, `siswa_absen`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'PST260817', '2026-08-17', '17:04:00', '17:11:00', '001-SMK-001', 'Informatika', 'Ms. Office', NULL, NULL, '2026-08-17 10:05:36', '2026-08-17 10:05:36'),
(2, 'PST260817', '2026-10-04', '18:08:00', '19:07:00', '001-SMK-001', 'Bahasa Inggris', 'tidur', NULL, 'PST260817-1791112185.png', '2026-10-04 11:09:45', '2026-10-04 11:09:45');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `nik` char(18) NOT NULL DEFAULT '0',
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `tipe_jabatan` enum('guru','petugas_piket','wali_kelas') NOT NULL DEFAULT 'guru',
  `no_hp` varchar(13) NOT NULL,
  `foto` varchar(30) DEFAULT NULL,
  `kode_dept` char(3) NOT NULL,
  `kode_cabang` char(3) DEFAULT NULL,
  `status_location` char(1) NOT NULL DEFAULT '1',
  `status_jam_kerja` char(1) NOT NULL DEFAULT '1',
  `status_face_id` char(1) NOT NULL DEFAULT '1',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `face_descriptor` text,
  `face_updated_at` timestamp NULL DEFAULT NULL,
  `last_chat_view_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`nik`, `nama_lengkap`, `jabatan`, `tipe_jabatan`, `no_hp`, `foto`, `kode_dept`, `kode_cabang`, `status_location`, `status_jam_kerja`, `status_face_id`, `password`, `remember_token`, `face_descriptor`, `face_updated_at`, `last_chat_view_at`) VALUES
('PST260817', 'Putriana Dyah Anjani', 'Konsentrasi Keahlian', 'guru', '089507291510', 'PST260817.jpg', 'GRU', 'PST', '0', '0', '0', '$2y$10$gPVL8SVv9AyWPp31hVz6y.ioXNi1T2A1kni.Go8efRyqNvaedlvIO', 'A8rfZBWGdqCzvjbJGuzDtLdmHFhFA63ZuKcBDHTM43I36LxueLYLVwpO3erS', '[-0.1939941942691803,0.08438944816589355,0.09720531105995178,-0.009245852008461952,0.004326545633375645,-0.12249153107404709,0.025407815352082253,-0.089872807264328,0.13070549070835114,-0.015825675800442696,0.2716212570667267,-0.026758071035146713,-0.1681409478187561,-0.1911448836326599,0.15615177154541016,0.1234140396118164,-0.20412945747375488,-0.1457548886537552,-0.0365246906876564,-0.11068138480186462,0.06437594443559647,-0.05027439817786217,0.017655910924077034,0.10814932733774185,-0.1339368373155594,-0.3204006850719452,-0.0780656486749649,-0.17770224809646606,0.08996772021055222,-0.07414311915636063,-0.024218618869781494,0.07259956002235413,-0.2237963229417801,-0.08340738713741302,-0.02952463924884796,0.12626561522483826,0.021424271166324615,-0.05437343567609787,0.16187386214733124,-0.03355824947357178,-0.12856057286262512,-0.04365742206573486,-0.025565780699253082,0.22528186440467834,0.13329778611660004,0.026124853640794754,0.07705877721309662,0.054027363657951355,0.03157806769013405,-0.18081305921077728,0.024762989953160286,0.10692452639341354,0.15771354734897614,0.05092183127999306,0.050853390246629715,-0.21631476283073425,0.034480199217796326,0.017938435077667236,-0.19424816966056824,0.047940902411937714,0.0024546505883336067,-0.13272948563098907,-0.009867580607533455,0.03487398847937584,0.26156556606292725,0.09857659041881561,-0.04123351350426674,-0.09765183180570602,0.18744871020317078,-0.06427823752164841,-0.036019906401634216,0.1422126293182373,-0.11826557666063309,-0.1543768048286438,-0.2332804799079895,0.08060341328382492,0.36797136068344116,0.0741603747010231,-0.24891428649425507,0.013192014768719673,-0.1594078093767166,-0.024242151528596878,0.04752768576145172,0.023540016263723373,-0.0639851987361908,0.06816409528255463,-0.21339134871959686,0.026364797726273537,0.17452195286750793,0.006551982369273901,-0.0017407096456736326,0.21310721337795258,0.033195268362760544,0.01780112460255623,0.04401403293013573,-0.025941990315914154,-0.004439958371222019,0.00316808233037591,-0.08129788190126419,-0.08047517389059067,0.10373794287443161,-0.08150035887956619,-0.05450756102800369,0.0658058151602745,-0.2232273668050766,0.08535211533308029,0.043030064553022385,-0.03324628621339798,-0.10813245922327042,0.023202138021588326,-0.07677296549081802,-0.010638085193932056,0.11187049746513367,-0.27312034368515015,0.17867441475391388,0.24460981786251068,-0.003908584825694561,0.1727433055639267,-0.021823517978191376,0.046892471611499786,-0.035448357462882996,-0.12507237493991852,-0.12166879326105118,-0.04191876947879791,0.09341219067573547,0.017845459282398224,0.028723306953907013,0.07110302895307541]', '2026-08-17 09:52:22', '2026-10-04 11:12:17');

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi_app`
--

CREATE TABLE `konfigurasi_app` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `konfigurasi_app`
--

INSERT INTO `konfigurasi_app` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'wa_gateway_token', '94DqURWTrqtFD9PG5cJP', '2026-10-04 10:48:08', '2026-10-04 10:48:08'),
(2, 'wa_gateway_admins', '081218391296', '2026-10-04 10:48:08', '2026-10-04 10:48:08'),
(3, 'wa_gateway_admin_names', 'putri', '2026-10-04 10:48:08', '2026-10-04 10:48:08'),
(4, 'tema_dashboard', 'default', '2026-10-04 11:28:18', '2026-10-04 11:28:32');

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi_jamkerja`
--

CREATE TABLE `konfigurasi_jamkerja` (
  `nik` char(18) DEFAULT NULL,
  `hari` varchar(10) DEFAULT NULL,
  `kode_jam_kerja` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `konfigurasi_jamkerja`
--

INSERT INTO `konfigurasi_jamkerja` (`nik`, `hari`, `kode_jam_kerja`) VALUES
('PST260817', 'Senin', 'JK01'),
('PST260817', 'Jumat', 'JK02');

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi_jamkerja_by_date`
--

CREATE TABLE `konfigurasi_jamkerja_by_date` (
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi_jk_dept`
--

CREATE TABLE `konfigurasi_jk_dept` (
  `kode_jk_dept` char(7) NOT NULL,
  `kode_cabang` char(3) DEFAULT NULL,
  `kode_dept` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi_jk_dept_detail`
--

CREATE TABLE `konfigurasi_jk_dept_detail` (
  `kode_jk_dept` char(7) DEFAULT NULL,
  `hari` varchar(10) DEFAULT NULL,
  `kode_jam_kerja` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `konfirmasi_kehadiran`
--

CREATE TABLE `konfirmasi_kehadiran` (
  `id` int UNSIGNED NOT NULL,
  `nik` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `kode_jam_kerja` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `quick_approve_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `approved_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `konfirmasi_kehadiran`
--

INSERT INTO `konfirmasi_kehadiran` (`id`, `nik`, `tanggal`, `kode_jam_kerja`, `jam_masuk`, `jam_keluar`, `alasan`, `bukti`, `status`, `quick_approve_token`, `catatan_admin`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 'PST260817', '2026-08-10', 'JK01', '07:00:00', '12:40:00', 'Mohon Maaf Saya Lupa Melakukan Absensi', NULL, 'pending', '8c15553c3cac74a7642ce1148b9c88af5558598d', NULL, NULL, '2026-08-17 08:07:37', '2026-08-17 08:07:37'),
(2, 'PST260817', '2026-10-01', 'JK01', '07:00:00', '12:40:00', 'tidak absen', 'PST260817_1791110440.png', 'approved', '7dd4e0765e6850b16d6f60f4c7662e1f190e2f00', 'oke', 'Miftahul Khaer', '2026-10-04 10:40:40', '2026-10-04 10:44:13');

-- --------------------------------------------------------

--
-- Table structure for table `master_cuti`
--

CREATE TABLE `master_cuti` (
  `kode_cuti` char(3) NOT NULL,
  `nama_cuti` varchar(30) DEFAULT NULL,
  `jml_hari` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `master_kelas`
--

CREATE TABLE `master_kelas` (
  `kode_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kelas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lembaga` enum('MI','MTs','MA','SMK') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_cabang` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wali_kelas_nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kurikulum` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kurikulum Merdeka',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_kelas`
--

INSERT INTO `master_kelas` (`kode_kelas`, `nama_kelas`, `lembaga`, `kode_cabang`, `qr_token`, `wali_kelas_nik`, `kurikulum`, `created_at`, `updated_at`) VALUES
('001-SMK-001', 'X TJKT', 'SMK', 'PST', '9c55d0ba-c8ae-4a28-90f7-3e1e374ea2c3', NULL, 'Kurikulum Merdeka', '2026-08-17 08:12:18', '2026-10-04 11:21:58'),
('PST-MA-001', 'X IPA', 'MA', 'PST', '4bfa199f-2550-440c-8563-1b6c48d9e286', NULL, 'Kurikulum Merdeka', '2026-10-04 11:21:47', '2026-10-04 11:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `master_mapel_kelas`
--

CREATE TABLE `master_mapel_kelas` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_mapel_kelas`
--

INSERT INTO `master_mapel_kelas` (`id`, `kode_kelas`, `nama_mapel`, `created_at`, `updated_at`) VALUES
(1, '001-SMK-001', 'Bahasa Inggris', '2026-08-17 08:12:46', '2026-08-17 08:12:46'),
(2, '001-SMK-001', 'Informatika', '2026-08-17 08:12:51', '2026-08-17 08:12:51'),
(3, 'PST-MA-001', 'Biologi', '2026-10-04 11:22:08', '2026-10-04 11:22:08'),
(4, 'PST-MA-001', 'Bahasa Arab', '2026-10-04 11:22:13', '2026-10-04 11:22:13'),
(5, 'PST-MA-001', 'Al - Quran Hadist', '2026-10-04 11:23:31', '2026-10-04 11:23:31'),
(6, 'PST-MA-001', 'Bahasa Indonesia', '2026-10-04 11:23:31', '2026-10-04 11:23:31'),
(7, 'PST-MA-001', 'Fikih', '2026-10-04 11:23:31', '2026-10-04 11:23:31'),
(8, 'PST-MA-001', 'Fisika', '2026-10-04 11:23:31', '2026-10-04 11:23:31'),
(9, 'PST-MA-001', 'PPKN', '2026-10-04 11:23:31', '2026-10-04 11:23:31'),
(10, 'PST-MA-001', 'Matematika', '2026-10-04 11:23:31', '2026-10-04 11:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `master_siswa`
--

CREATE TABLE `master_siswa` (
  `id` bigint UNSIGNED NOT NULL,
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_siswa` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2023_12_14_233749_create_permission_tables', 1),
(5, '2023_12_15_002819_create_permission_tables', 2),
(6, '2025_03_06_061327_create_zonasi_table', 3),
(7, '2025_05_03_191436_create_fakegps_table', 4),
(8, '2025_07_14_174215_create_zones_table', 5),
(9, '2026_08_10_000001_create_master_siswa_table', 6),
(10, '2026_08_10_000002_add_kurikulum_to_master_kelas_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 22),
(2, 'App\\Models\\User', 36);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengabdian`
--

CREATE TABLE `pengabdian` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi` enum('Pusat','Cabang') COLLATE utf8mb4_unicode_ci DEFAULT 'Pusat',
  `tunjangan_jabatan` decimal(15,2) DEFAULT '0.00',
  `tunjangan_pengabdian` decimal(15,2) DEFAULT '0.00',
  `tunjangan_lainnya` decimal(15,2) DEFAULT '0.00',
  `honorarium_wkt` int DEFAULT '0',
  `honorarium_stn` decimal(15,2) DEFAULT '0.00',
  `kehadiran_wkt` int DEFAULT '0',
  `kehadiran_stn` decimal(15,2) DEFAULT '0.00',
  `potongan_dansos` decimal(15,2) DEFAULT '0.00',
  `potongan_fpmk` decimal(15,2) DEFAULT '0.00',
  `potongan_lainnya` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_izin`
--

CREATE TABLE `pengajuan_izin` (
  `kode_izin` char(9) NOT NULL,
  `nik` char(18) DEFAULT NULL,
  `tgl_izin_dari` date DEFAULT NULL,
  `tgl_izin_sampai` date DEFAULT NULL,
  `status` char(1) DEFAULT NULL COMMENT 'i : izin s : sakit',
  `kode_cuti` char(3) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `doc_sid` varchar(255) DEFAULT NULL,
  `status_approved` char(1) DEFAULT '0' COMMENT '0 : Pending 1: Disetuji 2: Ditolak',
  `quick_approve_token` varchar(100) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `pengajuan_izin`
--

INSERT INTO `pengajuan_izin` (`kode_izin`, `nik`, `tgl_izin_dari`, `tgl_izin_sampai`, `status`, `kode_cuti`, `keterangan`, `doc_sid`, `status_approved`, `quick_approve_token`, `approved_by`) VALUES
('IZ0826001', 'PST260817', '2026-08-26', '2026-08-26', 'i', NULL, 'Ada Keperluan Keluagra', NULL, '0', '792efbe89c2da2a5e0c5f671eb0c8da706d2cbf2', NULL),
('IZ1026001', 'PST260817', '2026-10-03', '2026-10-04', 'i', NULL, 'sakit', NULL, '2', '66112b969841bc21fcdebaec5db324b20f7bd8dd', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `konten` text COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_pengumuman` date NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `konten`, `foto`, `doc_file`, `link`, `tgl_pengumuman`, `status`, `created_at`, `updated_at`) VALUES
(5, 'HARI SUMPAH PEMUDA', 'ABADAHDADHAJDHAKDHAJKDAKD', '1793422483_6ae574935d569_foto.webp', NULL, NULL, '2026-10-31', 1, '2026-10-31 04:54:43', '2026-10-31 04:54:43');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view-karyawan', 'web', '2023-12-14 17:36:28', '2023-12-14 17:36:28'),
(2, 'view-departemen', 'web', '2023-12-14 17:36:28', '2023-12-14 17:36:28');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piket_wa_log`
--

CREATE TABLE `piket_wa_log` (
  `id` bigint UNSIGNED NOT NULL,
  `nik_piket` char(18) NOT NULL,
  `nik_guru` char(18) NOT NULL,
  `tanggal` date NOT NULL,
  `pesan` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `response` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `potongan_guru`
--

CREATE TABLE `potongan_guru` (
  `id` bigint UNSIGNED NOT NULL,
  `guru_id` bigint UNSIGNED NOT NULL,
  `simpanan_koperasi` decimal(12,2) DEFAULT '0.00',
  `dana_sosial` decimal(12,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int NOT NULL,
  `nik` char(18) NOT NULL,
  `tgl_presensi` date NOT NULL,
  `jam_in` time DEFAULT NULL,
  `jam_out` time DEFAULT NULL,
  `foto_in` varchar(255) DEFAULT NULL,
  `foto_out` varchar(255) DEFAULT NULL,
  `lokasi_in` text,
  `lokasi_out` text,
  `kode_jam_kerja` char(4) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `kode_izin` char(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `nik`, `tgl_presensi`, `jam_in`, `jam_out`, `foto_in`, `foto_out`, `lokasi_in`, `lokasi_out`, `kode_jam_kerja`, `status`, `kode_izin`) VALUES
(1, 'PST260817', '2026-08-03', '07:00:00', '12:40:00', NULL, NULL, NULL, NULL, 'JK01', 'h', NULL),
(2, 'PST260817', '2026-08-17', '07:00:00', '12:40:00', NULL, NULL, NULL, NULL, 'JK01', 'h', NULL),
(3, 'PST260817', '2026-10-01', '07:00:00', '12:40:00', NULL, NULL, NULL, NULL, 'JK01', 'h', NULL),
(8, 'PST260817', '2026-10-04', '18:00:00', '00:00:00', NULL, NULL, NULL, NULL, 'JK03', 'h', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `q_rekappresensi`
-- (See below for the actual view)
--
CREATE TABLE `q_rekappresensi` (
`nik` char(18)
,`nama_lengkap` varchar(100)
,`jabatan` varchar(100)
,`tgl_1` varchar(329)
,`tgl_2` varchar(329)
,`tgl_3` varchar(329)
,`tgl_4` varchar(329)
,`tgl_5` varchar(329)
,`tgl_6` varchar(329)
,`tgl_7` varchar(329)
,`tgl_8` varchar(329)
,`tgl_9` varchar(329)
,`tgl_10` varchar(329)
,`tgl_11` varchar(329)
,`tgl_12` varchar(329)
,`tgl_13` varchar(329)
,`tgl_14` varchar(329)
,`tgl_15` varchar(329)
,`tgl_16` varchar(329)
,`tgl_17` varchar(329)
,`tgl_18` varchar(329)
,`tgl_19` varchar(329)
,`tgl_20` varchar(329)
,`tgl_21` varchar(329)
,`tgl_22` varchar(329)
,`tgl_23` varchar(329)
,`tgl_24` varchar(329)
,`tgl_25` varchar(329)
,`tgl_26` varchar(329)
,`tgl_27` varchar(329)
,`tgl_28` varchar(329)
,`tgl_29` varchar(329)
,`tgl_30` varchar(329)
,`tgl_31` varchar(329)
);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'administrator', 'web', '2023-12-14 17:36:28', '2023-12-14 17:36:28'),
(2, 'staff', 'user', '2023-12-17 17:22:14', '2023-12-17 17:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `setting_gaji_guru`
--

CREATE TABLE `setting_gaji_guru` (
  `id` int NOT NULL,
  `golongan` tinyint NOT NULL,
  `transport` int DEFAULT '0',
  `simpanan_koperasi` int DEFAULT '0',
  `dana_sosial` int DEFAULT '0',
  `gaji_pokok` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting_gaji_guru`
--

INSERT INTO `setting_gaji_guru` (`id`, `golongan`, `transport`, `simpanan_koperasi`, `dana_sosial`, `gaji_pokok`, `created_at`, `updated_at`) VALUES
(1, 1, 30000, 0, 0, 30000, NULL, '2026-08-17 08:57:39'),
(2, 2, 0, 0, 0, 0, NULL, '2026-08-17 08:57:39'),
(3, 3, 0, 0, 0, 0, NULL, '2026-08-17 08:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `zonasi`
--

CREATE TABLE `zonasi` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `radius` int NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `radius` int NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `q_rekappresensi`
--
DROP TABLE IF EXISTS `q_rekappresensi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`miftahu1`@`localhost` SQL SECURITY DEFINER VIEW `q_rekappresensi`  AS SELECT `karyawan`.`nik` AS `nik`, `karyawan`.`nama_lengkap` AS `nama_lengkap`, `karyawan`.`jabatan` AS `jabatan`, `presensi`.`tgl_1` AS `tgl_1`, `presensi`.`tgl_2` AS `tgl_2`, `presensi`.`tgl_3` AS `tgl_3`, `presensi`.`tgl_4` AS `tgl_4`, `presensi`.`tgl_5` AS `tgl_5`, `presensi`.`tgl_6` AS `tgl_6`, `presensi`.`tgl_7` AS `tgl_7`, `presensi`.`tgl_8` AS `tgl_8`, `presensi`.`tgl_9` AS `tgl_9`, `presensi`.`tgl_10` AS `tgl_10`, `presensi`.`tgl_11` AS `tgl_11`, `presensi`.`tgl_12` AS `tgl_12`, `presensi`.`tgl_13` AS `tgl_13`, `presensi`.`tgl_14` AS `tgl_14`, `presensi`.`tgl_15` AS `tgl_15`, `presensi`.`tgl_16` AS `tgl_16`, `presensi`.`tgl_17` AS `tgl_17`, `presensi`.`tgl_18` AS `tgl_18`, `presensi`.`tgl_19` AS `tgl_19`, `presensi`.`tgl_20` AS `tgl_20`, `presensi`.`tgl_21` AS `tgl_21`, `presensi`.`tgl_22` AS `tgl_22`, `presensi`.`tgl_23` AS `tgl_23`, `presensi`.`tgl_24` AS `tgl_24`, `presensi`.`tgl_25` AS `tgl_25`, `presensi`.`tgl_26` AS `tgl_26`, `presensi`.`tgl_27` AS `tgl_27`, `presensi`.`tgl_28` AS `tgl_28`, `presensi`.`tgl_29` AS `tgl_29`, `presensi`.`tgl_30` AS `tgl_30`, `presensi`.`tgl_31` AS `tgl_31` FROM (`karyawan` left join (select `presensi`.`nik` AS `nik`,max(if((`presensi`.`tgl_presensi` = '2023-11-01'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_1`,max(if((`presensi`.`tgl_presensi` = '2023-11-02'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_2`,max(if((`presensi`.`tgl_presensi` = '2023-11-03'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_3`,max(if((`presensi`.`tgl_presensi` = '2023-11-04'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_4`,max(if((`presensi`.`tgl_presensi` = '2023-11-05'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_5`,max(if((`presensi`.`tgl_presensi` = '2023-11-06'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_6`,max(if((`presensi`.`tgl_presensi` = '2023-11-07'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_7`,max(if((`presensi`.`tgl_presensi` = '2023-11-08'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_8`,max(if((`presensi`.`tgl_presensi` = '2023-11-09'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_9`,max(if((`presensi`.`tgl_presensi` = '2023-11-10'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_10`,max(if((`presensi`.`tgl_presensi` = '2023-11-11'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_11`,max(if((`presensi`.`tgl_presensi` = '2023-11-12'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_12`,max(if((`presensi`.`tgl_presensi` = '2023-11-13'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_13`,max(if((`presensi`.`tgl_presensi` = '2023-11-14'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_14`,max(if((`presensi`.`tgl_presensi` = '2023-11-15'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_15`,max(if((`presensi`.`tgl_presensi` = '2023-11-16'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_16`,max(if((`presensi`.`tgl_presensi` = '2023-11-17'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_17`,max(if((`presensi`.`tgl_presensi` = '2023-11-18'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_18`,max(if((`presensi`.`tgl_presensi` = '2023-11-19'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_19`,max(if((`presensi`.`tgl_presensi` = '2023-11-20'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_20`,max(if((`presensi`.`tgl_presensi` = '2023-11-21'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_21`,max(if((`presensi`.`tgl_presensi` = '2023-11-22'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_22`,max(if((`presensi`.`tgl_presensi` = '2023-11-23'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_23`,max(if((`presensi`.`tgl_presensi` = '2023-11-24'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_24`,max(if((`presensi`.`tgl_presensi` = '2023-11-25'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_25`,max(if((`presensi`.`tgl_presensi` = '2023-11-26'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_26`,max(if((`presensi`.`tgl_presensi` = '2023-11-27'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_27`,max(if((`presensi`.`tgl_presensi` = '2023-11-28'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_28`,max(if((`presensi`.`tgl_presensi` = '2023-11-29'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_29`,max(if((`presensi`.`tgl_presensi` = '2023-11-30'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_30`,max(if((`presensi`.`tgl_presensi` = '2023-11-31'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_31` from ((`presensi` left join `jam_kerja` on((`presensi`.`kode_jam_kerja` = `jam_kerja`.`kode_jam_kerja`))) left join `pengajuan_izin` on((`presensi`.`kode_izin` = `pengajuan_izin`.`kode_izin`))) group by `presensi`.`nik`) `presensi` on((`karyawan`.`nik` = `presensi`.`nik`)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE;

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`kode_cabang`) USING BTREE;

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`kode_dept`) USING BTREE;

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fake_g_p_s`
--
ALTER TABLE `fake_g_p_s`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_guru_karyawan` (`karyawan_nik`);

--
-- Indexes for table `harilibur`
--
ALTER TABLE `harilibur`
  ADD PRIMARY KEY (`kode_libur`) USING BTREE;

--
-- Indexes for table `jam_kerja`
--
ALTER TABLE `jam_kerja`
  ADD PRIMARY KEY (`kode_jam_kerja`) USING BTREE;

--
-- Indexes for table `jurnal_absensi_siswa`
--
ALTER TABLE `jurnal_absensi_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurnal_id` (`jurnal_id`),
  ADD KEY `nis` (`nis`);

--
-- Indexes for table `jurnal_mengajar`
--
ALTER TABLE `jurnal_mengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_kelas` (`kode_kelas`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`nik`) USING BTREE,
  ADD KEY `fk_karyawan_cabang` (`kode_cabang`) USING BTREE,
  ADD KEY `fk_karyawan_dept` (`kode_dept`) USING BTREE;

--
-- Indexes for table `konfigurasi_app`
--
ALTER TABLE `konfigurasi_app`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `konfigurasi_jamkerja`
--
ALTER TABLE `konfigurasi_jamkerja`
  ADD KEY `fk_kj_jk` (`kode_jam_kerja`) USING BTREE,
  ADD KEY `fk_kj_karyawan` (`nik`) USING BTREE;

--
-- Indexes for table `konfigurasi_jamkerja_by_date`
--
ALTER TABLE `konfigurasi_jamkerja_by_date`
  ADD KEY `fk_kjbydate_karyawan` (`nik`) USING BTREE;

--
-- Indexes for table `konfigurasi_jk_dept`
--
ALTER TABLE `konfigurasi_jk_dept`
  ADD PRIMARY KEY (`kode_jk_dept`) USING BTREE,
  ADD KEY `fk_kjd_cabang` (`kode_cabang`) USING BTREE,
  ADD KEY `fk_kjd_dept` (`kode_dept`) USING BTREE;

--
-- Indexes for table `konfigurasi_jk_dept_detail`
--
ALTER TABLE `konfigurasi_jk_dept_detail`
  ADD KEY `fk_jkdept` (`kode_jk_dept`) USING BTREE;

--
-- Indexes for table `konfirmasi_kehadiran`
--
ALTER TABLE `konfirmasi_kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nik` (`nik`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `master_cuti`
--
ALTER TABLE `master_cuti`
  ADD PRIMARY KEY (`kode_cuti`) USING BTREE;

--
-- Indexes for table `master_kelas`
--
ALTER TABLE `master_kelas`
  ADD PRIMARY KEY (`kode_kelas`),
  ADD UNIQUE KEY `qr_token_unique` (`qr_token`);

--
-- Indexes for table `master_mapel_kelas`
--
ALTER TABLE `master_mapel_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_kelas` (`kode_kelas`);

--
-- Indexes for table `master_siswa`
--
ALTER TABLE `master_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_siswa_kode_kelas_foreign` (`kode_kelas`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `pengabdian`
--
ALTER TABLE `pengabdian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD PRIMARY KEY (`kode_izin`) USING BTREE,
  ADD KEY `fk_pengajuan_cuti` (`kode_cuti`) USING BTREE,
  ADD KEY `fk_pengajuan_karyawan` (`nik`) USING BTREE;

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `piket_wa_log`
--
ALTER TABLE `piket_wa_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_piket_wa_guru_tanggal` (`nik_guru`,`tanggal`),
  ADD KEY `fk_piket_wa_log_piket` (`nik_piket`);

--
-- Indexes for table `potongan_guru`
--
ALTER TABLE `potongan_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_potongan_guru` (`guru_id`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `fk_prensesi_pengajuan_izin` (`kode_izin`) USING BTREE,
  ADD KEY `fk_presensi_jk` (`kode_jam_kerja`) USING BTREE,
  ADD KEY `fk_presensi_karyawan` (`nik`) USING BTREE;

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE;

--
-- Indexes for table `setting_gaji_guru`
--
ALTER TABLE `setting_gaji_guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `golongan` (`golongan`);

--
-- Indexes for table `zonasi`
--
ALTER TABLE `zonasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fake_g_p_s`
--
ALTER TABLE `fake_g_p_s`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jurnal_absensi_siswa`
--
ALTER TABLE `jurnal_absensi_siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_mengajar`
--
ALTER TABLE `jurnal_mengajar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `konfigurasi_app`
--
ALTER TABLE `konfigurasi_app`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `konfirmasi_kehadiran`
--
ALTER TABLE `konfirmasi_kehadiran`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_mapel_kelas`
--
ALTER TABLE `master_mapel_kelas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `master_siswa`
--
ALTER TABLE `master_siswa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengabdian`
--
ALTER TABLE `pengabdian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piket_wa_log`
--
ALTER TABLE `piket_wa_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `potongan_guru`
--
ALTER TABLE `potongan_guru`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `setting_gaji_guru`
--
ALTER TABLE `setting_gaji_guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `zonasi`
--
ALTER TABLE `zonasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_karyawan` FOREIGN KEY (`karyawan_nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal_mengajar`
--
ALTER TABLE `jurnal_mengajar`
  ADD CONSTRAINT `jurnal_mengajar_ibfk_1` FOREIGN KEY (`kode_kelas`) REFERENCES `master_kelas` (`kode_kelas`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `fk_karyawan_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_karyawan_dept` FOREIGN KEY (`kode_dept`) REFERENCES `departemen` (`kode_dept`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konfigurasi_jamkerja`
--
ALTER TABLE `konfigurasi_jamkerja`
  ADD CONSTRAINT `fk_kj_jk` FOREIGN KEY (`kode_jam_kerja`) REFERENCES `jam_kerja` (`kode_jam_kerja`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kj_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konfigurasi_jamkerja_by_date`
--
ALTER TABLE `konfigurasi_jamkerja_by_date`
  ADD CONSTRAINT `fk_kjbydate_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konfigurasi_jk_dept`
--
ALTER TABLE `konfigurasi_jk_dept`
  ADD CONSTRAINT `fk_kjd_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kjd_dept` FOREIGN KEY (`kode_dept`) REFERENCES `departemen` (`kode_dept`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konfigurasi_jk_dept_detail`
--
ALTER TABLE `konfigurasi_jk_dept_detail`
  ADD CONSTRAINT `fk_jkdept` FOREIGN KEY (`kode_jk_dept`) REFERENCES `konfigurasi_jk_dept` (`kode_jk_dept`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `master_mapel_kelas`
--
ALTER TABLE `master_mapel_kelas`
  ADD CONSTRAINT `master_mapel_kelas_ibfk_1` FOREIGN KEY (`kode_kelas`) REFERENCES `master_kelas` (`kode_kelas`) ON DELETE CASCADE;

--
-- Constraints for table `master_siswa`
--
ALTER TABLE `master_siswa`
  ADD CONSTRAINT `master_siswa_kode_kelas_foreign` FOREIGN KEY (`kode_kelas`) REFERENCES `master_kelas` (`kode_kelas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `fk_admins` FOREIGN KEY (`model_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD CONSTRAINT `fk_pengajuan_cuti` FOREIGN KEY (`kode_cuti`) REFERENCES `master_cuti` (`kode_cuti`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengajuan_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `piket_wa_log`
--
ALTER TABLE `piket_wa_log`
  ADD CONSTRAINT `fk_piket_wa_log_guru` FOREIGN KEY (`nik_guru`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_piket_wa_log_piket` FOREIGN KEY (`nik_piket`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `potongan_guru`
--
ALTER TABLE `potongan_guru`
  ADD CONSTRAINT `fk_potongan_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `fk_prensesi_pengajuan_izin` FOREIGN KEY (`kode_izin`) REFERENCES `pengajuan_izin` (`kode_izin`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_presensi_jk` FOREIGN KEY (`kode_jam_kerja`) REFERENCES `jam_kerja` (`kode_jam_kerja`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presensi_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
