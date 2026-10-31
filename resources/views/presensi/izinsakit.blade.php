@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Monitoring Pengajuan</h2>
                <div class="text-muted mt-1" style="font-size:12px;">Izin tidak hadir &middot; Sakit &middot; Cuti &middot; Konfirmasi Kehadiran</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">

        @if(Session::get('success'))
            <div class="alert alert-success alert-dismissible mb-3">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(Session::get('warning'))
            <div class="alert alert-warning alert-dismissible mb-3">
                {{ Session::get('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABS --}}
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="pengajuanTab">
                    <li class="nav-item">
                        <a class="nav-link {{ request('tab','izin') === 'izin' ? 'active' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['tab' => 'izin']) }}">
                            Izin / Sakit / Cuti
                            @php $pendingIzin = $izinsakit->where('status_approved', 0)->count(); @endphp
                            @if($pendingIzin > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $pendingIzin }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('tab') === 'konfirmasi' ? 'active' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['tab' => 'konfirmasi']) }}">
                            Konfirmasi Kehadiran
                            @php $pendingKonfirmasi = $konfirmasi->where('status', 'pending')->count(); @endphp
                            @if($pendingKonfirmasi > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $pendingKonfirmasi }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- TAB: IZIN / SAKIT / CUTI --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            @if(request('tab', 'izin') === 'izin')
            <div class="card-body pb-0">
                <form action="/presensi/izinsakit" method="GET" autocomplete="off">
                    <input type="hidden" name="tab" value="izin">
                    <div class="row g-2 mb-2">
                        <div class="col-md-2">
                            <input type="text" value="{{ Request('dari') }}" id="dari"
                                   class="form-control form-control-sm" placeholder="Dari tanggal" name="dari">
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{ Request('sampai') }}" id="sampai"
                                   class="form-control form-control-sm" placeholder="Sampai tanggal" name="sampai">
                        </div>
                        <div class="col-md-2">
                            <input type="text" value="{{ Request('nama_lengkap') }}"
                                   class="form-control form-control-sm" placeholder="Nama pegawai" name="nama_lengkap">
                        </div>
                        @if(Auth::guard('user')->user()?->hasRole('administrator'))
                        <div class="col-md-2">
                            <select name="kode_cabang" class="form-select form-select-sm">
                                <option value="">Semua Lokasi</option>
                                @foreach ($cabang as $d)
                                    <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ strtoupper($d->nama_cabang) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="kode_dept" class="form-select form-select-sm">
                                <option value="">Semua Jabatan</option>
                                @foreach ($departemen as $d)
                                    <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <select name="status_approved" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="0" {{ Request('status_approved') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ Request('status_approved') == 1 ? 'selected' : '' }}>Disetujui</option>
                                <option value="2" {{ Request('status_approved') == 2 ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                            <a href="/presensi/izinsakit?tab=izin" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-bordered table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th width="35">No</th>
                            <th>Nama Pegawai</th>
                            <th width="130">Tanggal</th>
                            <th width="60">Jenis</th>
                            <th>Keterangan</th>
                            <th width="50">Bukti</th>
                            <th width="80">Status</th>
                            @if(Auth::guard('user')->user()?->hasRole('administrator'))
                                <th width="100">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($izinsakit as $d)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $d->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $d->nik }} &middot; {{ $d->jabatan }}</small>
                                </td>
                                <td style="font-size:12px;">
                                    {{ date('d/m/Y', strtotime($d->tgl_izin_dari)) }}
                                    @if($d->tgl_izin_dari != $d->tgl_izin_sampai)
                                        – {{ date('d/m/Y', strtotime($d->tgl_izin_sampai)) }}
                                    @endif
                                </td>
                                <td>
                                    @if($d->status == 'i')
                                        <span class="badge bg-blue-lt">Izin</span>
                                    @elseif($d->status == 's')
                                        <span class="badge bg-red-lt">Sakit</span>
                                    @elseif($d->status == 'c')
                                        <span class="badge bg-yellow-lt">Cuti</span>
                                    @endif
                                </td>
                                <td style="font-size:12px;">
                                    {{ $d->keterangan }}
                                    @if($d->status == 'c' && $d->nama_cuti)
                                        <br><span class="badge bg-yellow-lt">{{ $d->nama_cuti }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!empty($d->doc_sid))
                                        <a href="{{ url(Storage::url('uploads/sid/' . $d->doc_sid)) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:10px; padding:2px 6px;">Lihat</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($d->status_approved == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($d->status_approved == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                @if(Auth::guard('user')->user()?->hasRole('administrator'))
                                    <td>
                                        @if($d->status_approved == 0)
                                            <a href="#" class="btn btn-sm btn-success approve"
                                               kode_izin="{{ $d->kode_izin }}">Setujui</a>
                                        @else
                                            <a href="/presensi/{{ $d->kode_izin }}/batalkanizinsakit"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Batalkan persetujuan ini?')">Batalkan</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        @if($izinsakit->isEmpty())
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $izinsakit->links('vendor.pagination.bootstrap-5') }}</div>
            @endif

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- TAB: KONFIRMASI KEHADIRAN --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            @if(request('tab') === 'konfirmasi')
            <div class="card-body pb-0">
                <form action="/presensi/izinsakit" method="GET" autocomplete="off">
                    <input type="hidden" name="tab" value="konfirmasi">
                    <div class="row g-2 mb-2">
                        <div class="col-md-2">
                            <input type="date" value="{{ Request('dari') }}" name="dari"
                                   class="form-control form-control-sm" placeholder="Dari tanggal">
                        </div>
                        <div class="col-md-2">
                            <input type="date" value="{{ Request('sampai') }}" name="sampai"
                                   class="form-control form-control-sm" placeholder="Sampai tanggal">
                        </div>
                        <div class="col-md-3">
                            <input type="text" value="{{ Request('nama') }}" name="nama"
                                   class="form-control form-control-sm" placeholder="Nama pegawai">
                        </div>
                        <div class="col-md-2">
                            <select name="status_konfirmasi" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="pending"  {{ Request('status_konfirmasi')=='pending'  ? 'selected':'' }}>Pending</option>
                                <option value="approved" {{ Request('status_konfirmasi')=='approved' ? 'selected':'' }}>Disetujui</option>
                                <option value="rejected" {{ Request('status_konfirmasi')=='rejected' ? 'selected':'' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                            <a href="/presensi/izinsakit?tab=konfirmasi" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-bordered table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th width="35">No</th>
                            <th>Nama Pegawai</th>
                            <th width="90">Tanggal</th>
                            <th width="70">Hari</th>
                            <th width="90">Jam</th>
                            <th>Alasan</th>
                            <th width="55">Bukti</th>
                            <th width="80">Status</th>
                            <th width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $namaHari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"]; $no = 1; @endphp
                        @forelse($konfirmasi as $d)
                            <tr>
                                <td class="text-muted">{{ $no++ }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $d->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $d->nik }} &middot; {{ $d->jabatan }}</small>
                                </td>
                                <td>{{ date('d/m/Y', strtotime($d->tanggal)) }}</td>
                                <td>{{ $namaHari[date('w', strtotime($d->tanggal))] }}</td>
                                <td class="text-center" style="font-size:11px;">
                                    {{ date('H:i', strtotime($d->jam_masuk)) }}<br>
                                    <span class="text-muted">{{ date('H:i', strtotime($d->jam_keluar)) }}</span>
                                </td>
                                <td style="font-size:12px; max-width:200px; white-space:pre-wrap;">{{ $d->alasan }}
                                    @if($d->catatan_admin)
                                        <br><em class="text-muted" style="font-size:10px;">Catatan: {{ $d->catatan_admin }}</em>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($d->bukti)
                                        <a href="{{ Storage::url('uploads/konfirmasi_kehadiran/' . $d->bukti) }}"
                                           target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:10px; padding:2px 6px;">Lihat</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
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
                                        <small class="text-muted">Oleh: {{ $d->approved_by ?? '-' }}</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data konfirmasi kehadiran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $konfirmasi->links('vendor.pagination.bootstrap-5') }}</div>
            @endif

        </div>{{-- end card --}}
    </div>
</div>

{{-- Modal Approve Izin/Sakit/Cuti --}}
<div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status Penerimaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/presensi/approveizinsakit" method="POST">
                    @csrf
                    <input type="hidden" id="kode_izin_form" name="kode_izin_form">
                    <div class="form-group mb-3">
                        <select name="status_approved" class="form-select">
                            <option value="1">Disetujui</option>
                            <option value="2">Ditolak</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Approve Konfirmasi --}}
<div class="modal modal-blur fade" id="modalApprove" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Konfirmasi Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApprove" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="approve-info" style="font-size:13px;" class="mb-2"></p>
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

{{-- Modal Reject Konfirmasi --}}
<div class="modal modal-blur fade" id="modalReject" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Tolak Konfirmasi Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReject" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="reject-info" style="font-size:13px;" class="mb-2"></p>
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

@endsection

@push('myscript')
<script>
$(function () {
    // Approve Izin/Sakit/Cuti
    $(".approve").click(function (e) {
        e.preventDefault();
        var kode_izin = $(this).attr("kode_izin");
        $("#kode_izin_form").val(kode_izin);
        $("#modal-izinsakit").modal("show");
    });

    // Datepicker Izin tab
    $("#dari, #sampai").datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
});

function showApprove(id, nama, tanggal) {
    document.getElementById('approve-info').innerText = 'Setujui pengajuan ' + nama + ' tanggal ' + tanggal + '?';
    document.getElementById('formApprove').action = '/panel/konfirmasikehadiran/' + id + '/approve';
    new bootstrap.Modal(document.getElementById('modalApprove')).show();
}
function showReject(id, nama, tanggal) {
    document.getElementById('reject-info').innerText = 'Tolak pengajuan ' + nama + ' tanggal ' + tanggal + '?';
    document.getElementById('formReject').action = '/panel/konfirmasikehadiran/' + id + '/reject';
    document.getElementById('reject-catatan').value = '';
    new bootstrap.Modal(document.getElementById('modalReject')).show();
}
</script>
@endpush
