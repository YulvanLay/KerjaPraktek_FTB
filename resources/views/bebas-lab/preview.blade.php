@extends('layouts.app')

@section('title', 'Preview Bebas Laboratorium')

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Preview Bebas Laboratorium</h2>
        </div>
      <div class="col-md-4 text-right"> <a href="{{ url('/bebas-lab/') }}" class="btn btn-secondary"> <i class="fas fa-arrow-left"></i> Kembali </a> </div>
    </div>

    <!-- Info Box -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Informasi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Pelanggan:</strong> {{ $bebas->pelanggan->nama_pelanggan ?? '-' }}</p>
                    <p><strong>Laboratorium:</strong> {{ $bebas->laboratorium->nama_laboratorium ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>No Bebas Lab:</strong> {{ $bebas->id }}</p>
                    <p><strong>Tanggal:</strong> {{ $bebas->created_at->format('d-m-Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Section -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Daftar Syarat Bebas Laboratorium</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">No</th>
                            <th width="50%">Daftar Syarat</th>
                            <th width="20%">Laboran</th>
                            <!-- <th width="20%">Kalab</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $checklists = [
                                1 => 'Bebas Pinjaman peralatan dan kunci loker',
                                2 => 'Sudah membersihkan biakan bakteri (Coldorom, CTR)',
                                3 => 'Sudah membayar bahan kimia dan media yang digunakan',
                                4 => 'Alat gelas dalam keadaan bersih dari label atau coret-coretan',
                                5 => 'Alat yang pecah atau rusak telah diganti',
                            ];
                            $columns = [
                                1 => 'ck_bebas_pinjaman',
                                2 => 'ck_buka_bakteri',
                                3 => 'ck_bayar_bahan',
                                4 => 'ck_alat_bersih',
                                5 => 'ck_alat_ganti',
                            ];
                        @endphp

                        @foreach($checklists as $number => $text)
                            @php $column = $columns[$number]; @endphp
                            <tr>
                                <td>{{ $number }}</td>
                                <td>{{ $text }}</td>
                                <td class="text-center">
                                    @if(auth()->user()->pelanggan)
                                        @if($bebas->$column)
                                            <i class="fas fa-check text-success"></i>
                                        @else
                                            -
                                        @endif
                                    @else
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input laboran-check" 
                                               id="laboran_{{ $number }}"
                                               data-checklist-number="{{ $number }}"
                                               data-bebas-id="{{ $bebas->id }}"
                                               {{ $bebas->$column ? 'checked' : '' }}
                                               {{ auth()->user()->laboran && auth()->user()->laboran->laboratorium == $bebas->laboratorium_id && !$bebas->acc_laboran ? '' : 'disabled' }}>
                                        <label class="custom-control-label" for="laboran_{{ $number }}"></label>
                                    </div>
                                    @endif
                                </td>
                                <!-- <td class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input kalab-check" 
                                               id="kalab_{{ $number }}"
                                               data-checklist-number="{{ $number }}"
                                               data-bebas-id="{{ $bebas->id }}"
                                               {{ $bebas->$column ? 'checked' : '' }}
                                               {{ auth()->user()->kalab ? '' : 'disabled' }}>
                                        <label class="custom-control-label" for="kalab_{{ $number }}"></label>
                                    </div>
                                </td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            @php
                $totalChecked = 0;
                foreach($columns as $col) {
                    if($bebas->$col) $totalChecked++;
                }
            @endphp
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-12">
                    <h6>Status Checklist</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ ($totalChecked / 5) * 100 }}%"
                             aria-valuenow="{{ $totalChecked }}" 
                             aria-valuemin="0" 
                             aria-valuemax="5">
                            {{ $totalChecked }}/5 Syarat Terpenuhi
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4 pt-3 border-top">
                @if((auth()->user()->laboran || auth()->user()->kalab) && $totalChecked === 5)
                    @if(auth()->user()->laboran && !$bebas->acc_laboran && auth()->user()->laboran->laboratorium == $bebas->laboratorium_id)
                        <div class="col-md-6">
                            <button class="btn btn-warning btn-block" id="btn-acc-laboran" data-bebas-id="{{ $bebas->id }}">
                                <i class="fas fa-check"></i> Acc Laboran
                            </button>
                        </div>
                    @elseif($bebas->acc_laboran && auth()->user()->laboran && auth()->user()->laboran->laboratorium == $bebas->laboratorium_id)
                        <div class="col-md-6">
                            <button class="btn btn-danger btn-block" id="btn-batal-acc-laboran" data-bebas-id="{{ $bebas->id }}">
                                <i class="fas fa-times"></i>
                               Batal Acc Laboran
                            </button>
                        </div>
                    @endif

                  
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        console.log('Preview page loaded');

        // Handle Laboran Checkbox Change
        $('.laboran-check').on('change', function() {
            const checklistNumber = $(this).data('checklist-number');
            const bebasId = $(this).data('bebas-id');
            const isChecked = $(this).is(':checked');

            console.log('Laboran check changed:', { checklistNumber, bebasId, isChecked });
            updateChecklist(bebasId, checklistNumber, isChecked, 'laboran', $(this));
        });

        // // Handle Kalab Checkbox Change
        // $('.kalab-check').on('change', function() {
        //     const checklistNumber = $(this).data('checklist-number');
        //     const bebasId = $(this).data('bebas-id');
        //     const isChecked = $(this).is(':checked');

        //     console.log('Kalab check changed:', { checklistNumber, bebasId, isChecked });
        //     updateChecklist(bebasId, checklistNumber, isChecked, 'kalab', $(this));
        // });

        // Update Checklist via AJAX
        function updateChecklist(bebasId, checklistNumber, isChecked, role, element) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            console.log('Sending AJAX request:', {
                url: '/bebas-lab/update-checklist',
                token: csrfToken,
                data: {
                    bebas_id: bebasId,
                    checklist_number: checklistNumber,
                    is_checked: isChecked,
                    role: role
                }
            });

            $.ajax({
                type: 'POST',
                url: '/bebas-lab/update-checklist',
                data: {
                    _token: csrfToken,
                    bebas_id: bebasId,
                    checklist_number: checklistNumber,
                    is_checked: isChecked,
                    role: role
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error response:', { xhr, status, error });
                    console.error('Response text:', xhr.responseText);
                    element.prop('checked', !isChecked);
                    alert('Gagal mengupdate checklist: ' + error);
                }
            });
        }

        // Handle Acc Laboran Button
        $('#btn-acc-laboran').on('click', function() {
            const bebasId = $(this).data('bebas-id');         
            
            if (confirm('Apakah Anda yakin akan memberikan persetujuan?')) {
                var kodeLaboran = "{{ auth()->user()->laboran->kode_laboran ?? '' }}";
                $.ajax({
                    type: 'POST',
                    url: '/bebas-lab/acc-laboran',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bebas_id: bebasId,
                        kode_laboran: kodeLaboran
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Persetujuan berhasil disimpan!');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            }
        });

        $('#btn-batal-acc-laboran').on('click', function() {
            const bebasId = $(this).data('bebas-id');
            
            if (confirm('Apakah Anda yakin akan membatalkan persetujuan?')) {
                $.ajax({
                    type: 'POST',
                    url: '/bebas-lab/batal-acc-laboran',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bebas_id: bebasId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Berhasil batal acc!');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            }
        });

        

        // Handle Acc Kalab Button
        $('#btn-acc-kalab').on('click', function() {
            const bebasId = $(this).data('bebas-id');
            
            if (confirm('Apakah Anda yakin akan memberikan persetujuan?')) {
                $.ajax({
                    type: 'POST',
                    url: '/bebas-lab/acc-kalab',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bebas_id: bebasId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Persetujuan berhasil disimpan!');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            }
        });
    });
</script>

@endsection

