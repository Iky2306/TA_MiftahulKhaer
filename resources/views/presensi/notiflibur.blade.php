@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">SiGuru</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection
@section('content')
<div class="row" style="margin-top: 60px">
    <div class="col">
        <div class="alert alert-warning">
            <p style="margin: 0; font-size: 16px; font-weight: bold; text-align: center;">
                Hari Ini Libur!
            </p>
            <p style="margin: 10px 0 0 0; font-size: 14px; text-align: center;">
                Maaf, Anda tidak dapat melakukan absensi karena hari ini adalah hari libur:<br>
                <strong style="color: #d63031;">{{ $keterangan }}</strong>
            </p>
        </div>
    </div>
</div>
@endsection
