<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::orderBy('tgl_pengumuman', 'desc')->get();
        return view('pengumuman.index', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $messages = [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'konten.required' => 'Isi pengumuman wajib diisi.',
            'tgl_pengumuman.required' => 'Tanggal pengumuman wajib diisi.',
            'tgl_pengumuman.date' => 'Format tanggal tidak valid.',
            'foto.image' => 'File yang diunggah harus berupa gambar valid.',
            'foto.mimes' => 'Format foto harus berupa: jpeg, png, jpg, gif, webp, atau jfif.',
            'foto.max' => 'Ukuran foto maksimal adalah 10 MB.',
            'doc_file.mimes' => 'Format dokumen harus berupa: pdf, doc, docx, xls, xlsx, zip, atau rar.',
            'doc_file.max' => 'Ukuran dokumen maksimal adalah 20 MB.',
        ];

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'tgl_pengumuman' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif,bmp|max:10240',
            'doc_file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:20480',
        ], $messages);

        try {
            $data = [
                'judul' => $request->judul,
                'konten' => $request->konten,
                'tgl_pengumuman' => $request->tgl_pengumuman,
                'link' => $request->link,
                'status' => 1
            ];

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $extension = strtolower($foto->getClientOriginalExtension());
                $foto_name = time() . '_' . uniqid() . '_foto.' . $extension;
                $foto->storeAs('public/uploads/pengumuman', $foto_name);
                $data['foto'] = $foto_name;
            }

            if ($request->hasFile('doc_file')) {
                $doc = $request->file('doc_file');
                $extension = strtolower($doc->getClientOriginalExtension());
                $doc_name = time() . '_' . uniqid() . '_doc.' . $extension;
                $doc->storeAs('public/uploads/pengumuman', $doc_name);
                $data['doc_file'] = $doc_name;
            }

            Pengumuman::create($data);
            return Redirect::back()->with(['success' => 'Pengumuman Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $pengumuman = Pengumuman::find($id);
        return view('pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'konten.required' => 'Isi pengumuman wajib diisi.',
            'tgl_pengumuman.required' => 'Tanggal pengumuman wajib diisi.',
            'tgl_pengumuman.date' => 'Format tanggal tidak valid.',
            'foto.image' => 'File yang diunggah harus berupa gambar valid.',
            'foto.mimes' => 'Format foto harus berupa: jpeg, png, jpg, gif, webp, atau jfif.',
            'foto.max' => 'Ukuran foto maksimal adalah 10 MB.',
            'doc_file.mimes' => 'Format dokumen harus berupa: pdf, doc, docx, xls, xlsx, zip, atau rar.',
            'doc_file.max' => 'Ukuran dokumen maksimal adalah 20 MB.',
        ];

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'tgl_pengumuman' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif,bmp|max:10240',
            'doc_file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:20480',
        ], $messages);

        try {
            $pengumuman = Pengumuman::findOrFail($id);
            $data = [
                'judul' => $request->judul,
                'konten' => $request->konten,
                'tgl_pengumuman' => $request->tgl_pengumuman,
                'link' => $request->link,
            ];

            if ($request->hasFile('foto')) {
                if ($pengumuman->foto && Storage::exists('public/uploads/pengumuman/' . $pengumuman->foto)) {
                    Storage::delete('public/uploads/pengumuman/' . $pengumuman->foto);
                }
                $foto = $request->file('foto');
                $extension = strtolower($foto->getClientOriginalExtension());
                $foto_name = time() . '_' . uniqid() . '_foto.' . $extension;
                $foto->storeAs('public/uploads/pengumuman', $foto_name);
                $data['foto'] = $foto_name;
            }

            if ($request->hasFile('doc_file')) {
                if ($pengumuman->doc_file && Storage::exists('public/uploads/pengumuman/' . $pengumuman->doc_file)) {
                    Storage::delete('public/uploads/pengumuman/' . $pengumuman->doc_file);
                }
                $doc = $request->file('doc_file');
                $extension = strtolower($doc->getClientOriginalExtension());
                $doc_name = time() . '_' . uniqid() . '_doc.' . $extension;
                $doc->storeAs('public/uploads/pengumuman', $doc_name);
                $data['doc_file'] = $doc_name;
            }

            $pengumuman->update($data);
            return Redirect::back()->with(['success' => 'Pengumuman Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $pengumuman = Pengumuman::findOrFail($id);
            if ($pengumuman->foto && Storage::exists('public/uploads/pengumuman/' . $pengumuman->foto)) {
                Storage::delete('public/uploads/pengumuman/' . $pengumuman->foto);
            }
            if ($pengumuman->doc_file && Storage::exists('public/uploads/pengumuman/' . $pengumuman->doc_file)) {
                Storage::delete('public/uploads/pengumuman/' . $pengumuman->doc_file);
            }
            $pengumuman->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Dihapus: ' . $e->getMessage()]);
        }
    }

    public function setstatus($id)
    {
        try {
            $pengumuman = Pengumuman::findOrFail($id);
            $status = $pengumuman->status == 1 ? 0 : 1;
            $pengumuman->update([
                'status' => $status
            ]);
            return Redirect::back()->with(['success' => 'Status Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Status Gagal Diupdate']);
        }
    }
}
