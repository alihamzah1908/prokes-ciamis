@extends('newmaster')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/jquery.ui.timepicker.css?v=0.3.3') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
<div class="card-header card-header-primary">
    <h4 class="card-title">Upload Dokumen Prokes Individu</h4>
    <!-- <p class="card-category">Complete your profile</p> -->
</div>
<ol class="breadcrumb mb-4 mt-4">
    <li class="breadcrumb-item"><a href="{{ route('prokes.index') }}">Prokes Individu </a></li>
    <li class="breadcrumb-item active">Dokumen Individu</li>
</ol>
@php 
if(Auth::user()->role == 'Staff'){
    $kode = Auth::user()->id;
    $val1 = \App\Models\User::select('kode_kecamatan')->where('id', Auth::user()->parent_admin)->first();
    $kode_kecamatan = $val1->kode_kecamatan;
}elseif(Auth::user()->role == 'Admin'){
    $val2 = \App\Models\Prokes::find(request()->individu_id);
    $kode_desa = $val2->kode_desa ?? '';
    $kode = Auth::user()->kode_kecamatan;
    $kode_kecamatan = Auth::user()->kode_kecamatan;
}else{
    $kode = $kode = Auth::user()->id;
    $val2 = \App\Models\Prokes::find(request()->individu_id);
    $kode_kecamatan = $val2->kode_kecamatan;
    $kode_desa = $val2->kode_desa ?? '';
}
@endphp
<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#data">Form Isian Data</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#dokumentasi">Dokumentasi</a>
    </li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="data">
    <form action="{{ route('upload.dokumen_individu') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row mt-4">
        @if(Auth::user()->kode_kecamatan == '')
            <input type="hidden" id="kd_kecamatan" value="{{ $kode_kecamatan }}" />
            <input type="hidden" id="kd_desa" value="{{ $kode_desa }}" />
        @else 
            <input type="hidden" id="kd_kecamatan" value="{{ $kode_kecamatan }}" />
            <input type="hidden" id="kd_desa" value="{{ $kode_desa }}" />
        @endif
            <div class="col-md-2">
                <label><strong>Alamat</strong></label>
            </div>
            <div class="col-md-6" id="kecamatan">
                @if(Auth::user()->role == 'Admin')
                <input type="hidden" name="kecamatan_id" value="{{ Auth::user()->kode_kecamatan }}" />
                <select name="kecamatan_id" id="kecamatan_id" class="form-control" disabled>
                    <option value="">Pilih Kecamatan</option>
                    @php
                    $kecamatan = \App\Models\Kecamatan::all()
                    @endphp
                    @foreach($kecamatan as $val)
                        <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == Auth::user()->kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                    @endforeach
                </select>
                @elseif(Auth::user()->role == 'super admin')
                <input type="hidden" name="kecamatan_id" value="{{ $kode_kecamatan }}"/>
                <select name="kecamatan_id" id="kecamatan_id" class="form-control" required disabled>
                    <option value="">Pilih Kecamatan</option>
                    @php
                    $kecamatan = \App\Models\Kecamatan::all()
                    @endphp
                    @foreach($kecamatan as $val)
                        <option value="{{ $val->code_kecamatan }}"{{ $val->code_kecamatan == $kode_kecamatan ? ' selected' : ''}}> {{ $val->kecamatan }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="kecamatan_id" value="{{ $kode_kecamatan }}" />
                <select name="kecamatan_id" id="kecamatan_id" class="form-control" disabled>
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
        <div class="row mb-3 mt-3" id="desa" style='display:none;'>
            <div class="col-md-2">
                <label><strong>Pilih Desa</strong></label>
            </div>
            <div class="col-md-6" id="kecamatan">
                @if(Auth::user()->role == 'super admin')
                <select name="desa_id" id="desa_id" class="form-control" disabled>
                </select>
                <input type="hidden" name="desa_id" value="{{ $kode_desa }}"/>
                @elseif(Auth::user()->role == 'Admin')
                <select name="desa_id" id="desa_id" class="form-control" disabled>
                </select>
                <input type="hidden" id="kd_desa" name="desa_id" value="{{ $kode_desa }}" />
                @else
                <select name="desa_id" id="desa_id" name="desa_id" class="form-control" disabled>
                </select>
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2">
                <label><strong>Lokasi Pantau</strong></label>
            </div>
            <div class="col-md-6">
                @php
                $xpld = explode(' - ', $val2->kode_lokasi_pantau);
                $master_lokasi_pantau = $xpld[0];
                $lokasi_pantau = $xpld[1];
                @endphp
                <select name="master_lokasi_pantau" reqired class="form-control" id="master_lokasi_pantau">
                    <option value="">Pilih Lokasi</option>
                    <option value="Pusat Perbelanjaan" {{ $master_lokasi_pantau == 'Pusat Perbelanjaan' ? ' selected' : '' }}>Pusat Perbelanjaan</option>
                    <option value="Obyek Wisata" {{ $master_lokasi_pantau == 'Obyek Wisata' ? ' selected' : ''}}>Obyek Wisata</option>
                    <option value="Area Publik" {{ $master_lokasi_pantau == 'Area Publik' ? ' selected' : ''}}>Area Publik</option>
                    <option value="Hotel" {{ $master_lokasi_pantau == 'Hotel' ? ' selected' : ''}}>Hotel</option>
                    <option value="Restoran" {{ $master_lokasi_pantau == 'Restoran' ? ' selected' : ''}}>Restoran</option>
                    <option value="Tempat Ibadah" {{ $master_lokasi_pantau == 'Tempat ibadah' ? ' selected' : ''}}>Tempat Ibadah</option>
                    <option value="Kegiatan Seni Budaya" {{ $master_lokasi_pantau == 'Kegiatan Seni Budaya' ? ' selected' : ''}}>Kegiatan Seni Budaya</option>
                    <option value="Transportasi Umum" {{ $master_lokasi_pantau == 'Transportasi Umum' ? ' selected' : ''}}>Transportasi Umum</option>
                </select>
                <textarea name="lokasi_pantau" class="form-control mt-2" id="lokasi_pantau" placeholder="mohon isi kelengkapan lokasi pantau" >{{ $lokasi_pantau }}</textarea>
                <!-- <input type="text" name="lokasi_pantau" class="form-control mt-2" id="lokasi_pantau" style="display:none" placeholder="mohon isi kelengkapan lokasi pantau" /> -->
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2">
                <label><strong>Tanggal Pantau</strong></label>
            </div>
            <div class="col-md-6">
                <input type="text" name="tanggal_pantau" id="tanggal_pantau" class="form-control" required placeholder="isi tanggal pantau" value="{{ $val2->tanggal_pantau }}">
            </div>
        </div>
        <div class="kolom">
            <div class="row">
                <div class="col-md-2">
                    <label><strong>Upload Dokumen (Harap isi dengan file jpg/jpeg/png)</strong></label>
                </div>
                <div class="col-md-6">
                    <input type="file" name="image[]" id="image" class="form-control" required placeholder="isi tanggal pantau">
                </div>
            </div>
        </div>
        <div class="row mb-3 d-flex justify-content-end">
            <div class="col-md-6">
                <button type="button" class="btn btn-sm btn-success mr-2 tambah-kolom"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Dokumen</button>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2">
                <label><strong>Deskripsi Dokumen</strong></label>
            </div>
            <div class="col-md-6">
                <textarea name="deskripsi_dokumen" id="deskripsi_dokumen" class="form-control"></textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <input type="hidden" name="individu_id" value="{{ request()->individu_id }}" />
            </div>
        </div>
    </form>
  </div>
    <div class="tab-pane fade" id="dokumentasi"> 
        <div class="row ml-1">
            @php 
            $image = \App\Models\DokumenIndividu::where('individu_id', request()->individu_id)->get();
            @endphp
            @foreach($image as $val)
            <a target="_blank" href="{{ asset('dokumen_individu') }}/{{ $val->image }}">  
                <img src="{{ asset('dokumen_individu') }}/{{ $val->image }}" width="100" height="100" style="margin-right: 20px;margin-top: 10px;"/>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.datatables.net/fixedheader/3.1.8/css/fixedHeader.dataTables.min.css"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.ui.timepicker.js?v=0.3.3') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
<script>
$(document).ready(function(){
    var id = 2;
    $('body').on('click','.tambah-kolom', function(){
        var body = '<div class="form' + id + '">';
        body += '<div class="row mb-3">';
        body += '<div class="col-md-4" style="margin-left: 205px;">';
        body += '<input type="file" name="image['+ id +']" id="image" class="form-control" required placeholder="isi tanggal pantau" required>';
        body += '</div>';
        body += '</div>';
        body += '</div>';
        $('.kolom').append(body)
        id++;
    })
    $("#tanggal_pantau").datepicker();
    $('#jam_pantau').timepicker({
        showPeriodLabels: false
    });

    $('body').on('change', '#master_lokasi_pantau', function(){
        $('#lokasi_pantau').show();
    })
    var kodekec = $('#kd_kecamatan').val();
    var kd_desa = $('#kd_desa').val();
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
            var selected = value.kode_kelurahan == kd_desa ? ' selected' : ''
            elementNew = '<option value=' + value.kode_kelurahan +' '+ selected +'>' + value.nama_kelurahan +'</option>';
            $('#desa_id').append(elementNew)
        })
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

    $('body').on('change', '.image', function(){
        var fileInput = $(".image").val()
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        // alert(allowedExtensions.exec(fileInput))
        if (!allowedExtensions.exec(fileInput)) {
            $(".image").val('')
            alert('Harap Input dokumen dengan file jpg, jpeg, png');
            return false;
        }
    })
})
</script>
@endpush