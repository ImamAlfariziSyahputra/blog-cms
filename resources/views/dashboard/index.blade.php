@extends('layouts.dashboard')

@section('title')
    Halaman Utama Dashboard
@endsection

@section('breadcrumbs')
    breadcrumbs
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Selamat Datang {{ Auth::user()->name }}</h2>
        </div>
    </div>
@endsection