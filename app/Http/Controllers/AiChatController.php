<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = trim($request->message);
        $user = Auth::guard('karyawan')->user();

        $namaGuru = $user ? $user->nama_lengkap : 'Bapak/Ibu Guru';
        $jabatan = $user ? $user->jabatan : 'Guru';

        $apiKey = env('GEMINI_API_KEY');

        // Layer 1: Call Google Gemini API
        if (!empty($apiKey)) {
            $systemInstruction = "Kamu adalah AI Siguru APP, asisten virtual cerdas & pakar sistem untuk aplikasi SiGuru di Sekolah / Yayasan Miftahul Khaer. "
                . "Kamu sedang melayani bapak/ibu guru {$namaGuru} ({$jabatan}). "
                . "Kamu memiliki pengetahuan mendalam mengenai seluruh arsitektur, aturan, dan fitur aplikasi SiGuru sebagai berikut:\n\n"
                . "1. TENTANG APLIKASI SIGURU:\n"
                . "- SiGuru APP adalah aplikasi manajemen sekolah & presensi guru berbasis web & mobile terintegrasi di Sekolah Miftahul Khaer.\n"
                . "- Menggunakan teknologi presensi online berbasis koordinat lokasi (GPS) dan verifikasi kamera (Face Capture / Face Recognition).\n\n"
                . "2. ATURAN PRESENSI & JAM KERJA:\n"
                . "- Jam Masuk bawaan: 07:00 WIB. Jam Pulang: 12:10 WIB (atau sesuai jam kerja cabang/piket).\n"
                . "- Absen Masuk: Wajib dilakukan di lokasi sekolah (dalam radius GPS). Dibatasi sampai jam pulang.\n"
                . "- Absen Pulang: Bisa dilakukan kapan saja setelah absen masuk tanpa batasan jam minimal.\n"
                . "- Status Kehadiran: Hadir (H), Izin (I), Sakit (S), Cuti (C), dan Alfa (A).\n"
                . "- Penilaian Alfa (A): Hanya dihitung pada hari di mana guru MEMILIKI jadwal mengajar tetapi tidak hadir tanpa keterangan izin. Hari tanpa jadwal mengajar atau libur TIDAK dihitung Alfa.\n\n"
                . "3. PANDUAN FITUR LENGKAP:\n"
                . "- Absen Masuk/Pulang: Ke Dashboard > klik 'Absen Masuk' (Hijau) / 'Absen Pulang' (Merah) > izinkan GPS & kamera > posisikan wajah > ambil foto.\n"
                . "- Mengatasi Gagal Absen: Cek koneksi internet, pastikan GPS HP aktif & diizinkan di browser, bersihkan cache browser jika kamera tidak muncul, atau hubungi admin piket jika di luar radius.\n"
                . "- E-Jurnal KBM: Klik menu 'E-Jurnal' di dashboard > pilih kelas & mapel > isi rangkuman materi pembelajaran & jumlah siswa hadir > simpan.\n"
                . "- Status Mengajar: Klik menu 'Status Mengajar' di dashboard untuk melihat rincian jadwal ngajar harian & durasi jam kerja.\n"
                . "- Fitur Kelasku (Wali Kelas): Khusus Wali Kelas untuk memantau data dan kehadiran siswa kelas binaan.\n"
                . "- Monitoring Piket: Khusus Petugas Piket untuk memantau kehadiran guru harian dan mengirim notifikasi WhatsApp.\n"
                . "- Pengajuan Izin/Sakit/Cuti: Pilih menu 'Pengajuan' di navigasi bawah > pilih jenis izin > isi tanggal & alasan > lampirkan surat dokter/berkas > submit.\n"
                . "- Riwayat & Rekap: Pilih menu 'Riwayat' di navigasi bawah > filter bulan & tahun untuk melihat total Hadir, Izin, Sakit, Cuti, dan Alfa.\n"
                . "- Slip Gaji: Pilih menu 'Slip Gaji' di dashboard untuk melihat rincian honorarium bulanan dan cetak slip.\n"
                . "- Edit Profil & Password: Pilih menu 'Profil' di pojok kanan bawah > tekan ikon kamera untuk ubah foto profil, atau perbarui nomor HP & kata sandi.\n"
                . "- Papan Skor & Ranking (Leaderboard): Menampilkan Top 3 Guru Teladan (🥇 Juara 1 Gold, 🥈 Juara 2 Silver, 🥉 Juara 3 Bronze) berbasis efisiensi kehadiran & e-jurnal.\n\n"
                . "ATURAN MENJAWAB:\n"
                . "- Jawablah pertanyaan apapun sejelas dan mendalam mungkin sesuai konteks aplikasi di atas.\n"
                . "- Gunakan bahasa Indonesia yang sopan, ramah, profesional, berstruktur, dan mudah dipahami.\n"
                . "- Jika ditanya langkah-langkah, selalu berikan urutan nomor (1, 2, 3, dst.) yang lengkap.";

            $models = [
                'gemini-flash-latest',
                'gemini-3.5-flash',
                'gemini-3-flash-preview',
                'gemini-2.0-flash',
            ];

            foreach ($models as $modelName) {
                try {
                    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(15)->post($endpoint, [
                        'system_instruction' => [
                            'parts' => [['text' => $systemInstruction]]
                        ],
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $userMessage]]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.6,
                            'maxOutputTokens' => 1024,
                        ]
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($replyText) {
                            $formattedReply = $this->formatMarkdownToHtml($replyText);

                            return response()->json([
                                'status' => 'success',
                                'reply' => $formattedReply,
                            ]);
                        }
                    } else {
                        Log::warning("Gemini model {$modelName} API error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::warning("Gemini model {$modelName} exception: " . $e->getMessage());
                }
            }
        }

        // Layer 2: Smart Built-in AI Engine Fallback (Tepat Sasaran & Akurat)
        $aiReply = $this->generateSmartAiFallback($userMessage, $namaGuru, $jabatan);

        return response()->json([
            'status' => 'success',
            'reply' => $aiReply,
        ]);
    }

    /**
     * Konversi Markdown Gemini ke HTML secara aman tanpa merusak kalimat
     */
    private function formatMarkdownToHtml($text)
    {
        if (empty($text)) return '';

        // Clean HTML entities first cleanly
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 1. **bold** -> <strong>bold</strong>
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);

        // 2. * bullet -> • bullet
        $text = preg_replace('/^[*\-]\s+(.*?)$/m', '• $1', $text);

        // 3. Newlines to <br>
        $text = nl2br($text);

        // 4. Clean excessive <br>
        $text = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br><br>', $text);

        return $text;
    }

    /**
     * Engine Asisten Pintar AI Siguru APP (Fallback Tepat Sasaran)
     */
    private function generateSmartAiFallback($message, $namaGuru, $jabatan)
    {
        $msg = strtolower($message);

        // 1. Absen & Langkah Absen
        if (preg_match('/(langkah|cara|gimana|bagaimana|tutorial|petunjuk)/i', $msg) && preg_match('/(absen|absensi|masuk|pulang)/i', $msg)) {
            return "<strong>Langkah-langkah Melakukan Absen:</strong><br><br>"
                . "1. Buka <strong>Dashboard Utama</strong> aplikasi SiGuru.<br>"
                . "2. Tekan tombol hijau <strong>'Absen Masuk'</strong> (atau tombol merah <strong>'Absen Pulang'</strong>).<br>"
                . "3. Izinkan akses Lokasi (GPS) dan Kamera pada HP Anda.<br>"
                . "4. Posisikan wajah tepat di dalam bingkai kamera.<br>"
                . "5. Tekan tombol ambil foto untuk menyelesaikan presensi.";
        }

        if (preg_match('/(absen|absensi|masuk|pulang)/i', $msg) && !preg_match('/(gagal|lupa|histori|rekap)/i', $msg)) {
            return "<strong>Cara Melakukan Absen:</strong><br><br>"
                . "1. Buka Dashboard aplikasi SiGuru.<br>"
                . "2. Klik tombol <strong>Absen Masuk</strong> atau <strong>Absen Pulang</strong>.<br>"
                . "3. Ambil foto diri dengan GPS dan kamera aktif.";
        }

        if (preg_match('/(gagal|kendala|error|bisa|sulit)/i', $msg) && preg_match('/(absen|foto|gps|lokasi)/i', $msg)) {
            return "<strong>Langkah Mengatasi Gagal Absen:</strong><br><br>"
                . "1. Pastikan koneksi internet HP aktif dan stabil.<br>"
                . "2. Aktifkan Fitur Lokasi (GPS) & berikan izin kamera pada browser.<br>"
                . "3. Coba muat ulang (refresh) halaman aplikasi.<br>"
                . "4. Jika masih berkendala, segera koordinasikan dengan admin/piket.";
        }

        if (preg_match('/(lupa)/i', $msg) && preg_match('/(absen|pulang|masuk)/i', $msg)) {
            return "Jika bapak/ibu <strong>lupa melakukan absen pulang</strong>:<br><br>"
                . "1. Hubungi admin atau atasan untuk konfirmasi.<br>"
                . "2. Admin dapat menyesuaikan data presensi melalui panel admin.<br>"
                . "3. Pastikan untuk selalu melakukan absen tepat waktu saat jam pulang usai.";
        }

        // 2. Aplikasi SiGuru / Penjelasan Aplikasi
        if (preg_match('/(tentang|apa itu|tahu|mengenai)/i', $msg) && preg_match('/(aplikasi|siguru|sistem)/i', $msg)) {
            return "Tentu saja saya tahu! <strong>SiGuru APP</strong> adalah sistem aplikasi manajemen sekolah terintegrasi di Yayasan Miftahul Khaer yang digunakan untuk presensi online (kamera & GPS), jadwal mengajar, pengisian e-jurnal KBM, pengajuan izin/cuti, serta laporan absensi guru.";
        }

        // 3. Greetings (Gunakan Word Boundary \b agar tidak salah cocok)
        if (preg_match('/\b(halo|hallo|hi|hai|pagi|siang|sore|malam|ping|bos|bro|admin)\b/i', $msg)) {
            return "Halo <strong>{$namaGuru}</strong>! Ada yang bisa <strong>AI Siguru APP</strong> bantu terkait presensi, jadwal mengajar, e-jurnal, atau pengajuan izin hari ini?";
        }

        if (preg_match('/(terima kasih|makasih|thanks|thx|ok|oke|baik)/i', $msg)) {
            return "Sama-sama <strong>{$namaGuru}</strong>! Senang bisa membantu Anda. Selamat bertugas!";
        }

        // 4. Jadwal & Mengajar
        if (preg_match('/(jadwal|mengajar|mapel|mata pelajaran|kelas)/i', $msg)) {
            return "<strong>Cara Melihat Jadwal Mengajar:</strong><br><br>"
                . "1. Masuk ke menu <strong>'Status Mengajar'</strong> di dashboard utama.<br>"
                . "2. Lihat daftar jam mengajar, mata pelajaran, dan status kehadiran kelas Anda hari ini.";
        }

        // 5. E-Jurnal
        if (preg_match('/(jurnal|e-jurnal|materi|kbm)/i', $msg)) {
            return "<strong>Cara Mengisi E-Jurnal:</strong><br><br>"
                . "1. Klik ikon <strong>'E-Jurnal'</strong> pada dashboard.<br>"
                . "2. Pilih kelas & mata pelajaran.<br>"
                . "3. Tuliskan rangkuman materi KBM hari ini dan simpan jurnal.";
        }

        // 6. Pengajuan Izin / Cuti
        if (preg_match('/(izin|sakit|cuti|pengajuan)/i', $msg)) {
            return "<strong>Cara Mengajukan Izin / Sakit / Cuti:</strong><br><br>"
                . "1. Pilih menu <strong>'Pengajuan'</strong> pada navigasi bawah.<br>"
                . "2. Pilih jenis izin, isi tanggal & alasan, lalu kirim pengajuan.";
        }

        // 7. General Fallback
        return "Terima kasih atas pertanyaannya, <strong>{$namaGuru}</strong>.<br><br>"
            . "Sebagai <strong>AI Siguru APP</strong>, saya siap membantu bapak/ibu guru mengenai:<br>"
            . "• Langkah-langkah Absen Masuk & Pulang<br>"
            . "• Informasi Jadwal Mengajar & E-Jurnal<br>"
            . "• Prosedur Pengajuan Izin / Cuti / Sakit<br>"
            . "• Rekapitulasi Presensi & Slip Gaji<br><br>"
            . "Silakan ketik pertanyaan spesifik Anda di bawah!";
    }
}
