@extends('newtheme')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css?family=Inconsolata|Source+Sans+Pro:200,300,400,600');
    h1 {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 22px;
        color: #151E3F;
        font-weight: 300;
        letter-spacing: 2px;
    }

    .responsive {
        padding: 0 6px;
        float: left;
        width: 24.99999%;
    }

    .wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        > * {
            margin: 5px;
        }
    }

    .media {
        width: 300px;
        height: 200px;
        overflow: hidden;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        img {
            max-width: 100%;
            height: auto;
        }
    }

    .layer {
        opacity: 0;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 10px;
        height: 90%;
        background: #FFF;
        color: #151E3F;
        transition: all 0.9s ease;
        p {
            transition: all 0.9s ease;
            transform: scale(0.1)
        }
    }

    p {
        font-family: 'Inconsolata', monospace;
        text-align: center;
        font-size: 15px;
        letter-spacing:1px;
    }
    .desc {
        font-family: 'Inconsolata', monospace;
        text-align: left;
        font-size: 15px;
        letter-spacing:1px;
    }

    .media:hover .layer {
        opacity: 0.8;
        width: 90%;
        transition: all 0.5s ease;
        p {
            transform: scale(1);
            transition: all 0.9s ease;
        }
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
                ->orderBy('id','desc')
                ->get();
        }elseif(request()->dokumentasi == 'institusi'){
            $image = \App\Models\DokumenInstitusi::where('tanggal_pantau', $tanggal_pantau)
                ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
                ->orderBy('id','desc')
                ->get();
        }else{
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)
                ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
                ->orderBy('id','desc')
                ->get();
        }
    }else{
        if(request()->dokumentasi == 'individu'){
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)
                ->orderBy('id','desc')
                ->get();
        }elseif(request()->dokumentasi == 'institusi'){
            $image = \App\Models\DokumenInstitusi::where('tanggal_pantau', $tanggal_pantau)
                ->orderBy('id','desc')
                ->get();
        }else{
            $image = \App\Models\DokumenIndividu::where('tanggal_pantau', $tanggal_pantau)
                ->orderBy('id','desc')
                ->get();
        }
    }
    @endphp
    @if(count($image) != 0)
    @foreach($image as $img)
    <div class="responsive mt-4">
        <div class="gallery">
            @if(request()->dokumentasi == 'individu')
                <a target="_blank" href="{{ asset('dokumen_individu') }}/{{ $img->image }}">  
                    <div class="wrapper">
                        <div class="media">
                            <div class="layer">
                                <p>Prokes Institusi</p>
                            </div>
                            <img src="{{ asset('dokumen_individu') }}/{{ $img->image }}" alt="" />
                        </div>
                    </div>
                </a>
            @elseif(request()->dokumentasi == 'institusi')
                <a target="_blank" href="{{ asset('dokumen_institusi') }}/{{ $img->image }}">
                    <div class="wrapper">
                        <div class="media">
                            <div class="layer">
                                <p>Prokes Institusi</p>
                            </div>
                            <img src="{{ asset('dokumen_institusi') }}/{{ $img->image }}" alt="" />
                        </div>
                    </div>
                </a>
            @else
                <a target="_blank" href="{{ asset('dokumen_individu') }}/{{ $img->image }}">
                    <div class="wrapper">
                        <div class="media">
                            <div class="layer">
                                <p>Prokes Individu</p>
                            </div>
                            <img src="{{ asset('dokumen_individu') }}/{{ $img->image }}" alt="" />
                        </div>
                    </div>
                </a>
            @endif
            <div class="ml-4 mt-2 desc"><strong>Tanggal Pantau</strong> : {{ date('d M Y', strtotime($img->tanggal_pantau)) }}</div>
            <div class="ml-4 mt-2 desc"><strong>Lokasi Pantau</strong> : {{ $img->lokasi_pantau }}</div>
            <div class="ml-4 mt-2 desc"><strong>Desa</strong> : {{ $img->get_desa->nama_kelurahan }}</div>
            <div class="ml-4 mt-2 desc"><strong>Kecamatan</strong> : {{ $img->get_kecamatan->kecamatan }}</div>
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
            window.location.href = '{{ route("dokumentasi") }}' + "?dokumentasi=" + data + "&kecamatan={{ request()->kecamatan }}&periode_kasus={{ request()->periode_kasus}}&latitude={{ request()->latitude}}&longitude={{ request()->longitude }}";
        })
        $("#datepicker").datepicker({ 
            format: 'yyyy-mm-dd'
        });
    })
</script>
@endpush