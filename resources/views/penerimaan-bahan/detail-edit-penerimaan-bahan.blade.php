{{-- resources/views/penerimaan-bahan/detail-edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Detail Penerimaan Bahan</h5>
                </div>
                <div class="card-body">

                    {{-- Alert status --}}
                    @if (session('status'))
                        <div class="alert alert-{{ session('kode') == 1 ? 'success' : 'danger' }} alert-dismissible fade show">
                            {!! session('status') !!}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <form action="{{ route('DetailPenerimaanBahan.update', $detail->id) }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">No PO</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $detail->no_PO }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Bahan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $detail->bahan->nama_bahan ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Jumlah / Stok <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" name="jumlah" class="form-control"
                                    value="{{ $detail->jumlah }}" min="1" required>
                                <small class="form-text text-muted">
                                    Stok bahan akan otomatis diperbarui sesuai perubahan jumlah ini.
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Harga Bahan</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="harga" class="form-control"
                                        value="{{ $detail->bahan->harga_bahan ?? '' }}" min="0">
                                </div>
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah harga.</small>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-9 offset-sm-3 d-flex">
                                <button type="submit" class="btn btn-warning mr-2">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="/terima-bahan-detail/{{ $detail->no_PO }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection