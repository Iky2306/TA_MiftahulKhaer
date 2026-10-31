<form action="/pengumuman/{{ $pengumuman->id }}/update" method="POST" id="frmEditPengumuman" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                        <path d="M16 3v4"></path>
                        <path d="M8 3v4"></path>
                        <path d="M4 11h16"></path>
                        <path d="M11 15h1"></path>
                        <path d="M12 15v3"></path>
                    </svg>
                </span>
                <input type="date" value="{{ $pengumuman->tgl_pengumuman }}" class="form-control" name="tgl_pengumuman" placeholder="Tanggal Pengumuman" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-letter-t" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M6 4l12 0"></path>
                        <path d="M12 4l0 16"></path>
                    </svg>
                </span>
                <input type="text" class="form-control" name="judul" value="{{ $pengumuman->judul }}" placeholder="Judul Pengumuman" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <textarea name="konten" id="konten_edit" cols="30" rows="5" class="form-control" placeholder="Isi Pengumuman" required>{{ $pengumuman->konten }}</textarea>
            </div>
        </div>
    </div>
    <div class="row text-muted" style="font-size: 0.75rem;">
        <div class="col-12">
            <label class="form-label mb-1">Foto Pengumuman (Opsional)</label>
            @if($pengumuman->foto)
                <div class="mb-2 text-center p-2 border rounded bg-light">
                    <img src="{{ Storage::url('uploads/pengumuman/' . $pengumuman->foto) }}" class="img-thumbnail" style="max-height: 140px; border-radius: 8px;" alt="Foto Saat Ini">
                    <div class="text-success small mt-1">File saat ini: {{ $pengumuman->foto }}</div>
                </div>
            @endif
            <div class="input-group mb-1">
                <input type="file" class="form-control" name="foto" id="inputEditFoto" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,image/jfif,image/*">
            </div>
            <div class="form-text text-muted mb-2" style="font-size: 0.75rem;">
                Format: JPG, JPEG, PNG, WEBP, GIF (Maksimal 10MB). Biarkan kosong jika tidak ingin mengubah foto.
            </div>
            <div id="previewEditFotoContainer" class="mb-3 text-center" style="display: none;">
                <div class="small text-info mb-1 font-weight-bold">Pratinjau Foto Baru:</div>
                <img id="previewEditFoto" src="#" alt="Preview Foto Baru" class="img-thumbnail" style="max-height: 140px; border-radius: 8px;">
                <div class="text-muted small mt-1" id="previewEditFotoInfo"></div>
            </div>
        </div>
    </div>
    <div class="row text-muted" style="font-size: 0.75rem;">
        <div class="col-12">
            <label class="form-label mb-1">Dokumen Lampiran (Opsional)</label>
            @if($pengumuman->doc_file)
                <div class="mb-2 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                    <span class="text-success small">File saat ini: {{ $pengumuman->doc_file }}</span>
                    <a href="{{ Storage::url('uploads/pengumuman/' . $pengumuman->doc_file) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 11px;">Unduh</a>
                </div>
            @endif
            <div class="input-group mb-1">
                <input type="file" class="form-control" name="doc_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
            </div>
            <div class="form-text text-muted mb-3" style="font-size: 0.75rem;">
                Format: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR (Maksimal 20MB). Biarkan kosong jika tidak ingin mengubah dokumen.
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"></path>
                        <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"></path>
                    </svg>
                </span>
                <input type="text" class="form-control" name="link" value="{{ $pengumuman->link }}" placeholder="Tautan/Link (Opsional)">
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                </svg>
                Update Pengumuman
            </button>
        </div>
    </div>
</form>

<script>
    $("#inputEditFoto").change(function() {
        var file = this.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Terlalu Besar',
                    text: 'Ukuran foto maksimal adalah 10 MB'
                });
                $(this).val('');
                $("#previewEditFotoContainer").hide();
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#previewEditFoto").attr('src', e.target.result);
                var sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
                $("#previewEditFotoInfo").text(file.name + ' (' + sizeInMb + ' MB)');
                $("#previewEditFotoContainer").show();
            }
            reader.readAsDataURL(file);
        } else {
            $("#previewEditFotoContainer").hide();
        }
    });
</script>
