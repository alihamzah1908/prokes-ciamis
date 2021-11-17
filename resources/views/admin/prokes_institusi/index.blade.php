@extends('newmaster')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/jquery.ui.timepicker.css?v=0.3.3') }}" type="text/css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<div class="row">
    <div class="col-md-6">
        <button class="btn btn-success btn-sm mb-4 ml-2 add tambah">
            <i class="fa fa-plus" aria-hidden="true"></i>  Tambah Data
        </button>
    </div>
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
    <div class="col-md-6 d-flex justify-content-end">
        <button class="btn btn-primary btn-sm mb-4 ml-2 upload-excel">
            <i class="fa fa-download" aria-hidden="true"></i>  Upload Excel
        </button>
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
                <th>Fasilitas Cuci Tangan</th>
                <th>Sosialisasi Prokes</th>
                <th>Cek Suhu Tubuh</th>
                <th>Petugas Pengawas Prokes</th>
                <th>Desinfeksi Berkala</th>
                <th>Total Prokes</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade add" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form method="POST" action="{{ route('institusi.store') }}" enctype="multipart/form-data" id="add_inst">
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
                    <div class="row">
                        <div class="col-md-6">
                            <label><strong>Alamat</strong></label>
                            <div class="form-group" id="kecamatan">
                                @if(Auth::user()->role == 'Admin')
                                <input type="hidden" name="kecamatan_id" value="{{ Auth::user()->kode_kecamatan }}" />
                                <select name="kecamatan_id" id="kecamatan_id" class="form-control" disabled required>
                                    <option value="">Pilih Kecamatan</option>
                                    @php
                                    $kecamatan = \App\Models\Kecamatan::orderBy('kecamatan','asc')->get()
                                    @endphp
                                    @foreach($kecamatan as $val)
                                        <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == Auth::user()->kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                                    @endforeach
                                </select>
                                @elseif(Auth::user()->role == 'super admin')
                                <select name="kecamatan_id" id="kecamatan_id" class="form-control" required>
                                    <option value="">Pilih Kecamatan</option>
                                    @php
                                    $kecamatan = \App\Models\Kecamatan::orderBy('kecamatan','asc')->get()
                                    @endphp
                                    @foreach($kecamatan as $val)
                                        <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == Auth::user()->kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                                    @endforeach
                                </select>
                                @else 
                                <input type="hidden" name="kecamatan_id" value="{{ $kode_kecamatan }}" />
                                <select name="kecamatan_id" id="kecamatan_id" class="form-control" required disabled>
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
                        <div class="col-md-6">
                            <label><strong>Fasilitas Cuci Tangan</strong></label>
                            <div class="form-group">
                                <select name="cuci_tangan" class="form-control" id="cuci_tangan" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Tidak Ada</option>
                                    <option value="7">Ada, Tidak Bisa Digunakan</option>
                                    <option value="13">Ada, Perlu Perbaikan</option>
                                    <option value="17">Ada, Tidak Terawat dan Dapat Digunakan</option>
                                    <option value="20">Ada, Terawat dan Dapat Digunakan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="desa" style='display:none;'>
                        <div class="col-md-6">
                            <label><strong>Pilih Desa</strong></label>
                            <div class="form-group">
                                <select name="desa_id" id="desa_id" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Sosialisasi Prokes</strong></label>
                            <div class="form-group">
                                <select name="sosialisasi_prokes" class="form-control" id="sosialisasi_prokes" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Tidak Ada</option>
                                    <option value="7">Ada, Sebagian Besar Rusak (Luntur/Robek)</option>
                                    <option value="13">Ada, Sebagian Kecil Rusak (Luntur/Robek)</option>
                                    <option value="17">Ada, Kondisinya Kusam/Tidak Berfungsi Seluruhnya</option>
                                    <option value="20">Ada, Kondisinya Terawat dan Baik</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3" id="desa_edit" style='display:none;'>
                        <div class="col-md-6">
                            <label><strong>Pilih Desa</strong></label>
                            <div class="form-group">
                                <select name="desa_id" id="desa_id" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label><strong>Lokasi Pantau</strong></label>
                            <div class="form-group">
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Petugas Pengawas Prokes</strong></label>
                            <div class="form-group">
                                <select name="pengawas_prokes" class="form-control" id="petugas" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Tidak Ada</option>
                                    <option value="7">Ada, Sebagian Kecil Petugas Sesuai Dengan Jadwal Yang Ditetapkan</option>
                                    <option value="13">Ada, Sebagian Besar Petugas Sesuai Dengan Jadwal Yang Ditetapkan</option>
                                    <option value="17">Ada, Tetapi Tidak Terjadwal</option>
                                    <option value="20">Ada, Dan Seusuai Dengan Jadwal Yang Ditetapkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                             <label><strong>Tanggal Pantau</strong></label>
                            <div class="form-group">
                                <input type="text" name="tanggal_pantau" id="tanggal_pantau" class="form-control" required placeholder="isi mulai tanggal pantau">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Desinfeksi berkala</strong></label>
                            <div class="form-group">
                                <select name="desinfeksi_berkala" class="form-control" id="desinfeksi" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Tidak Ada</option>
                                    <option value="7">Ada, Sebagian Kecil Desinfeksi Sesuai Dengan Jadwal Yang Ditetapkan</option>
                                    <option value="13">Ada, Sebagian Besar Desinfeksi Sesuai Dengan Jadwal Yang Ditetapkan</option>
                                    <option value="17">Ada, Tetapi Tidak Terjadwal</option>
                                    <option value="20">Ada, Dan Seusuai Dengan Jadwal Yang Ditetapkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                             <label><strong>Mulai Jam Pantau</strong></label>
                            <div class="form-group">
                                <input type='text' name="jam_pantau" class="form-control" id="jam_pantau" required placeholder="isi mulai jam pantau">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Cek suhu tubuh</strong></label>
                            <div class="form-group">
                                <select name="suhu_tubuh" class="form-control" id="suhu_tubuh" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Tidak Ada</option>
                                    <option value="7">Ada, Tidak Bisa Digunakan</option>
                                    <option value="13">Ada, Perlu Perbaikan</option>
                                    <option value="17">Ada, Tidak Terawat dan Dapat Digunakan</option>
                                    <option value="20">Ada, Terawat dan Dapat Digunakan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Selesai Jam Pantau</strong></label>
                            <div class="form-group">
                                <input type='text' name="selesai_jam_pantau" class="form-control" id="selesai_jam_pantau" required placeholder="isi selesai jam pantau">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 file_dokumen_new" style="display: none;"> 
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 kolom file_dokumen">
                            <label><strong>Upload Dokumen (Harap isi dengan file jpg/jpeg/png)</strong></label>
                            <div class="form-group">
                                <input type="file" name="image[]" id="image" class="form-control image" required placeholder="isi tanggal pantau">
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-success mr-2 tambah-kolom"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Dokumen</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                </div>
                <input type="hidden" name="id" id="id" value=""/>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" role="dialog" tabindex="-1" id="newParticipantBulk">
    <form method="POST" action="{{ route('import.prokes_institusi') }}" enctype="multipart/form-data" id="import_participan">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">Upload Data Prokes Institusi</h4>
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
<script type="text/javascript" src="{{ asset('assets/js/jquery.ui.timepicker.js?v=0.3.3') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.8/css/fixedHeader.dataTables.min.css"></script>
<script type="text/javascript">
$(document).ready(function(){
    var id = 2;
    $('body').on('click','.tambah-kolom', function(){
        var body = '<div class="form-group" >';
        body += '<input type="file" name="image['+ id +']" class="form-control image_append" required placeholder="isi tanggal pantau" required>';
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
    })
    $('body').on('click','.tambah', function(){
        $('#exampleModal').modal('show')
        var input = $('.file_dokumen').find('#image').length
        if(input == 0){
           $('.file_dokumen').append('<input type="file" name="image[]" id="image" class="form-control image" required placeholder="isi tanggal pantau">')
        }
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
        var url = '{{ route("download.institusi") }}';
        $.ajax({
            url: url,
            method: 'get',
            xhrFields: {
                responseType: 'blob'
            },
            data : {
                'kode': $(this).attr('data-bind')
            },
            success: function(data){
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                a.href = url;
                a.download = 'prokes-institusi.xlsx';
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
    $('body').on('click','.edit', function(){
        var data = JSON.parse($(this).attr('data-bind'));
        var split = data.lokasi_pantau.split(' - ');
        $('#lokasi_pantau').show()
        $("#id").val(data.id)
        $("#kecamatan_id").val(data.kecamatan_id)
        $("#desa_id").val(data.desa_id)
        $("#master_lokasi_pantau").val(split[0])
        $("#lokasi_pantau").val(split[1])
        $("#tanggal_pantau").val(data.tanggal_pantau)
        $("#jam_pantau").val(data.jam_pantau)
        $("#selesai_jam_pantau").val(data.selesai_jam_pantau)
        $("#cuci_tangan").val(data.fasilitas_cuci_tangan)
        $("#sosialisasi_prokes").val(data.sosialisasi_prokes)
        $("#suhu_tubuh").val(data.cek_suhu_tubuh)
        $("#petugas").val(data.petugas_pengawas_prokes)
        $("#desinfeksi").val(data.desinfeksi_berkala)
        $('#exampleModal').modal('show')
        $.ajax({
            url : "{{ route('get.desa') }}",
            dataType: 'json',
            method: 'get',
            data: {
                "code_kecamatan": data.kecamatan_id,
            }
        }).done(function(response){
            $("#desa_id").html("")
            $.each(response, function(index, value){
                var selected = data.desa_id == value.kode_kelurahan ? ' selected' : ''
                elementNew = '<option value=' + value.kode_kelurahan +' ' + selected +'>' + value.nama_kelurahan +'</option>';
                $('#desa_id').append(elementNew)
            })
        });

        $.ajax({
            url : "{{ route('get.image_institusi') }}",
            dataType: 'json',
            method: 'get',
            data: {
                "institusi_id": data.id,
            }
        }).done(function(response){
            if(response.length > 0){
                $('#image').remove()
                $('.image').removeAttr('required')
                $('.file_dokumen_new').show()
                $.each(response, function(index, value){
                    elementNew = '<input type="file" name="image[]" class="form-control image image_edit" placeholder="isi tanggal pantau" value='+ value.image +'><img src="{{ asset("dokumen_institusi") }}/' + value.image + '" width="100" height="100" style="margin-right: 10px;margin-top: 10px;"/>';
                    $('.file_dokumen_new').append(elementNew)
                })
            }else {
                var input = $('.file_dokumen').find('#image').length
                if(input == 0){
                    $('.file_dokumen').append('<input type="file" name="image[]" id="image" class="form-control image" required placeholder="isi tanggal pantau">')
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
                url: '{{ route("institusi.delete") }}',
                data: {
                    "id": id,
                    "_token" : token,
                }
            }).done(function(){
                location.reload()
            })
        }
    })
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
    var url = '{{ route("institusi.datatable") }}';
    var table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: url,
        columns: [
            { data: 'id', visible: false},
            { data: "nama_user"},
            { data: "kelurahan"},
            { data: "kecamatan"},
            { data: "lokasi_pantau"},
            { data: "tanggal_pantau"},
            { data: "mulai_jam_pantau"},
            { data: "selesai_jam_pantau"},
            { data: "fasilitas_cuci_tangan"},
            { data: "sosialisasi_prokes"},
            { data: "cek_suhu_tubuh"},
            { data: "petugas_pengawas_prokes"},
            { data: "desinfeksi_berkala"},
            { data: "total_prokes"},
            { data: "created_at"},
            { data: "aksi" },
        ],
        "order": [[0, 'desc']],
        "pageLength" : 25,
    })

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
            $(".image").val('')
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
        $("input").prop('required', true);
        $('img').remove()
        $(".image_edit").remove()
        $(".image_append").remove()
    })
})
</script>
@endpush