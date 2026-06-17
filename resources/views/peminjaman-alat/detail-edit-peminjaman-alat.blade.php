{{-- resources/views/peminjaman-alat/detail-edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Detail Penerimaan Alat Lab</h5>
                </div>
                <div class="card-body">

                    {{-- Alert status --}}
                    @if (session('status'))
                        <div class="alert alert-{{ session('kode') == 1 ? 'success' : 'danger' }} alert-dismissible fade show">
                            {!! session('status') !!}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <form action="{{ route('DetailPeminjamanAlat.update', $detail->id) }}" method="POST">
                        @csrf
                        @method('POST')

                        <input type="hidden" name="no_transaksi" value="{{ $detail->no_transaksi }}">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">No Transaksi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $detail->no_transaksi }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Alat</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $detail->alat->nama_alat ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Jumlah Usulan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $detail->jumlah_usulan }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Jumlah ACC <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" name="jumlah_acc" class="form-control"
                                    value="{{ $detail->jumlah }}" min="1"
                                    max="{{ $detail->jumlah_usulan }}" required>
                                <small class="form-text text-muted">
                                    Jumlah ACC tidak boleh melebihi jumlah usulan 
                                    ({{ $detail->jumlah_usulan }}).
                                    Stok alat tersedia: 
                                    <strong>{{ $detail->alat->stok ?? 0 }}</strong>
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-9 offset-sm-3 d-flex">
                                <button type="submit" class="btn btn-warning mr-2">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="/pinjam-alat-detail/{{ $detail->no_transaksi }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>

                    </form>

                    {{-- Form Hapus terpisah --}}
                    <hr>
                    <div class="row">
                        <div class="col-sm-9 offset-sm-3">
                            <form action="/pinjam-alat-detail/hapus-detail/{{ $detail->id }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus alat {{ $detail->alat->nama_alat ?? '' }} dari transaksi ini? Stok akan dikembalikan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Detail Ini
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection