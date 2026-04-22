@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - 404')

@section('content')
<div class="alert alert-danger" role="alert">
	Halaman tidak dapat ditemukan.
</div>
<a href="{{ url('/') }}">< Kembali</a>
@endsection