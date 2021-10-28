@extends('newmaster')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/jquery.ui.timepicker.css?v=0.3.3') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<div class="row">
    <div class="col-md-6">
        <button class="btn btn-success btn-sm mb-4 ml-2 add tambah">
            <i class="fa fa-plus" aria-hidden="true"></i>  Tambah Data
        </button>
    </div>
    <div class="col-md-6 d-flex justify-content-end">
        <button class="btn btn-primary btn-sm mb-4 ml-2 upload-excel">
            <i class="fa fa-download" aria-hidden="true"></i>  Upload Excel
        </button>
        @php 
        if(Auth::user()->role == 'Staff'){
            $kode = Auth::user()->id;
            $val1 = \App\Models\User::select('kode_kecamatan')->where('id', Auth::user()->parent_admin)->first();
            $kode_kecamatan = $val1->kode_kecamatan;
        }elseif(Auth::user()->role == 'Admin'){
            $kode = Auth::user()->kode_kecamatan;
            $kode_kecamatan = Auth::user()->kode_kecamatan;
        }else{
            $kode = $kode = Auth::user()->id;
            $kode_kecamatan = '';
        }
        @endphp
        @if(Auth::user()->kode_kecamatan == '')
            <input type="hidden" id="kd_kecamatan" value="{{ $kode_kecamatan }}" />
        @else 
            <input type="hidden" id="kd_kecamatan" value="{{ $kode_kecamatan }}" />
        @endif
        <button class="btn btn-success btn-sm mb-4 ml-2 download-template" data-bind="{{ $kode }}">
            <div id="text-button-voucher" class="text-button">
                <i class="fa fa-download" aria-hidden="true"></i> Download Template
            </div>
            <div id="loading-wrap-voucher" class="text-center loading-wrap" style="display: none;">
                <div class="spinner-border text-light" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </button>
    </div>
</div>
<div class="table-responsive">          
    <table class="table" id="example">
        <thead>
            <tr>
                <th></th>
                <th>Nama User</th>
                <th>Desa/Kelurahan</th>
                <th>Kecamatan</th>
                <th>Lokasi Pantau</th>
                <th>Tanggal Pantau</th>
                <th>Mulai Jam Pantau</th>
                <th>Selesai Jam Pantau</th>
                <th>Jumlah Pakai Masker</th>
                <th>Jumlah Tidak Pakai</th>
                <th>Total Masker</th>
                <th>% Kepatuhan Masker</th>
                <!-- <th>Peta Zonasi Masker</th> -->
                <th>Jumlah Jaga Jarak</th>
                <th>Jumlah Tidak Jaga Jarak</th>
                <th>Total Jaga Jarak</th>
                <th>% Kepatuhan Jaga Jarak</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade add" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form method="POST" action="{{ route('prokes.store') }}" enctype="multipart/form-data" id="import_participan">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <strong>Tambah Data Prokes</strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Mohon Input Data Prokes</p>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Alamat</strong></label>
                        </div>
                        <div class="col-md-6" id="kecamatan">
                            @if(Auth::user()->role == 'Admin')
                            <input type="hidden" name="kecamatan_id" value="{{ Auth::user()->kode_kecamatan }}" />
                            <select name="kecamatan_id" id="kecamatan_id" class="form-control" disabled required>
                                <option value="">Pilih Kecamatan</option>
                                @php
                                $kecamatan = \App\Models\Kecamatan::all()
                                @endphp
                                @foreach($kecamatan as $val)
                                    <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == Auth::user()->kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                                @endforeach
                            </select>
                            @elseif(Auth::user()->role == 'super admin')
                            <select name="kecamatan_id" id="kecamatan_id" class="form-control" required>
                                <option value="">Pilih Kecamatan</option>
                                @php
                                $kecamatan = \App\Models\Kecamatan::all()
                                @endphp
                                @foreach($kecamatan as $val)
                                    <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == Auth::user()->kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                                @endforeach
                            </select>
                            @else
                            <input type="hidden" name="kecamatan_id" value="{{ $kode_kecamatan }}" />
                            <select name="kecamatan_id" id="kecamatan_id" class="form-control" disabled required>
                                <option value="">Pilih Kecamatan</option>
                                @php
                                $kecamatan = \App\Models\Kecamatan::all()
                                @endphp
                                @foreach($kecamatan as $val)
                                    <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == $kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3" id="desa" style='display:none;'>
                        <div class="col-md-2">
                            <label><strong>Pilih Desa</strong></label>
                        </div>
                        <div class="col-md-6" id="kecamatan">
                            <select name="desa_id" id="desa_id" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3" id="desa_edit" style='display:none;'>
                        <div class="col-md-2">
                            <label><strong>Pilih Desa</strong></label>
                        </div>
                        <div class="col-md-6" id="kecamatan">
                            <select name="desa_id" id="desa_id" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Lokasi Pantau</strong></label>
                        </div>
                        <div class="col-md-6">
                            <select name="master_lokasi_pantau" class="form-control" id="master_lokasi_pantau" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="Pusat Perbelanjaan">Pusat Perbelanjaan</option>
                                <option value="Obyek Wisata">Obyek Wisata</option>
                                <option value="Area Publik">Area Publik</option>
                                <option value="Hotel">Hotel</option>
                                <option value="Restoran">Restoran</option>
                                <option value="Tempat Ibadah">Tempat Ibadah</option>
                                <option value="Kegiatan Seni Budaya">Kegiatan Seni Budaya</option>
                                <option value="Transportasi Umum">Transportasi Umum</option>
                            </select>
                            <textarea name="lokasi_pantau" class="form-control mt-2" id="lokasi_pantau" style="display:none" placeholder="mohon isi kelengkapan lokasi pantau" ></textarea>
                            <!-- <input type="text" name="lokasi_pantau" class="form-control mt-2" id="lokasi_pantau" style="display:none" placeholder="mohon isi kelengkapan lokasi pantau" /> -->
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Tanggal Pantau</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="tanggal_pantau" id="tanggal_pantau" class="form-control" placeholder="isi tanggal pantau" required> 
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Mulai Jam Pantau</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type='text' name="jam_pantau" class="form-control" id="jam_pantau" required placeholder="isi mulai jam pantau">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Selesai Jam Pantau</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type='text' name="selesai_jam_pantau" class="form-control" id="selesai_jam_pantau" required placeholder="isi selesai jam pantau">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Jumlah Pakai Masker</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="jumlah_pakai_masker" id="jumlah_pakai_masker" class="form-control" required placeholder="mohon isi dengan angka">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Jumlah Tidak Pakai</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="jumlah_tidak_pakai" id="jumlah_tidak_pakai" class="form-control" required placeholder="mohon isi dengan angka">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Jumlah Jaga Jarak</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="jumlah_jaga_jarak" id="jumlah_jaga_jarak" class="form-control" required placeholder="mohon isi dengan angka">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Jumlah Tidak Jaga Jarak</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="jumlah_tidak_jaga_jarak" id="jumlah_tidak_jaga_jarak" class="form-control" required placeholder="mohon isi dengan angka">
                        </div>
                    </div>
                    <div class="row kolom mb-3">
                        <div class="col-md-2">
                            <label><strong>Upload Dokumen (Harap isi dengan file jpg/jpeg/png)</strong></label>
                        </div>
                        <div class="col-md-6 file_dokumen">
                            <input type="file" name="image[]" id="image" class="form-control image" required placeholder="isi tanggal pantau">
                        </div>
                        <div class="col-md-6 file_dokumen_new" style="display:none;">
                            
                        </div>
                    </div>
                    
                    <div class="row mb-3 d-flex justify-content-end">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-success mr-2 tambah-kolom"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Dokumen</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
                <input type="hidden" name="id" id="id" value=""/>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" role="dialog" tabindex="-1" id="newParticipantBulk">
    <form method="POST" action="{{ route('import.prokes') }}" enctype="multipart/form-data" id="import_participan">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">Upload Data Prokes Individu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p>Please provide upload file</p>
                    <div class="form-group">
                        <input type="file" name="file" id="import-participant-input" class="form-control" required="true">
                    </div>
                    <div id="error-import-participant" class="mb-3 invalid-feedback" style="display: none; font-size: 14px;">Please upload file to import</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-dismiss="modal">Close</button>
                    <button type="submit" id="upload-file" class="btn btn-primary">
                        <div id="text-button-file" class="text-button button-title">Upload</div>
                        <div id="loading-wrap-file" class="text-center loading-wrap" style="display: none;">
                            <div class="spinner-border text-light" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </button>
                </div>
                <input type="hidden" name="code_kecamatan" id="code_kecamatan" value="">
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script src="https://cdn.datatables.net/fixedheader/3.1.8/css/fixedHeader.dataTables.min.css"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.ui.timepicker.js?v=0.3.3') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
    var id = 2;
    $('body').on('click','.tambah-kolom', function(){
        var body = '<div class="row mb-3" >';
        body += '<div class="col-md-10" style="margin-left: 150px;">';
        body += '<input type="file" name="image['+ id +']" class="form-control image_append" placeholder="isi tanggal pantau" required>';
        body += '</div>';
        body += '</div>';
        $('.kolom').append(body)
        id++;
    })
    var kodekec = $('#kd_kecamatan').val();
    $("#desa").show()
    $.ajax({
        url : "{{ route('get.desa') }}",
        dataType: 'json',
        method: 'get',
        data: {
            "code_kecamatan": kodekec
        }
    }).done(function(response){
        $("#desa_id").html("")
        $.each(response, function(index, value){
            elementNew = '<option value=' + value.kode_kelurahan +'>' + value.nama_kelurahan +'</option>';
            $('#desa_id').append(elementNew)
        })
    });
    $('body').on('click','.tambah', function(){
        $('#exampleModal').modal('show')
        var input = $('.image').closest('.image').length
        if(input == 0){
            $('.file_dokumen').append('<input type="file" name="image[]" class="form-control image" required placeholder="isi tanggal pantau">')
        }
    })
    $('body').on('click','.edit', function(){
        var data = JSON.parse($(this).attr('data-bind'));
        var split = data.kode_lokasi_pantau.split(' - ');
        $("#id").val(data.id)
        $('#lokasi_pantau').show()
        $("#kecamatan_id").val(data.kode_kecamatan)
        $("#desa_id").val(data.kode_desa)
        $("#master_lokasi_pantau").val(split[0])
        $("#lokasi_pantau").val(split[1])
        $("#tanggal_pantau").val(data.tanggal_pantau)
        $("#jam_pantau").val(data.jam_pantau)
        $("#selesai_jam_pantau").val(data.selesai_jam_pantau)
        $("#jumlah_pakai_masker").val(data.pakai_masker)
        $("#jumlah_tidak_pakai").val(data.tidak_pakai_masker)
        $("#jumlah_jaga_jarak").val(data.jaga_jarak)
        $("#jumlah_tidak_jaga_jarak").val(data.tidak_jaga_jarak)
        $('#exampleModal').modal('show')
        $.ajax({
            url : "{{ route('get.desa') }}",
            dataType: 'json',
            method: 'get',
            data: {
                "code_kecamatan": data.kode_kecamatan,
            }
        }).done(function(response){
            $("#desa_id").html("")
            $.each(response, function(index, value){
                var selected = data.kode_desa == value.kode_kelurahan ? ' selected' : ''
                elementNew = '<option value=' + value.kode_kelurahan +' ' + selected +'>' + value.nama_kelurahan +'</option>';
                $('#desa_id').append(elementNew)
            })
        });
        $.ajax({
            url : "{{ route('get.image_individu') }}",
            dataType: 'json',
            method: 'get',
            data: {
                "individu_id": data.id,
            }
        }).done(function(response){
            if(response.length > 0){
                $('.image').remove()
                $('.image').removeAttr('required')
                $('.file_dokumen_new').show()
                $.each(response, function(index, value){
                    elementNew = '<input type="file" id="image_edit" name="image[]" class="form-control image image_edit" placeholder="isi tanggal pantau" value='+ value.image +'><img src="{{ asset("dokumen_individu") }}/' + value.image + '" width="100" height="100" style="margin-right: 10px;margin-top: 10px;"/>';
                    $('.file_dokumen_new').append(elementNew)
                })
            } else {
                var input = $('.image').closest('.image').length
                if(input == 0){
                    $('.file_dokumen').append('<input type="file" name="image[]" class="form-control image" required placeholder="isi tanggal pantau">')
                }
                $('.image').prop('required', true)
                $('.file_dokumen_new').hide()
            }
        });
    })
    $('body').on('click', '.delete', function(){
        if(confirm('Apakah anda yakin menghapus data ini ?')){
            var id = $(this).attr('data-bind');
            var token = $("meta[name='csrf-token']").attr("content");
            $.ajax({
                dataType: 'json',
                method: 'DELETE',
                url: '{{ route("individu.delete") }}',
                data: {
                    "id": id,
                    "_token" : token,
                }
            }).done(function(response){
                console.log(response)
                location.reload()
            })
        }
    })
    $('body').on('change','#kecamatan_id', function(){
        var code_kecamatan = $(this).val()
        $("#desa").show()
        $.ajax({
            url : "{{ route('get.desa') }}",
            dataType: 'json',
            method: 'get',
            data: {
                "code_kecamatan": code_kecamatan
            }
        }).done(function(response){
            $("#desa_id").html("")
            $.each(response, function(index, value){
                elementNew = '<option value=' + value.kode_kelurahan +'>' + value.nama_kelurahan +'</option>';
                $('#desa_id').append(elementNew)
            })
        })
    })
    $('body').on('click','.download-template', function(){
        var url = '{{ route("download.template") }}';
        $.ajax({
            url: url,
            method: 'get',
            xhrFields: {
                responseType: 'blob'
            },
            data: {
                'kode' : $(this).attr('data-bind')
            },
            success: function(data){
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                a.href = url;
                a.download = 'prokes-individu.xlsx';
                document.body.append(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                $(this).sess
            }
        })
        $("#loading-wrap-voucher").css("display","block")
        $("#text-button-voucher").css("display","none")
        setTimeout(function() {
            $(this).prop('disabled', false)
            $("#loading-wrap-voucher").css("display","none")
            $("#text-button-voucher").css("display","block")
        }, 1000);
    })
    $('body').on('click', '.upload-excel', function(){
        var code = $(this).attr('data-code')
        $('#code_kecamatan').val(code)
        $('#newParticipantBulk').modal('show')
    })

    $('body').on('change', '#master_lokasi_pantau', function(){
        $('#lokasi_pantau').show();
    })
    $("#tanggal_pantau").datepicker();
    $('#jam_pantau').timepicker({
        showPeriodLabels: false
    });
    $('#selesai_jam_pantau').timepicker({
        showPeriodLabels: false
    });
    $('#example thead tr').clone(true).appendTo( '#example thead' );
    $('#example thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    });
    var url = '{{ route("individu.datatable") }}';
    var table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: url,
        columns: [
            { data: 'id', visible:false},
            { data: "nama_user"},
            { data: "kelurahan"},
            { data: "kecamatan"},
            { data: "kode_lokasi_pantau"},
            { data: "tanggal_pantau"},
            { data: "mulai_jam_pantau"},
            { data: "selesai_jam_pantau"},
            { data: "pakai_masker"},
            { data: "tidak_pakai_masker"},
            { data: "total_masker"},
            { data: "kepatuhan_prokes"},
            // { data: "level_masker"},
            { data: "jaga_jarak"},
            { data: "tidak_jaga_jarak"},
            { data: "total_jaga_jarak"},
            { data: "kepatuhan_jaga_jarak"},
            { data: "created_at"},
            // { data: "level_jaga_jarak"},
            { data: "aksi" },
        ],
        "order": [[0, 'desc']],
    });

    $('body').on('change', '#image', function(){
        var fileInput = $("#image").val()
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        // alert(allowedExtensions.exec(fileInput))
        if (!allowedExtensions.exec(fileInput)) {
            $("#image").val('')
            alert('Harap Input dokumen dengan file jpg, jpeg, png');
            return false;
        }
    })

    $('body').on('change', '.image_append', function(){
        var fileInput = $(".image_append").val()
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        // alert(allowedExtensions.exec(fileInput))
        if (!allowedExtensions.exec(fileInput)) {
            $(".image_append").val('')
            alert('Harap Input dokumen dengan file jpg, jpeg, png');
            return false;
        }
    })

    $('body').on('click','.modal-close', function(){
        $("#id").val(' ')
        $('#lokasi_pantau').show()
        $("#master_lokasi_pantau").val('')
        $("#lokasi_pantau").val('')
        $("#tanggal_pantau").val('')
        $("#jam_pantau").val('')
        $("#selesai_jam_pantau").val('')
        $("#jumlah_pakai_masker").val('')
        $("#jumlah_tidak_pakai").val('')
        $("#jumlah_jaga_jarak").val('')
        $("#jumlah_tidak_jaga_jarak").val('')
        $("input").prop('required',true);
        $('img').remove()
        $(".image_edit").remove()
        $(".image_append").remove()
    })

    $('body').on('click','.close', function(){
        $("#id").val(' ')
        $('#lokasi_pantau').show()
        $("#master_lokasi_pantau").val('')
        $("#lokasi_pantau").val('')
        $("#tanggal_pantau").val('')
        $("#jam_pantau").val('')
        $("#selesai_jam_pantau").val('')
        $("#jumlah_pakai_masker").val('')
        $("#jumlah_tidak_pakai").val('')
        $("#jumlah_jaga_jarak").val('')
        $("#jumlah_tidak_jaga_jarak").val('')
        $("input").prop('required',true);
        $('img').remove()
        $(".image_edit").remove()
        $(".image_append").remove()
    })
})
</script>
@endpush