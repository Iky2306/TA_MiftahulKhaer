@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Admin Panel</div>
                <h2 class="page-title">Konfirmasi Kehadiran</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @if(Session::get('success'))
            <div class="alert alert-success alert-dismissible mb-3">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(Session::get('error'))
            <div class="alert alert-danger alert-dismissible mb-3">
                {{ Session::get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="/panel/konfirmasikehadiran">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label form-label-sm">Nama / NIK</label>
                            <input type="text" name="nama" value="{{ request('nama') }}" class="form-control form-control-sm" placeholder="Cari nama...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm">Dari Tanggal</label>
                            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm">Sampai Tanggal</label>
                            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Pending</option>
                                <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>Disetujui</option>
                                <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary btn-sm">Filter</button>
                            <a href="/panel/konfirmasikehadiran" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover table-bordered card-table" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th width="30">No</th>
                                <th>Nama / NIK</th>
                                <th width="95">Tanggal</th>
                                <th width="70">Hari</th>
                                <th width="90">Jam</th>
                                <th>Alasan</th>
                                <th width="60">Bukti</th>
                                <th width="80">Diajukan</th>
                                <th width="80">Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $namaHari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp
                            @forelse($data as $d)
                                @php
                                    $hari = $namaHari[date('w', strtotime($d->tanggal))];
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $no++ }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $d->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $d->nik }} &middot; {{ $d->jabatan }}</small>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($d->tanggal)) }}</td>
                                    <td>{{ $hari }}</td>
                                    <td class="text-center">
                                        {{ date('H:i', strtotime($d->jam_masuk)) }}<br>
                                        <small class="text-muted">{{ date('H:i', strtotime($d->jam_keluar)) }}</small>
                                    </td>
                                    <td>
                                        <div style="max-width:200px; white-space:pre-wrap; font-size:12px;">{{ $d->alasan }}</div>
                                        @if($d->catatan_admin)
                                            <div class="text-muted mt-1" style="font-size:11px;"><em>Catatan: {{ $d->catatan_admin }}</em></div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($d->bukti)
                                            <a href="{{ Storage::url('uploads/konfirmasi_kehadiran/' . $d->bukti) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:10px; padding:2px 6px;">
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted" style="font-size:11px;">{{ date('d/m/Y H:i', strtotime($d->created_at)) }}</td>
                                    <td>
                                        @if($d->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($d->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->status === 'pending')
                                            <button class="btn btn-sm btn-success mb-1 w-100"
                                                    onclick="showApprove({{ $d->id }}, '{{ $d->nama_lengkap }}', '{{ date('d/m/Y', strtotime($d->tanggal)) }}')">
                                                Setujui
                                            </button>
                                            <button class="btn btn-sm btn-danger w-100"
                                                    onclick="showReject({{ $d->id }}, '{{ $d->nama_lengkap }}', '{{ date('d/m/Y', strtotime($d->tanggal)) }}')">
                                                Tolak
                                            </button>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">
                                                Oleh: {{ $d->approved_by ?? '-' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-5">Tidak ada data pengajuan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($data->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $data->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Modal Setujui --}}
<div class="modal modal-blur fade" id="modalApprove" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApprove" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-2" id="approve-info" style="font-size:13px;"></p>
                    <label class="form-label form-label-sm">Catatan Admin (opsional)</label>
                    <textarea name="catatan_admin" class="form-control form-control-sm" rows="2" placeholder="Tulis catatan jika ada..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal modal-blur fade" id="modalReject" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Tolak Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReject" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-2" id="reject-info" style="font-size:13px;"></p>
                    <label class="form-label form-label-sm">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="catatan_admin" class="form-control form-control-sm" rows="2"
                              placeholder="Tuliskan alasan penolakan..." required id="reject-catatan"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('myscript')
<script>
function showApprove(id, nama, tanggal) {
    document.getElementById('approve-info').innerText = 'Setujui pengajuan ' + nama + ' untuk tanggal ' + tanggal + '?';
    document.getElementById('formApprove').action = '/panel/konfirmasikehadiran/' + id + '/approve';
    var modal = new bootstrap.Modal(document.getElementById('modalApprove'));
    modal.show();
}
function showReject(id, nama, tanggal) {
    document.getElementById('reject-info').innerText = 'Tolak pengajuan ' + nama + ' untuk tanggal ' + tanggal + '?';
    document.getElementById('formReject').action = '/panel/konfirmasikehadiran/' + id + '/reject';
    document.getElementById('reject-catatan').value = '';
    var modal = new bootstrap.Modal(document.getElementById('modalReject'));
    modal.show();
}
</script>
@endpush
@endsection
