/*
 Navicat Premium Dump SQL

 Source Server         : serverwebsiteabsensi
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : presensigpsnew

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 10/06/2025 17:26:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_dept` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kode_cabang` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES (14, 'Bu desi ratnasari', 'desiratnasari@gmail.com', NULL, '$2y$10$mthzYa/CRxONhqC0gwbL/eHyajw4EtHAQdnERmFhiU0NNJdUgwcCO', 'KPS', 'SMP', NULL, '2025-03-04 13:30:31', '2025-03-04 13:30:31');
INSERT INTO `admins` VALUES (17, 'badrit', 'kangbadrit@gmail.com', NULL, '$2y$10$JmLNIuJW19LNMdC8sljfNudjIkP/QOjTaPtGZ.2x6OHBffF1xNvVS', 'TU', 'SMP', NULL, '2025-05-27 13:25:23', '2025-05-27 13:25:23');
INSERT INTO `admins` VALUES (20, 'achmadbaihaqi', 'achmadbaihaqi583@gmail.com', NULL, '$2y$10$YKTORtTiAMZSp48lMv.J9u68bljNmCOmil66XZxgF1yDX7ba97VYe', 'TU', 'SMP', NULL, '2025-05-27 14:08:20', '2025-05-27 14:08:20');

-- ----------------------------
-- Table structure for cabang
-- ----------------------------
DROP TABLE IF EXISTS `cabang`;
CREATE TABLE `cabang`  (
  `kode_cabang` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_cabang` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `lokasi_cabang` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `radius_cabang` int NOT NULL,
  PRIMARY KEY (`kode_cabang`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cabang
-- ----------------------------
INSERT INTO `cabang` VALUES ('SMP', 'Kantor smp', '-7.57831, 112.44093', 10);

-- ----------------------------
-- Table structure for departemen
-- ----------------------------
DROP TABLE IF EXISTS `departemen`;
CREATE TABLE `departemen`  (
  `kode_dept` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_dept` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`kode_dept`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of departemen
-- ----------------------------
INSERT INTO `departemen` VALUES ('GRU', 'Guru');
INSERT INTO `departemen` VALUES ('KPS', 'Kepala sekolah');
INSERT INTO `departemen` VALUES ('TU', 'Tata usaha');

-- ----------------------------
-- Table structure for harilibur
-- ----------------------------
DROP TABLE IF EXISTS `harilibur`;
CREATE TABLE `harilibur`  (
  `kode_libur` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_libur` date NOT NULL,
  `kode_cabang` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kode_libur`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of harilibur
-- ----------------------------

-- ----------------------------
-- Table structure for harilibur_detail
-- ----------------------------
DROP TABLE IF EXISTS `harilibur_detail`;
CREATE TABLE `harilibur_detail`  (
  `kode_libur` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nik` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of harilibur_detail
-- ----------------------------
INSERT INTO `harilibur_detail` VALUES ('LB24002', '3636');
INSERT INTO `harilibur_detail` VALUES ('LB24002', '22-01');
INSERT INTO `harilibur_detail` VALUES ('LB24002', '99-99');
INSERT INTO `harilibur_detail` VALUES ('LB24002', '12349');
INSERT INTO `harilibur_detail` VALUES ('LB24003', '12345');
INSERT INTO `harilibur_detail` VALUES ('LB24003', '3636');
INSERT INTO `harilibur_detail` VALUES ('LB24003', 'Ustadafrizal');
INSERT INTO `harilibur_detail` VALUES ('LB24003', 'kangbadrit');
INSERT INTO `harilibur_detail` VALUES ('LB24003', 'bucandy');

-- ----------------------------
-- Table structure for jam_kerja
-- ----------------------------
DROP TABLE IF EXISTS `jam_kerja`;
CREATE TABLE `jam_kerja`  (
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_jam_kerja` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `awal_jam_masuk` time NOT NULL,
  `jam_masuk` time NOT NULL,
  `akhir_jam_masuk` time NOT NULL,
  `awal_jam_istirahat` time NULL DEFAULT NULL,
  `akhir_jam_istirahat` time NULL DEFAULT NULL,
  `status_istirahat` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jam_pulang` time NOT NULL,
  `total_jam` smallint NOT NULL,
  `lintashari` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`kode_jam_kerja`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of jam_kerja
-- ----------------------------
INSERT INTO `jam_kerja` VALUES ('JK01', 'SENIN - KAMIS', '06:00:00', '07:00:00', '07:30:00', '08:30:00', '09:30:00', '1', '14:00:00', 7, NULL);
INSERT INTO `jam_kerja` VALUES ('JK02', 'JUM\'AT', '06:00:00', '07:00:00', '07:30:00', '08:30:00', '09:30:00', '1', '11:00:00', 6, NULL);
INSERT INTO `jam_kerja` VALUES ('JK03', 'SABTU', '06:00:00', '07:00:00', '07:30:00', '08:55:00', '09:30:00', '1', '14:00:00', 7, NULL);

-- ----------------------------
-- Table structure for karyawan
-- ----------------------------
DROP TABLE IF EXISTS `karyawan`;
CREATE TABLE `karyawan`  (
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0',
  `nama_lengkap` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jabatan` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `no_hp` varchar(13) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `foto` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kode_dept` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `kode_cabang` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status_location` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '1',
  `status_jam_kerja` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '1',
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `remember_token` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`nik`) USING BTREE,
  INDEX `fk_karyawan_cabang`(`kode_cabang` ASC) USING BTREE,
  INDEX `fk_karyawan_dept`(`kode_dept` ASC) USING BTREE,
  CONSTRAINT `fk_karyawan_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_karyawan_dept` FOREIGN KEY (`kode_dept`) REFERENCES `departemen` (`kode_dept`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of karyawan
-- ----------------------------
INSERT INTO `karyawan` VALUES ('bucandy', 'candy alfa agustina,S.Pd', 'Guru matematika', '085852137454', 'bucandy.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$1ERZBnStqEX1ksNcrutgsuPgYAyNaobg2CsNjZCK70XzeSd8KNNau', NULL);
INSERT INTO `karyawan` VALUES ('buifa', 'Ifa miftahul rifdah,S.Pd', 'Guru pai dan ips', '08197653488', 'buifa.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$h8RUkAc8Q2tZ1LL3sqx7SeqfTbrZOR0v4yBUkoQrIe9eAopPyTWJm', NULL);
INSERT INTO `karyawan` VALUES ('bulailatul', 'Lailatul fitriyah,S.Fil', 'Guru tik', '086738039740', 'bulailatul.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$0fpv4CslJj37ya.Qea7/M.Pjqc.LgWd/vnRrEM8Nz0nZ6rTxwRave', NULL);
INSERT INTO `karyawan` VALUES ('bulailiyatul', 'lailiyatul fadlilah,S.Pd', 'Guru ips', '081749293824', 'bulailiyatul.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$zeyb30WAJyqQG1/H0QQ6p.Lad9McQi.KySUdvdxakCsJ7Nos.ifKK', NULL);
INSERT INTO `karyawan` VALUES ('burizki', 'rizki aminatut tazkiyah,S.Kep', 'Ipa Terpadu', '0858920848402', 'burizki.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$idvv.f.ALzbbkjsTS5uWO.U9..g.46XhwBU6dIVMNNkVG3DTX92sS', NULL);
INSERT INTO `karyawan` VALUES ('busiti', 'siti maimunah,S.Pd', 'Guru bahasa inggris', '081748273893', 'busiti.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$BUmZ1HvcpT0hNoyF1h9VCeodyzBSmOjbEkIK7OY9jt/q0bpQNAcrS', NULL);
INSERT INTO `karyawan` VALUES ('butauqiyah', 'tauqiyah ridha adfina,S.Pd', 'Guru pai', '0859092847739', 'butauqiyah.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$7Yu/Wo7bfBW60j.b3CCYpO16TFslo5nZzEvfr/sxSKPUTAZw92Ea2', NULL);
INSERT INTO `karyawan` VALUES ('kangbadrit', 'Badrit tamimi', 'Staff tu', '085852137454', 'kangbadrit.jpg', 'TU', 'SMP', '1', '1', '$2y$10$p31UmTyiXvtRMRJWlhjfiu3MIRqP2TK78hvfjwU0XLRmlwqb70Lde', NULL);
INSERT INTO `karyawan` VALUES ('Ustadafrizal', 'Afrizal asyari S.Pd.i', 'Guru aswaja', '085852137545', 'Ustadafrizal.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$ywsDtXJY.AchY6h2gUCWR.oCXzKNxG.AG/HwhJmYfNoTzjUuADZ1q', NULL);
INSERT INTO `karyawan` VALUES ('Ustadmashuda', 'Mashuda,S.Pd', 'Guru Matematika', '085469738393', 'Ustadmashuda.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$oURZPi5IRBKicjREzjEc8ePtlBn9rzWWn/3PZoJ34Hh.kdPh5cG8O', NULL);
INSERT INTO `karyawan` VALUES ('Ustadnasrudin', 'H.Agus nasrudin,S.Pd. i', 'Guru pkn', '0858990384859', 'Ustadnasrudin.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$7p6Lv0FA8t1fpVFhnubltOnWyI6iDLIw7w0oO8siZabDAc8iF0IQe', NULL);
INSERT INTO `karyawan` VALUES ('Ustadnur', 'Nur rohman,M.Pd', 'guru btq', '8178459378', 'Ustadnur.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$Aw30deJBG6BY7ov5tEh0IO6VVxEesYUaOcRb.CoyprRUxREhGv3Xy', NULL);
INSERT INTO `karyawan` VALUES ('Ustadwahyu', 'Wahyu agung ari susanto.S,Pd', 'Gurubahasaindonesia', '0867588767855', 'Ustadwahyu.jpg', 'GRU', 'SMP', '1', '1', '$2y$10$l7I9x5nx92M88S6BzW8B5e7D4NKxSv8e4fPv8v7xZqI3iJjxHGjGW', NULL);

-- ----------------------------
-- Table structure for konfigurasi_jamkerja
-- ----------------------------
DROP TABLE IF EXISTS `konfigurasi_jamkerja`;
CREATE TABLE `konfigurasi_jamkerja`  (
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hari` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  INDEX `fk_kj_jk`(`kode_jam_kerja` ASC) USING BTREE,
  INDEX `fk_kj_karyawan`(`nik` ASC) USING BTREE,
  CONSTRAINT `fk_kj_jk` FOREIGN KEY (`kode_jam_kerja`) REFERENCES `jam_kerja` (`kode_jam_kerja`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_kj_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of konfigurasi_jamkerja
-- ----------------------------
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('kangbadrit', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bucandy', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnasrudin', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('buifa', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailatul', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('bulailiyatul', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadmashuda', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadnur', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('burizki', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('busiti', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('Ustadwahyu', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Minggu', NULL);
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jamkerja` VALUES ('butauqiyah', 'Minggu', NULL);

-- ----------------------------
-- Table structure for konfigurasi_jamkerja_by_date
-- ----------------------------
DROP TABLE IF EXISTS `konfigurasi_jamkerja_by_date`;
CREATE TABLE `konfigurasi_jamkerja_by_date`  (
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  INDEX `fk_kjbydate_karyawan`(`nik` ASC) USING BTREE,
  CONSTRAINT `fk_kjbydate_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of konfigurasi_jamkerja_by_date
-- ----------------------------

-- ----------------------------
-- Table structure for konfigurasi_jk_dept
-- ----------------------------
DROP TABLE IF EXISTS `konfigurasi_jk_dept`;
CREATE TABLE `konfigurasi_jk_dept`  (
  `kode_jk_dept` char(7) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `kode_cabang` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kode_dept` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`kode_jk_dept`) USING BTREE,
  INDEX `fk_kjd_cabang`(`kode_cabang` ASC) USING BTREE,
  INDEX `fk_kjd_dept`(`kode_dept` ASC) USING BTREE,
  CONSTRAINT `fk_kjd_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_kjd_dept` FOREIGN KEY (`kode_dept`) REFERENCES `departemen` (`kode_dept`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of konfigurasi_jk_dept
-- ----------------------------
INSERT INTO `konfigurasi_jk_dept` VALUES ('JSMPGRU', 'SMP', 'GRU');
INSERT INTO `konfigurasi_jk_dept` VALUES ('JSMPKPS', 'SMP', 'KPS');

-- ----------------------------
-- Table structure for konfigurasi_jk_dept_detail
-- ----------------------------
DROP TABLE IF EXISTS `konfigurasi_jk_dept_detail`;
CREATE TABLE `konfigurasi_jk_dept_detail`  (
  `kode_jk_dept` char(7) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hari` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  INDEX `fk_jkdept`(`kode_jk_dept` ASC) USING BTREE,
  CONSTRAINT `fk_jkdept` FOREIGN KEY (`kode_jk_dept`) REFERENCES `konfigurasi_jk_dept` (`kode_jk_dept`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of konfigurasi_jk_dept_detail
-- ----------------------------
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Senin', 'JK01');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Selasa', 'JK01');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Rabu', 'JK01');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Kamis', 'JK01');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Jumat', 'JK02');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPKPS', 'Sabtu', 'JK03');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Senin', 'JKO4');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Selasa', 'JKO4');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Rabu', 'JKO4');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Kamis', 'JKO4');
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Jumat', NULL);
INSERT INTO `konfigurasi_jk_dept_detail` VALUES ('JSMPGRU', 'Sabtu', NULL);

-- ----------------------------
-- Table structure for master_cuti
-- ----------------------------
DROP TABLE IF EXISTS `master_cuti`;
CREATE TABLE `master_cuti`  (
  `kode_cuti` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_cuti` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jml_hari` smallint NULL DEFAULT NULL,
  PRIMARY KEY (`kode_cuti`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of master_cuti
-- ----------------------------
INSERT INTO `master_cuti` VALUES ('C01', 'Tahunan', 12);
INSERT INTO `master_cuti` VALUES ('C02', 'Cuti Melahirkan', 90);
INSERT INTO `master_cuti` VALUES ('C04', 'Cuti Menikah', 2);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (2, '2019_08_19_000000_create_failed_jobs_table', 1);
INSERT INTO `migrations` VALUES (3, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` VALUES (4, '2023_12_14_233749_create_permission_tables', 1);
INSERT INTO `migrations` VALUES (5, '2023_12_15_002819_create_permission_tables', 2);
INSERT INTO `migrations` VALUES (6, '2025_03_06_061327_create_zonasi_table', 3);
INSERT INTO `migrations` VALUES (7, '2025_05_03_191436_create_fakegps_table', 4);

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_permissions_model_id_model_type_index`(`model_id` ASC, `model_type` ASC) USING BTREE,
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles`  (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_roles_model_id_model_type_index`(`model_id` ASC, `model_type` ASC) USING BTREE,
  CONSTRAINT `fk_admins` FOREIGN KEY (`model_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------
INSERT INTO `model_has_roles` VALUES (1, 'App\\Models\\User', 14);
INSERT INTO `model_has_roles` VALUES (1, 'App\\Models\\User', 17);
INSERT INTO `model_has_roles` VALUES (1, 'App\\Models\\User', 20);

-- ----------------------------
-- Table structure for pengajuan_izin
-- ----------------------------
DROP TABLE IF EXISTS `pengajuan_izin`;
CREATE TABLE `pengajuan_izin`  (
  `kode_izin` char(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tgl_izin_dari` date NULL DEFAULT NULL,
  `tgl_izin_sampai` date NULL DEFAULT NULL,
  `status` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'i : izin s : sakit',
  `kode_cuti` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_sid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status_approved` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0' COMMENT '0 : Pending 1: Disetuji 2: Ditolak',
  PRIMARY KEY (`kode_izin`) USING BTREE,
  INDEX `fk_pengajuan_cuti`(`kode_cuti` ASC) USING BTREE,
  INDEX `fk_pengajuan_karyawan`(`nik` ASC) USING BTREE,
  CONSTRAINT `fk_pengajuan_cuti` FOREIGN KEY (`kode_cuti`) REFERENCES `master_cuti` (`kode_cuti`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pengajuan_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of pengajuan_izin
-- ----------------------------
INSERT INTO `pengajuan_izin` VALUES ('IZ0225001', 'Ustadafrizal', '2025-02-03', '2025-02-07', 'i', NULL, 'izin sakit', NULL, '0');
INSERT INTO `pengajuan_izin` VALUES ('IZ0325001', 'bucandy', '2025-03-05', '2025-03-10', 'i', NULL, 'malas berkerja', NULL, '0');
INSERT INTO `pengajuan_izin` VALUES ('IZ0525001', 'Ustadafrizal', '2025-05-04', '2025-05-06', 's', NULL, 'Assalamuallaikum kepada sekolah smp rahman wahid izin untuk mengirimkan surat izin yang sudah saya buat di file suratizinustadrizal .pdf', 'IZ0525001.png', '2');
INSERT INTO `pengajuan_izin` VALUES ('IZ0525002', 'Ustadafrizal', '2025-05-08', '2025-05-09', 'i', NULL, 'sakit', NULL, '2');

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `permissions_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'view-karyawan', 'web', '2023-12-15 00:36:28', '2023-12-15 00:36:28');
INSERT INTO `permissions` VALUES (2, 'view-departemen', 'web', '2023-12-15 00:36:28', '2023-12-15 00:36:28');

-- ----------------------------
-- Table structure for presensi
-- ----------------------------
DROP TABLE IF EXISTS `presensi`;
CREATE TABLE `presensi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` char(18) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tgl_presensi` date NOT NULL,
  `jam_in` time NULL DEFAULT NULL,
  `jam_out` time NULL DEFAULT NULL,
  `foto_in` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `foto_out` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lokasi_in` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `lokasi_out` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `kode_jam_kerja` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kode_izin` char(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_prensesi_pengajuan_izin`(`kode_izin` ASC) USING BTREE,
  INDEX `fk_presensi_jk`(`kode_jam_kerja` ASC) USING BTREE,
  INDEX `fk_presensi_karyawan`(`nik` ASC) USING BTREE,
  CONSTRAINT `fk_prensesi_pengajuan_izin` FOREIGN KEY (`kode_izin`) REFERENCES `pengajuan_izin` (`kode_izin`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_presensi_jk` FOREIGN KEY (`kode_jam_kerja`) REFERENCES `jam_kerja` (`kode_jam_kerja`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_presensi_karyawan` FOREIGN KEY (`nik`) REFERENCES `karyawan` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 58 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of presensi
-- ----------------------------
INSERT INTO `presensi` VALUES (48, 'Ustadafrizal', '2025-05-20', '00:06:00', '00:14:00', NULL, NULL, NULL, NULL, 'JK01', 'h', NULL);

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`) USING BTREE,
  INDEX `role_has_permissions_role_id_foreign`(`role_id` ASC) USING BTREE,
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
INSERT INTO `role_has_permissions` VALUES (1, 1);
INSERT INTO `role_has_permissions` VALUES (2, 1);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `roles_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'administrator', 'web', '2023-12-15 00:36:28', '2023-12-15 00:36:28');
INSERT INTO `roles` VALUES (2, 'admin departemen', 'web', '2023-12-18 00:22:14', '2023-12-18 00:22:14');

-- ----------------------------
-- View structure for q_rekappresensi
-- ----------------------------
DROP VIEW IF EXISTS `q_rekappresensi`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `q_rekappresensi` AS select `karyawan`.`nik` AS `nik`,`karyawan`.`nama_lengkap` AS `nama_lengkap`,`karyawan`.`jabatan` AS `jabatan`,`presensi`.`tgl_1` AS `tgl_1`,`presensi`.`tgl_2` AS `tgl_2`,`presensi`.`tgl_3` AS `tgl_3`,`presensi`.`tgl_4` AS `tgl_4`,`presensi`.`tgl_5` AS `tgl_5`,`presensi`.`tgl_6` AS `tgl_6`,`presensi`.`tgl_7` AS `tgl_7`,`presensi`.`tgl_8` AS `tgl_8`,`presensi`.`tgl_9` AS `tgl_9`,`presensi`.`tgl_10` AS `tgl_10`,`presensi`.`tgl_11` AS `tgl_11`,`presensi`.`tgl_12` AS `tgl_12`,`presensi`.`tgl_13` AS `tgl_13`,`presensi`.`tgl_14` AS `tgl_14`,`presensi`.`tgl_15` AS `tgl_15`,`presensi`.`tgl_16` AS `tgl_16`,`presensi`.`tgl_17` AS `tgl_17`,`presensi`.`tgl_18` AS `tgl_18`,`presensi`.`tgl_19` AS `tgl_19`,`presensi`.`tgl_20` AS `tgl_20`,`presensi`.`tgl_21` AS `tgl_21`,`presensi`.`tgl_22` AS `tgl_22`,`presensi`.`tgl_23` AS `tgl_23`,`presensi`.`tgl_24` AS `tgl_24`,`presensi`.`tgl_25` AS `tgl_25`,`presensi`.`tgl_26` AS `tgl_26`,`presensi`.`tgl_27` AS `tgl_27`,`presensi`.`tgl_28` AS `tgl_28`,`presensi`.`tgl_29` AS `tgl_29`,`presensi`.`tgl_30` AS `tgl_30`,`presensi`.`tgl_31` AS `tgl_31` from (`karyawan` left join (select `presensi`.`nik` AS `nik`,max(if((`presensi`.`tgl_presensi` = '2023-11-01'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_1`,max(if((`presensi`.`tgl_presensi` = '2023-11-02'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_2`,max(if((`presensi`.`tgl_presensi` = '2023-11-03'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_3`,max(if((`presensi`.`tgl_presensi` = '2023-11-04'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_4`,max(if((`presensi`.`tgl_presensi` = '2023-11-05'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_5`,max(if((`presensi`.`tgl_presensi` = '2023-11-06'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_6`,max(if((`presensi`.`tgl_presensi` = '2023-11-07'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_7`,max(if((`presensi`.`tgl_presensi` = '2023-11-08'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_8`,max(if((`presensi`.`tgl_presensi` = '2023-11-09'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_9`,max(if((`presensi`.`tgl_presensi` = '2023-11-10'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_10`,max(if((`presensi`.`tgl_presensi` = '2023-11-11'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_11`,max(if((`presensi`.`tgl_presensi` = '2023-11-12'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_12`,max(if((`presensi`.`tgl_presensi` = '2023-11-13'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_13`,max(if((`presensi`.`tgl_presensi` = '2023-11-14'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_14`,max(if((`presensi`.`tgl_presensi` = '2023-11-15'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_15`,max(if((`presensi`.`tgl_presensi` = '2023-11-16'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_16`,max(if((`presensi`.`tgl_presensi` = '2023-11-17'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_17`,max(if((`presensi`.`tgl_presensi` = '2023-11-18'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_18`,max(if((`presensi`.`tgl_presensi` = '2023-11-19'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_19`,max(if((`presensi`.`tgl_presensi` = '2023-11-20'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_20`,max(if((`presensi`.`tgl_presensi` = '2023-11-21'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_21`,max(if((`presensi`.`tgl_presensi` = '2023-11-22'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_22`,max(if((`presensi`.`tgl_presensi` = '2023-11-23'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_23`,max(if((`presensi`.`tgl_presensi` = '2023-11-24'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_24`,max(if((`presensi`.`tgl_presensi` = '2023-11-25'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_25`,max(if((`presensi`.`tgl_presensi` = '2023-11-26'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_26`,max(if((`presensi`.`tgl_presensi` = '2023-11-27'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_27`,max(if((`presensi`.`tgl_presensi` = '2023-11-28'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_28`,max(if((`presensi`.`tgl_presensi` = '2023-11-29'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_29`,max(if((`presensi`.`tgl_presensi` = '2023-11-30'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_30`,max(if((`presensi`.`tgl_presensi` = '2023-11-31'),concat(convert(ifnull(`presensi`.`jam_in`,'NA') using latin1),'|',convert(ifnull(`presensi`.`jam_out`,'NA') using latin1),'|',ifnull(`presensi`.`status`,'NA'),'|',ifnull(`jam_kerja`.`nama_jam_kerja`,'NA'),'|',convert(ifnull(`jam_kerja`.`jam_masuk`,'NA') using latin1),'|',convert(ifnull(`jam_kerja`.`jam_pulang`,'NA') using latin1),'|',ifnull(`presensi`.`kode_izin`,'NA'),'|',ifnull(`pengajuan_izin`.`keterangan`,'NA'),'|'),NULL)) AS `tgl_31` from ((`presensi` left join `jam_kerja` on((`presensi`.`kode_jam_kerja` = `jam_kerja`.`kode_jam_kerja`))) left join `pengajuan_izin` on((`presensi`.`kode_izin` = `pengajuan_izin`.`kode_izin`))) group by `presensi`.`nik`) `presensi` on((`karyawan`.`nik` = `presensi`.`nik`)));

SET FOREIGN_KEY_CHECKS = 1;
