<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Setjamkerja;
use App\Models\Setjamkerjadept;
use App\Models\Setjamkerjadept_detail;
use Carbon\Carbon;

class RankingService
{
    /**
     * Get the full leaderboard (sorted by score)
     * 
     * @return \Illuminate\Support\Collection
     */
    /**
     * Helper: Ambil Konfigurasi Tanggal & Status Leaderboard dari Admin
     */
    public function getLeaderboardConfig()
    {
        $today = Carbon::now();
        $todayDate = $today->format('Y-m-d');

        $startConfig  = DB::table('konfigurasi_app')->where('key', 'leaderboard_start_date')->first();
        $endConfig    = DB::table('konfigurasi_app')->where('key', 'leaderboard_end_date')->first();
        $statusConfig = DB::table('konfigurasi_app')->where('key', 'leaderboard_status')->first();
        $msgConfig    = DB::table('konfigurasi_app')->where('key', 'leaderboard_message')->first();

        $customMessage = $msgConfig && !empty($msgConfig->value)
            ? $msgConfig->value
            : 'Tanggal mulai perhitungan skor poin belum dimulai atau telah berakhir.';

        $status = $statusConfig ? $statusConfig->value : 'active';

        // Jika status di-nonaktifkan oleh admin
        if ($status !== 'active') {
            return [
                'startDate'      => $startConfig ? $startConfig->value : '2026-08-03',
                'endDate'        => $endConfig ? $endConfig->value : $todayDate,
                'isPeriodActive' => false,
                'message'        => $customMessage,
            ];
        }

        if ($startConfig && !empty($startConfig->value) && $endConfig && !empty($endConfig->value)) {
            $startDate = $startConfig->value;
            $endDateTarget = $endConfig->value;

            $isWithinDateRange = ($todayDate >= $startDate && $todayDate <= $endDateTarget);
            $queryStartDate = ($startDate > $todayDate) ? $todayDate : $startDate;

            return [
                'startDate'      => $queryStartDate,
                'endDate'        => min($todayDate, $endDateTarget),
                'isPeriodActive' => $isWithinDateRange,
                'message'        => $customMessage,
            ];
        }

        // Fallback jika belum diatur admin: reset otomatis per 03 Agustus
        $launchDate = Carbon::create(2026, 8, 3);
        if ($today->lt($launchDate)) {
            $startDate = '2026-08-03';
        } else {
            $isAfterAug3 = ($today->month > 8 || ($today->month == 8 && $today->day >= 3));
            $resetYear = $isAfterAug3 ? $today->year : $today->year - 1;
            $startDate = Carbon::create($resetYear, 8, 3)->format('Y-m-d');
        }

        $isPeriodActive = ($todayDate >= $startDate);

        return [
            'startDate'      => $startDate,
            'endDate'        => $todayDate,
            'isPeriodActive' => $isPeriodActive,
            'message'        => $customMessage,
        ];
    }

    private function computeFullLeaderboard()
    {
        $today = Carbon::now();
        $config = $this->getLeaderboardConfig();

        $startDate = $config['startDate'];
        $endDate = $config['endDate'];

        // Hitung jumlah minggu dalam periode scoring
        $weeksInPeriod = max(1, ceil(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) / 7));

        // Pre-calculate Day Frequencies in Range
        $dayFreq = [];
        $temp = Carbon::parse($startDate);
        while ($temp->lte($today)) {
            $indIdx = $this->getDayNameIndo($temp->format('l'));
            if (!isset($dayFreq[$indIdx])) $dayFreq[$indIdx] = 0;
            $dayFreq[$indIdx]++;
            $temp->addDay();
        }

        // Get Target Teachers (Include all non-PPT departments)
        $teachers = Karyawan::where('kode_dept', '!=', 'PPT')->get();
        $niks = $teachers->pluck('nik')->toArray();

        if (empty($niks)) return collect();

        // Batch Queries
        $todayStr = $today->format('Y-m-d');
        $attendanceMap = DB::table('presensi')
            ->select('nik', 
                DB::raw("SUM(IF(status='h' AND (jam_out IS NOT NULL OR tgl_presensi = '$todayStr'), 1, 0)) as present"),
                DB::raw("SUM(IF(status='h' AND jam_in IS NOT NULL AND jam_out IS NULL AND tgl_presensi < '$todayStr', 1, 0)) as incomplete"),
                DB::raw("SUM(IF(status='i', 1, 0)) as izin"),
                DB::raw("SUM(IF(status='s', 1, 0)) as sakit")
            )
            ->whereIn('nik', $niks)
            ->whereBetween('tgl_presensi', [$startDate, $endDate])
            ->groupBy('nik')
            ->get()
            ->keyBy('nik');

        $journalMap = DB::table('jurnal_mengajar')
            ->select('nik', 
                DB::raw("COUNT(DISTINCT tanggal) as journal_days"),
                DB::raw("COUNT(*) as total_entries"),
                DB::raw("SUM(IF(jam_mulai IS NOT NULL AND jam_selesai IS NOT NULL AND jam_selesai > jam_mulai, TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai), 0)) as total_minutes")
            )
            ->whereIn('nik', $niks)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('nik')
            ->get()
            ->keyBy('nik');

        $guruMap = DB::table('guru')
            ->select('karyawan_nik', 'jam_mengajar')
            ->get()
            ->keyBy('karyawan_nik');

        $pengabdianMap = DB::table('pengabdian')
            ->select('nik', 'honorarium_wkt')
            ->get()
            ->keyBy('nik');

        $individualSchedules = Setjamkerja::whereIn('nik', $niks)->get()->groupBy('nik');
        
        $deptCombos = $teachers->unique(function($item){ return $item->kode_dept . $item->kode_cabang; });
        $deptSchedules = collect();
        foreach($deptCombos as $combo) {
            $confDept = Setjamkerjadept::where('kode_dept', $combo->kode_dept)
                        ->where('kode_cabang', $combo->kode_cabang)
                        ->first();
            if ($confDept) {
                $details = DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $confDept->kode_jk_dept)->get();
                $deptSchedules->put($combo->kode_dept . $combo->kode_cabang, $details);
            }
        }

        // Calculate Scores
        $leaderboard = collect();
        foreach ($teachers as $teacher) {
            $attRecord = $attendanceMap->get($teacher->nik);
            $attPresent = $attRecord->present ?? 0;
            $attIncomplete = $attRecord->incomplete ?? 0;
            
            $jrnRecord = $journalMap->get($teacher->nik);
            $jrnDays = $jrnRecord->journal_days ?? 0;
            $jrnTotal = $jrnRecord->total_entries ?? 0;
            $jrnMinutes = $jrnRecord->total_minutes ?? 0;

            $guruRecord = $guruMap->get($teacher->nik);
            $pengabdianRecord = $pengabdianMap->get($teacher->nik);

            // Jam mengajar diambil dari Data Guru (jam_mengajar) atau Data Pengabdian (honorarium_wkt) secara eksklusif
            if ($guruRecord && (int)$guruRecord->jam_mengajar > 0) {
                $totalJamMengajar = (int)$guruRecord->jam_mengajar;
            } elseif ($pengabdianRecord && (int)$pengabdianRecord->honorarium_wkt > 0) {
                $totalJamMengajar = (int)$pengabdianRecord->honorarium_wkt;
            } else {
                $totalJamMengajar = 0;
            }
            
            $expectedDays = 0;
            $teacherIndSchedules = $individualSchedules->get($teacher->nik, collect());
            $teacherDeptSchedule = $deptSchedules->get($teacher->kode_dept . $teacher->kode_cabang);

            foreach ($dayFreq as $hari => $count) {
                $isWork = false;
                if ($teacherIndSchedules->isNotEmpty()) {
                    $isWork = $teacherIndSchedules->where('hari', $hari)->whereNotNull('kode_jam_kerja')->isNotEmpty();
                } elseif ($teacherDeptSchedule) {
                    $isWork = $teacherDeptSchedule->where('hari', $hari)->whereNotNull('kode_jam_kerja')->isNotEmpty();
                }
                if ($isWork) $expectedDays += $count;
            }

            $effectiveExpected = $expectedDays > 0 ? $expectedDays : max($attPresent, 1);
            
            // Efisiensi Kehadiran (penalti lupa absen pulang)
            $netPresent = max(0, $attPresent - ($attIncomplete * 0.5));
            $pEff = $effectiveExpected > 0 ? ($netPresent / $effectiveExpected) * 100 : 0;
            
            // Efisiensi Jurnal (E-Jurnal): Aturan Kepatuhan Scan Harian Guru
            // 1. Jika guru hadir di hari mengajar tapi 0x scan E-Jurnal, maka KENA POTONGAN pada hari itu.
            // 2. Jika guru sudah scan E-Jurnal min. 1x di hari hadir tersebut, TIDAK KENA POTONGAN pada hari itu.
            // 3. Jika poin E-Jurnal 0, ditetapkan min 0% (TIDAK PERNAH MINUS).
            if ($attPresent > 0) {
                $forgottenDays = max(0, $attPresent - $jrnDays);
                $activeScannedDays = max(0, $attPresent - $forgottenDays);
                $jEff = max(0, ($activeScannedDays / $attPresent) * 100);

                // Micro-bonus jika guru melakukan scan lebih banyak kelas pada hari-hari hadir tersebut
                if ($jrnTotal > $jrnDays && $jEff > 0) {
                    $extraBonus = ($jrnTotal - $jrnDays) * 2;
                    $jEff = min(100, $jEff + $extraBonus);
                }
            } else {
                $jEff = 0;
            }
            
            $expectedEntries = $attPresent;
            $baseScore = ($pEff * 0.5) + ($jEff * 0.5);

            // Micro-bonus tie-breaker
            $tieBreakerBonus = ($pEff * 0.001)
                + ($attPresent * 0.01)
                + ($jrnTotal * 0.005)
                + ($totalJamMengajar * 0.002)
                + ($jrnMinutes * 0.00001)
                - ($attIncomplete * 0.005);

            $finalScore = $baseScore + max(0, $tieBreakerBonus);

            if ($attPresent > 0 || $jrnTotal > 0 || $attIncomplete > 0) {
                $leaderboard->push([
                    'nik' => $teacher->nik,
                    'nama_lengkap' => $teacher->nama_lengkap,
                    'foto' => $teacher->foto,
                    'score' => round($finalScore, 2),
                    'stats' => [
                        'presence_efficiency' => round($pEff, 2),
                        'journal_efficiency'  => round($jEff, 2),
                        'actual_days_present' => $attPresent,
                        'incomplete_count'    => $attIncomplete,
                        'journal_days'        => $jrnDays,
                        'journal_count'       => $jrnTotal,
                        'journal_minutes'     => $jrnMinutes,
                        'total_jam_mengajar'  => $totalJamMengajar,
                        'expected_entries'    => $expectedEntries,
                        'expected_days'       => $expectedDays
                    ]
                ]);
            }
        }

        return $leaderboard->sort(function($a, $b) {
            // 1. Total Skor Utama (Tertinggi)
            if ($b['score'] != $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            // 2. Efisiensi Kehadiran (Tertinggi)
            if ($b['stats']['presence_efficiency'] != $a['stats']['presence_efficiency']) {
                return $b['stats']['presence_efficiency'] <=> $a['stats']['presence_efficiency'];
            }
            // 3. Jumlah Hari Kehadiran Fisik (Tertinggi)
            if ($b['stats']['actual_days_present'] != $a['stats']['actual_days_present']) {
                return $b['stats']['actual_days_present'] <=> $a['stats']['actual_days_present'];
            }
            // 4. Total Scan Jurnal (Tertinggi)
            if ($b['stats']['journal_count'] != $a['stats']['journal_count']) {
                return $b['stats']['journal_count'] <=> $a['stats']['journal_count'];
            }
            // 5. Total Jam Mengajar (Tertinggi)
            if ($b['stats']['total_jam_mengajar'] != $a['stats']['total_jam_mengajar']) {
                return $b['stats']['total_jam_mengajar'] <=> $a['stats']['total_jam_mengajar'];
            }
            // 6. Total Menit Mengajar Jurnal (Tertinggi)
            if ($b['stats']['journal_minutes'] != $a['stats']['journal_minutes']) {
                return $b['stats']['journal_minutes'] <=> $a['stats']['journal_minutes'];
            }
            // 7. Penalti Lupa Absen Pulang (Terendah)
            if ($a['stats']['incomplete_count'] != $b['stats']['incomplete_count']) {
                return $a['stats']['incomplete_count'] <=> $b['stats']['incomplete_count'];
            }
            return 0;
        })->values();
    }

    /**
     * Public method for Top Teachers (compatibility)
     */
    public function getTopTeachers($limit = 10)
    {
        return $this->computeFullLeaderboard()->take($limit);
    }

    /**
     * Get Top Teachers AND current user's specific rank
     */
    public function getLeaderboardWithUserRank($userNik, $limit = 10)
    {
        $config = $this->getLeaderboardConfig();
        $fullList = $this->computeFullLeaderboard();
        
        $topTeachers = $fullList->take($limit);
        
        // Find user in the full list to get their rank
        $userRank = null;
        foreach ($fullList as $index => $item) {
            if ($item['nik'] == $userNik) {
                $userRank = $item;
                $userRank['rank'] = $index + 1;
                break;
            }
        }
        
        // If user not found (no scores yet), provide empty stats
        if (!$userRank) {
            $userRank = [
                'nik' => $userNik,
                'score' => 0,
                'rank' => '?',
                'stats' => [
                    'presence_efficiency' => 0,
                    'journal_efficiency' => 0,
                    'actual_days_present' => 0,
                    'journal_count' => 0,
                    'expected_days' => 0
                ]
            ];
        }

        return [
            'topTeachers'    => $topTeachers,
            'userRank'       => $userRank,
            'isPeriodActive' => $config['isPeriodActive'] ?? true,
            'periodMessage'  => $config['message'] ?? '',
        ];
    }


    /**
     * Compute the full Piket (PPT) leaderboard
     * Score = Attendance Efficiency (50%) + Monitoring WA Efficiency (50%)
     * Attendance: only counts days with BOTH jam_in AND jam_out
     * Monitoring: counts successful WA sends from piket_wa_log
     */
    private function computePiketLeaderboard()
    {
        $today = Carbon::now();
        $config = $this->getLeaderboardConfig();

        $startDate = $config['startDate'];
        $endDate = $config['endDate'];

        // 2. Count total weekdays (Mon-Sat) in range — diratakan untuk semua PPT
        $totalWorkDays = 0;
        $temp = Carbon::parse($startDate);
        while ($temp->lte($today)) {
            if ($temp->dayOfWeek >= 1 && $temp->dayOfWeek <= 6) { // Mon=1 to Sat=6
                $totalWorkDays++;
            }
            $temp->addDay();
        }

        // 3. Get PPT employees
        $piketStaff = Karyawan::where('kode_dept', 'PPT')->get();
        $niks = $piketStaff->pluck('nik')->toArray();

        if (empty($niks)) return collect();

        // 4. Batch query: attendance
        $todayStr = $today->format('Y-m-d');
        $attendanceMap = DB::table('presensi')
            ->select('nik',
                DB::raw("SUM(IF(status='h' AND (jam_out IS NOT NULL OR tgl_presensi = '$todayStr'), 1, 0)) as present"),
                DB::raw("SUM(IF(status='h' AND jam_in IS NOT NULL AND jam_out IS NULL AND tgl_presensi < '$todayStr', 1, 0)) as incomplete")
            )
            ->whereIn('nik', $niks)
            ->whereBetween('tgl_presensi', [$startDate, $endDate])
            ->groupBy('nik')
            ->get()
            ->keyBy('nik');

        // 5. Batch query: monitoring WA activity (successful sends only)
        $waMap = DB::table('piket_wa_log')
            ->select('nik_piket', DB::raw("COUNT(*) as total"))
            ->whereIn('nik_piket', $niks)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('nik_piket')
            ->get()
            ->keyBy('nik_piket');

        // 6. Calculate scores
        $leaderboard = collect();
        foreach ($piketStaff as $staff) {
            $attRecord = $attendanceMap->get($staff->nik);
            $attPresent = $attRecord->present ?? 0;
            $attIncomplete = $attRecord->incomplete ?? 0;

            $waRecord = $waMap->get($staff->nik);
            $waTotal = $waRecord->total ?? 0;

            $effectiveExpected = $totalWorkDays > 0 ? $totalWorkDays : max($attPresent, 1);
            
            $netPresent = max(0, $attPresent - ($attIncomplete * 0.5));
            $attEff = $effectiveExpected > 0 ? ($netPresent / $effectiveExpected) * 100 : 0;
            $monEff = $effectiveExpected > 0 ? min(100, ($waTotal / $effectiveExpected) * 100) : 0;

            $baseScore = ($attEff * 0.5) + ($monEff * 0.5);

            // Micro-bonus desimal berbasis tie-breaker (agar skor piket tidak 50 kembar tanpa mengubah urutan peringkat)
            $tieBreakerBonus = ($attEff * 0.001)
                + ($attPresent * 0.01)
                + ($waTotal * 0.01)
                - ($attIncomplete * 0.005);

            $finalScore = $baseScore + max(0, $tieBreakerBonus);

            if ($attPresent > 0 || $waTotal > 0 || $attIncomplete > 0) {
                $leaderboard->push([
                    'nik' => $staff->nik,
                    'nama_lengkap' => $staff->nama_lengkap,
                    'foto' => $staff->foto,
                    'score' => round($finalScore, 2),
                    'stats' => [
                        'attendance_efficiency' => round($attEff, 2),
                        'monitoring_efficiency' => round($monEff, 2),
                        'actual_days_present'   => $attPresent,
                        'incomplete_count'      => $attIncomplete,
                        'wa_count'              => $waTotal,
                        'expected_days'         => $totalWorkDays
                    ]
                ]);
            }
        }

        return $leaderboard->sort(function($a, $b) {
            // 1. Total Skor Utama (Tertinggi)
            if ($b['score'] != $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            // 2. Efisiensi Kehadiran Presensi (Tertinggi)
            if ($b['stats']['attendance_efficiency'] != $a['stats']['attendance_efficiency']) {
                return $b['stats']['attendance_efficiency'] <=> $a['stats']['attendance_efficiency'];
            }
            // 3. Jumlah Hari Kehadiran Fisik Nyata (Tertinggi)
            if ($b['stats']['actual_days_present'] != $a['stats']['actual_days_present']) {
                return $b['stats']['actual_days_present'] <=> $a['stats']['actual_days_present'];
            }
            // 4. Jumlah Monitoring WA (Tertinggi)
            if ($b['stats']['wa_count'] != $a['stats']['wa_count']) {
                return $b['stats']['wa_count'] <=> $a['stats']['wa_count'];
            }
            // 5. Penalti Lupa Absen Pulang (Terendah / Paling Sedikit)
            if ($a['stats']['incomplete_count'] != $b['stats']['incomplete_count']) {
                return $a['stats']['incomplete_count'] <=> $b['stats']['incomplete_count'];
            }
            return 0;
        })->values();
    }

    /**
     * Public method for Top Piket
     */
    public function getTopPiket($limit = 10)
    {
        return $this->computePiketLeaderboard()->take($limit);
    }

    /**
     * Get Top Piket AND current user's specific rank
     */
    public function getPiketLeaderboardWithUserRank($userNik, $limit = 10)
    {
        $fullList = $this->computePiketLeaderboard();
        
        $topPiket = $fullList->take($limit);
        
        $userRank = null;
        foreach ($fullList as $index => $item) {
            if ($item['nik'] == $userNik) {
                $userRank = $item;
                $userRank['rank'] = $index + 1;
                break;
            }
        }
        
        if (!$userRank) {
            $userRank = [
                'nik' => $userNik,
                'score' => 0,
                'rank' => '?',
                'stats' => [
                    'attendance_efficiency' => 0,
                    'monitoring_efficiency' => 0,
                    'actual_days_present' => 0,
                    'wa_count' => 0,
                    'expected_days' => 0
                ]
            ];
        }

        $config = $this->getLeaderboardConfig();

        return [
            'topPiket'       => $topPiket,
            'userRank'       => $userRank,
            'isPeriodActive' => $config['isPeriodActive'] ?? true,
            'periodMessage'  => $config['message'] ?? '',
        ];
    }


    private function getDayNameIndo($day)
    {
        $days = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
        return $days[$day] ?? $day;
    }
}
