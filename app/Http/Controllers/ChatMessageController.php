<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function fetch()
    {
        $messages = ChatMessage::with(['karyawan', 'replyTo.karyawan'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    /**
     * Menyimpan pesan baru (teks atau foto)
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'reply_to_id' => 'nullable|integer|exists:chat_messages,id',
            'photo' => 'nullable|image|max:10240', // Maksimal 10MB
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;
        $fileName = null;

        // Proses Unggah Foto
        if ($request->hasFile('photo')) {
            try {
                $image = $request->file('photo');
                $fileName = time() . '_' . uniqid() . '.jpg';
                $folderPath = "public/uploads/chat/";
                
                if (!Storage::exists($folderPath)) {
                    Storage::makeDirectory($folderPath);
                }

                // Resolusi path absolut menggunakan Storage::path
                $sourcePath = $image->getRealPath();
                $destinationPath = Storage::path($folderPath . $fileName);

                // Kompresi gambar (Lebar max 1000px, kualitas 60%)
                $success = $this->compressImage($sourcePath, $destinationPath, 1000, 60);
                
                if (!$success) {
                    Log::error("Kompresi GD gagal untuk: " . $sourcePath);
                    return response()->json(['status' => 'error', 'message' => 'Gagal memproses gambar'], 500);
                }
            } catch (\Exception $e) {
                Log::error("Chat Upload Error: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()], 500);
            }
        }

        $chat = new ChatMessage();
        $chat->nik = $nik;
        $chat->message = $request->message ?? '';
        $chat->file = $fileName;
        $chat->reply_to_id = $request->reply_to_id;
        $chat->save();

        return response()->json(['status' => 'success', 'data' => $chat]);
    }

    /**
     * Memperbarui waktu terakhir kali chat dilihat oleh user
     */
    public function markAsRead()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        
        // Update kolom last_chat_view_at di tabel karyawan
        \DB::table('karyawan')->where('nik', $nik)->update([
            'last_chat_view_at' => now()
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Mengambil jumlah pesan yang belum dibaca
     */
    public function getUnreadCount()
    {
        $user = Auth::guard('karyawan')->user();
        
        // Cek apakah kolom sudah ada untuk menghindari error jika SQL belum dijalankan
        try {
            $lastView = $user->last_chat_view_at;
        } catch (\Exception $e) {
            return response()->json(['unread_count' => 0]);
        }

        $query = ChatMessage::query();
        
        if ($lastView) {
            $query->where('created_at', '>', $lastView);
        }

        // Jangan hitung pesan kita sendiri sebagai "belum dibaca"
        $count = $query->where('nik', '!=', $user->nik)->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Helper untuk kompresi gambar agar hemat penyimpanan
     */
    private function compressImage($sourcePath, $destinationPath, $maxWidth, $quality)
    {
        list($width, $height, $type) = getimagesize($sourcePath);
        
        $newWidth = $width;
        $newHeight = $height;
        
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = round($height * ($maxWidth / $width));
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparency for PNG/GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        $success = imagejpeg($newImage, $destinationPath, $quality);
        
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $success;
    }

    public function destroy($id)
    {
        $chat = ChatMessage::findOrFail($id);
        $nik = Auth::guard('karyawan')->user()->nik;

        if ($chat->nik == $nik) {
            if ($chat->file) {
                Storage::delete('public/uploads/chat/' . $chat->file);
            }
            $chat->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Tidak diizinkan'], 403);
    }
}
