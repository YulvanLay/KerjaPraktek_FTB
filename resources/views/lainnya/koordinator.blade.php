@extends('layouts.app')

@section('title', 'Daftar Koordinator')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h2>Koordinator</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit();">Ubah Koordinator</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Koordinator</th>
        </tr>
    </thead>
    <tbody>
        @foreach($koors as $koor)
        <tr>
            <td>{{ $koor->pejabat->kode_pejabat }}</td>
            <td>{{ $koor->pejabat->nama_pejabat }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Koordinator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="pejabat">Pejabat</label>
                        <select id="pejabat" class="select2 form-control" name="pejabat">
                            @foreach($pejabats as $pejabat)
                            <option value="{{ $pejabat->kode_pejabat }}">{{ $pejabat->nama_pejabat }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formEdit').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
    });

    function showModalEdit(){
        var url = '/lainnya/koordinator/1';
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                // console.log(data);return;
                var urlUpdate = '{{ route("lainnya.Koordinator.update", ":id") }}';
                urlUpdate = urlUpdate.replace(':id', data.id);
                $("#formEdit").attr('action', urlUpdate);

                $('#pejabat').val(data.kode_pejabat);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }
</script>
@endsection