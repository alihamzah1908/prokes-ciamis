@extends('newtheme')
@section('content')
<style>
div.gallery {
  border: 1px solid #ccc;
  margin-bottom: 10px;
}

div.gallery:hover {
  border: 1px solid #777;
}

div.gallery img {
  width: 100%;
  height: auto;
}

div.desc {
  padding: 15px;
  text-align: center;
}

* {
  box-sizing: border-box;
}

.responsive {
  padding: 0 6px;
  float: left;
  width: 24.99999%;
}

@media only screen and (max-width: 700px) {
  .responsive {
    width: 49.99999%;
    margin: 6px 0;
  }
}

@media only screen and (max-width: 500px) {
  .responsive {
    width: 100%;
  }
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
}
</style>
<div class="row ml-3 mr-3 mt-4">
    <div class="col-lg-6" style="margin-top: 25px;">
        <?php
            if(request()->periode_kasus){
                $tanggal_pantau = request()->periode_kasus;
            }else{
                $var = \App\Models\DokumenIndividu::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
                $tanggal_pantau = $var->tanggal_pantau;
            }
        ?>
        <h5><strong>Dokumentasi Pemantauan Protokol Kesehatan<br />Kabupaten Ciamis</strong></h5>
        <h5> {{ date('d M Y', strtotime($tanggal_pantau)) }}</h5>
    </div>
    <div class="col-lg-3" style="margin-top: 25px;">
        <label><strong>Pilih Dokumentasi</strong></label>
        <select name="prokes" class="form-control dokumen">
            <option value="#">Pilih Dokumentasi</option>
            <option value="individu"{{ request()->dokumentasi == 'individu' ? ' selected' : ''}}>Dokumentasi Prokes Individu</option>
            <option value="institusi"{{ request()->dokumentasi == 'institusi' ? ' selected' : ''}}>Dokumentasi Prokes Institusi</option>
        </select>
    </div>
    <div class="col-lg-3" style="margin-top: 50px;">
        <form action='{{ route("dokumentasi") }}' method="get">
            <div class="datepicker date input-group p-0 shadow-sm" id="datepicker">
                <input type="text" name="periode_kasus" placeholder="Pilih Tanggal Pantau" class="form-control py-4 px-4" id="reservationDate" value="{{ request()->periode_kasus }}">
                
                <div class="input-group-append">
                    <button class="input-group-text px-4 btn-warning" type="submit"><i class="fa fa-search"></i>&nbsp;Cari</button>
                </div>
            </div>
            <input type="hidden" name="dokumentasi" placeholder="Pilih Tanggal Pantau" class="form-control py-4 px-4" id="reservationDate" value="{{ request()->dokumentasi }}">
        </form>
    </div>
</div>
<div class="row ml-3 mt-4" style='border-top: 3px solid #b300b3;'>
    @php 
    if(Auth::user()->role == 'Admin'){
        if(request()->dokumentasi == 'individu'){
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)
            ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
            ->get();
        }elseif(request()->dokumentasi == 'institusi'){
            $image = \App\Models\DokumenInstitusi::where('tanggal_pantau', $tanggal_pantau)
            ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
            ->get();
        }else{
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)
            ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
            ->get();
        }
    }else{
        if(request()->dokumentasi == 'individu'){
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)->get();
        }elseif(request()->dokumentasi == 'institusi'){
            $image = \App\Models\DokumenInstitusi::where('tanggal_pantau', $tanggal_pantau)->get();
        }else{
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)->get();
        }
    }
    @endphp
    @if(count($image) != 0)
    @foreach($image as $img)
    <div class="responsive mt-4">
        <div class="gallery">
            @if(request()->dokumentasi == 'individu')
                <a target="_blank" href="{{ asset('dokumen_individu') }}/{{ $img->image }}">
                    <img src="{{ asset('dokumen_individu') }}/{{ $img->image }}" alt="Cinque Terre" width="600" height="400">
                </a>
            @elseif(request()->dokumentasi == 'institusi')
                <a target="_blank" href="{{ asset('dokumen_institusi') }}/{{ $img->image }}">
                    <img src="{{ asset('dokumen_institusi') }}/{{ $img->image }}" alt="Cinque Terre" width="600" height="400">
                </a>
            @else
                <a target="_blank" href="{{ asset('dokumen_individu') }}/{{ $img->image }}">
                    <img src="{{ asset('dokumen_individu') }}/{{ $img->image }}" alt="Cinque Terre" width="600" height="400">
                </a>
            @endif
            <p class="ml-4 mt-2"><strong>Tanggal Pantau</strong> : {{ date('d M Y', strtotime($img->tanggal_pantau)) }}</p>
            <p class="ml-4 mt-2"><strong>Lokasi Pantau</strong> : {{ $img->lokasi_pantau }}</p>
            <p class="ml-4 mt-2"><strong>Desa</strong> : {{ $img->get_desa->nama_kelurahan }}</p>
            <p class="ml-4 mt-2"><strong>Kecamatan</strong> : {{ $img->get_kecamatan->kecamatan }}</p>
        </div>
    </div>
    @endforeach
    @else 
    <h5 class="mt-4">Tidak ada dokumentasi</h5>
    @endif
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        $('body').on('change', '.dokumen', function(){
            var data = $(this).val()
            window.location.href = '{{ route("dokumentasi") }}' + "?dokumentasi=" + data ;
        })
        $("#datepicker").datepicker({ 
            format: 'yyyy-mm-dd'
        });
    })
</script>
@endpush