@extends('newtheme')
@section('content')
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        background-color:
    }
    #map-prokes{
        width: 100%;
        height: 400px;
    }
    .info {
        padding: 6px 8px;
        font: 14px/16px Arial, Helvetica, sans-serif;
        background: white;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }
    
    .info h4 {
        margin: 0 0 5px;
        color: #777;
    }
    
    .legend {
        text-align: left;
        line-height: 20px;
        color: #555;
    }
    
    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }
    .text-card-color {
        color: #b300b3 !important;
    }
    #lokasipantau {
        height: 1000px;
    }
    #pie {
	    height: 500px;
    }
    .highcharts-figure, .highcharts-data-table table {
        max-width: 1000px;
        margin: 1em auto;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/css/loading.css') }}" />
<div class="row ml-3 mr-3 mt-4">
    <div class="col-lg-6" style="margin-top: 25px;">
        <?php
            if(request()->periode_kasus){
                $tanggal_pantau = request()->periode_kasus;
            }else{
                $var = \App\Models\ProkesInstitusi::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
                $tanggal_pantau = $var->tanggal_pantau;
            }
            $kecamatan = \App\Models\Kecamatan::where('code_kecamatan', request()->kecamatan)->first();
        ?>
        <h4><strong>Peta Kepatuhan Prokes Institusi COVID-19 di Kecamatan {{ $kecamatan->kecamatan }}</strong></h4>
        <h5> {{ date('d M Y', strtotime($tanggal_pantau)) }}</h5>
    </div>
    <div class="col-lg-6" style="margin-top: 25px;">
        <form action='{{ route("institusi.desa") }}' method="get">
            <input type="hidden" name="kecamatan" id="code_kecamatan" value=""/>
            <div class="datepicker date input-group p-0 shadow-sm">
                <input type="text" name="periode_kasus" placeholder="Pilih Tanggal Pantau" class="form-control py-4 px-4" id="reservationDate" value="{{ request()->periode_kasus }}">
                <div class="input-group-append">
                    <button class="input-group-text px-4 btn-warning" type="submit"><i class="fa fa-search"></i>&nbsp;Cari</button>
                </div>
            </div>
            <input type="hidden" name="latitude" id="latitude" value=""/>
            <input type="hidden" name="longitude" id="longitude" value=""/>
        </form>
    </div>
</div>
<div class="row ml-3 mr-3 mt-4">
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-center text-uppercase mb-1 text-card-color">
                            JUMLAH WILAYAH</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.freepik.com/free-vector/coughing-person-with-coronavirus_23-2148485525.jpg"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">DESA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                            @php 
                            $desa = \App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                                ->where('kecamatan_id', request()->kecamatan)
                                ->groupBy('desa_id')
                                ->get();
                            @endphp
                            {{ 
                                count($desa)
                            }}
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            JUMLAH SAMPEL</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.freepik.com/free-vector/strong-man-with-good-immune-system-against-viruses_23-2148568830.jpg"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">CUCI TANGAN</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        @endphp
                        {{ $arr->sum('fasilitas_cuci_tangan') }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">SOSIALISASI PROKES</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        @endphp
                        {{ $arr->sum('sosialisasi_prokes') }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">CEK SUHU TUBUH</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        @endphp
                        {{ $arr->sum('cek_suhu_tubuh') }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">PETUGAS PENGAWAS PROKES</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        @endphp
                        {{ $arr->sum('petugas_pengawas_prokes') }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">DESINFEKSI BERKALA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        @endphp
                        {{ $arr->sum('desinfeksi_berkala') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            TINGKAT KEPATUHAN</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.freepik.com/free-vector/strong-man-with-good-immune-system-against-viruses_23-2148568830.jpg"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">CUCI TANGAN</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() * 5;
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">SOSIALISASI PROKES</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() * 5;
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">CEK SUHU TUBUH</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() * 5;
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">PETUGAS PENGAWAS PROKES</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() * 5;
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">DESINFEKSI BERKALA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg() * 5;
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3" >
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            KEPATUHAN PROKES <br /> INSTITUSI</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.flaticon.com/icons/png/512/595/595812.png"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color font-weight-bold"></span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $kepatuhan_prokes = App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
                            ->where('kecamatan_id', request()->kecamatan)
                            ->get();
                        $average = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() + $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() + $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() + $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() + $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg();
                        @endphp
                        {{ round($average, 2) . '%' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
    <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            KEPATUHAN PROKES LOKASI PANTAU</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.freepik.com/free-vector/strong-man-with-good-immune-system-against-viruses_23-2148568830.jpg"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">HOTEL</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="hotel"></div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">KEGIATAN SENI BUDAYA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="sebud">
                        
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">RESTORAN CAFE TEMPAT HIBURAN</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="resto">
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">TEMPAT IBADAH</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="ibadah">
                       
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">AREA PUBLIK</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="publik">
                        
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">OBJEK WISATA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="wisata">
                       
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">PUSAT PERBELANJAAN</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="belanja">
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">TRANSPORTSI UMUM</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color" id="transport">
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row ml-3 mr-3 mt-4" style='border-top: 3px solid #b300b3;'>
    <div class="col-lg-6" style="margin-top: 40px;">
        <div class="row mt-1">
            <h5><strong>Peta Kepatuhan Prokes Institusi COVID-19 di Kecamatan {{ $kecamatan->kecamatan }}</strong></h5>
            <h5>Update Terakhir Data : {{ date('d M Y', strtotime($tanggal_pantau)) }}</h5>
        </div>
        <div class="row" style="margin-top: 20px">
            <div class="fountainX"></div>
            <figure class="highcharts-figure">
                <div id="pie"></div>
            </figure>
        </div>
    </div>
    <div class="col-lg-6" style="margin-top: 25px;">
        <div class="row">
            @if(Auth::user()->kode_kecamatan == '')
            <div class="col-md-6 pilih-kecamatan mt-3">
                <label><strong>Pilih Kecamatan</strong></label>
                <select name="kecamatan" class="form-control kecamatan" style="height: auto">
                    @php
                    $kecamatan = App\Models\Kecamatan::orderBy('kecamatan')->get();
                    @endphp
                    <option value="#">PILIH KECAMATAN</option>
                    @foreach($kecamatan as $val)
                        <option value="{{ $val }}"{{ $val->code_kecamatan == request()->kecamatan ? ' selected' : ''}}>{{ $val->kecamatan }}</option>
                    @endforeach
                </select> 
            </div>
            <input type="hidden" id="code_kecamatan" value="" />
            <input type="hidden" id="latitude" value="" />
            <input type="hidden" id="longitude" value="" />
            @endif
        </div>
        <div class="row">
            <div class="col-md-12 pilih-kecamatan mt-3">
                <a href="javascript:void(0)" class="btn cuci mt-2 kasus border mr-2" data-bind="cuci">
                    <i class="fa fa-meh-o" aria-hidden="true"></i> Cuci Tangan
                </a>
                <a href="javascript:void(0)" class="btn mt-2 sosialisasi kasus border mr-2" data-bind="sosialisasi">
                    <i class="fa fa-smile-o" aria-hidden="true"></i> Sosialisasi Prokes
                </a>
                <a href="javascript:void(0)" class="btn mt-2 suhu kasus border mr-2" data-bind="suhu">
                    <i class="fa fa-smile-o" aria-hidden="true"></i> Cek Suhu Tubuh
                </a>
                <a href="javascript:void(0)" class="btn mt-2 petugas kasus border mr-2" data-bind="petugas">
                    <i class="fa fa-smile-o" aria-hidden="true"></i> Petugas Pengawas Prokes
                </a>
                <a href="javascript:void(0)" class="btn desinfeksi mt-2 kasus border mr-2" data-bind="desinfeksi">
                    <i class="fa fa-smile-o" aria-hidden="true"></i> Desinfeksi Berkala
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 pilih-kecamatan mt-3">
                <!-- <div id="loading"></div> -->
                <div class="fountainX"></div>
                <div id="map-prokes">
            </div>
        </div>
    </div>
</div>
<div class="row mt-4" style='border-top: 3px solid #b300b3;'>
    <div class="col-md-6 mt-4">
        <h4>Peta Kepatuhan Prokes</h4>
        <h5>Peta Kepatuhan Prokes merupakan pemetaan tingkat kepatuhan masyarakat terhadap protokol kesehatan khususnya protokol menggunakan masker dan menjaga jarak. Data diperoleh dari hasil pengamatan secara sampel di beberapa titik dan pada jam tertentu. Perbedaan warna merepresentasikan perbedaan tingkat kepatuhan masyarakat terhadap protokol kesehatan.</h5>
        <table id="datatable" class='table table-striped mt-4'>
            <thead>
                <tr>
                    <th>KECAMATAN</th>
                    <th>CUCI TANGAN</th>
                    <th>SOSIALISASI PROKES</th>
                    <th>CEK SUHU TUBUH</th>
                    <th>PETUGAS PENGAWAS PROKES</th>
                    <th>DESINFEKSI BERKALA</th>
                </tr>
            </thead>
            <tbody>
            @php 
            $data = \App\Models\Desa::orderBy('nama_kelurahan', 'asc')
                ->where('kode_kecamatan', request()->kecamatan)
                ->get();
            @endphp
            @foreach($data as $val)
            @php 
            $kepatuhan_prokes = \App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)
            ->where('desa_id', $val->kode_kelurahan)->get();
            @endphp
                <tr>
                    <td>{{ $val->nama_kelurahan }}</td>
                    <td>{{ round($kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg(), 2) * 5 }}</td>
                    <td>{{ round($kepatuhan_prokes->pluck('sosialisasi_prokes')->avg(), 2) * 5}}</td>
                    <td>{{ round($kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg(), 2) * 5}}</td>
                    <td>{{ round($kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg(), 2) * 5}}</td>
                    <td>{{ round($kepatuhan_prokes->pluck('desinfeksi_berkala')->avg(), 2) * 5}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-6 mt-4 ">
        <h4>Grafik Kepatuhan Prokes</h4>
        <!-- <canvas id="densityChart" width="500" height="600"></canvas>
        <canvas id="densityChart-cuci" width="500" height="600" style="display: none;"></canvas>
        <canvas id="densityChart-sosialisasi" width="500" height="600" style="display: none;"></canvas>
        <canvas id="densityChart-desinfeksi" width="500" height="600" style="display: none;"></canvas>
        <canvas id="densityChart-suhu" width="500" height="600" style="display: none;"></canvas>
        <canvas id="densityChart-petugas" width="500" height="600" style="display: none;"></canvas> -->
        <!-- <figure class="highcharts-figure">
            <div id="container"></div>
        </figure> -->
        <div class="fountainX"></div>
        <div id="lokasipantau" class="mt-4"></div>
    </div>
</div>
<div class="row mt-4" style='border-top: 3px solid #b300b3;'>
    <div class="col-lg-12 mt-4 mr-4">
        <h4>Peta Kepatuhan Prokes</h4>
        <h5>Peta Kepatuhan Prokes merupakan pemetaan tingkat kepatuhan masyarakat terhadap protokol kesehatan khususnya protokol menggunakan masker dan menjaga jarak. Data diperoleh dari hasil pengamatan secara sampel di beberapa titik dan pada jam tertentu. Perbedaan warna merepresentasikan perbedaan tingkat kepatuhan masyarakat terhadap protokol kesehatan.</h5>
        <div id="container"></div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<!-- <script src="{{ asset('js/peta_desa.js') }}"></script> -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
    var code_kecamatan = "{{ request()->kecamatan }}"
    var latitude = "{{ request()->latitude }}"
    var longitude = "{{ request()->longitude }}"
    $("#code_kecamatan").val(code_kecamatan);
    $("#latitude").val(latitude);
    $("#longitude").val(longitude);
    var map = L.map('map-prokes').setView([latitude, longitude], 11.5);
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 15,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
            'Imagery ?? <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox/light-v9',
        tileSize: 512,
        zoomOffset: 1
    }).addTo(map);


    // control that shows state info on hover
    var info = L.control();

    info.onAdd = function(map) {
        this._div = L.DomUtil.create('div', 'info');
        this.update();
        return this._div;
    };

    info.update = function(props) {
        var total = props ? props.total_kasus : '';
        this._div.innerHTML = '<p><strong>Presentasi Kepatuhan Prokes</strong></p>' + (props ?
            '<b>' + props.name + '</b><br /> Level : ' + props.density:
            'Gerakan Kursor Dalam Peta');
    };

    info.addTo(map);


    // get color depending on population density value
    // function getColor(d) {
    //     return d < 1.5 ? '#00ff00':
    //         d < 2.5 ? '#ffff00' :
    //         d < 3.5 ? '#ff9900' :
    //         d >= 3.5 ? '#ff3300' : '#ff3300';
    // }

    function getColor(d) {
        return d == 0 ? '#ffe6e6':
            d < 61 ? '#ff3300':
            d < 76 ? '#ff9900' :
            d < 91 ? '#ffff00' :
            d > 91 ? '#00ff00' : '#ffe6e6';
    }

    function style(feature) {
        return {
            weight: 2,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 0.7,
            fillColor: getColor(feature.properties.density)
        };
    }

    function highlightFeature(e) {
        var layer = e.target;

        layer.setStyle({
            weight: 5,
            color: '#666',
            dashArray: '',
            fillOpacity: 0.7
        });

        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }

        info.update(layer.feature.properties);
    }

    var geojson;

    function resetHighlight(e) {
        geojson.resetStyle(e.target);
        info.update();
    }

    function zoomToFeature(e) {
        map.fitBounds(e.target.getBounds());
    }

    function onEachFeature(feature, layer) {
        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
            click: zoomToFeature
        });
    }
    var url = "{{ route('sebaran.prokes_institusi_desa') }}";
    $.ajax({
        data: 'json',
        method: 'get',
        url: url,
        data: {
            code: "{{ request()->kecamatan }}",
            periode_kasus: "{{ request()->periode_kasus }}"
        },
        beforeSend: function(){
            var loading = '<div id="fountainG_1" class="fountainG"></div>';
            loading += '<div id="fountainG_1" class="fountainG"></div>';
            loading += '<div id="fountainG_2" class="fountainG"></div>';
            loading += '<div id="fountainG_3" class="fountainG"></div>';
            loading += '<div id="fountainG_4" class="fountainG"></div>';
            loading += '<div id="fountainG_5" class="fountainG"></div>';
            loading += '<div id="fountainG_6" class="fountainG"></div>';
            loading += '<div id="fountainG_7" class="fountainG"></div>';
            $('.fountainX').html(loading)
        }
    }).done(function(states) {
        $('.fountainX').html(' ')
        // console.log(states.features)
        var kecamatan = [];
        var jumlah_penduduk = [];
        var color = [];
        $.each(states.features, function(index, value){
            kecamatan.push(value.properties.name)
            jumlah_penduduk.push(value.properties.density)
            color.push('#FD8D3C')
        })
        console.log(states)
        geojson = L.geoJson(states, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        // map.attributionControl.addAttribution('Population data &copy; <a href="http://census.gov/">US Census Bureau</a>');


        var legend = L.control({
            position: 'bottomleft'
        });

        legend.onAdd = function(map) {

            var div = L.DomUtil.create('div', 'info legend'),
                grades = ["0 - 60%", "61 - 75%", "76 - 90%", "91 - 100%"],
                labels = ["Keterangan"],
                from, to;

            for (var i = 0; i < 4; i++) {
                patokan = [60, 75, 90, 100]
                to = i + 1;
                labels.push(
                    '<i style="background:' + getColor(patokan[i]) + '"></i> ' +
                    grades[i]);
            }

            div.innerHTML = labels.join('<br>');
            return div;
        };

        legend.addTo(map);


        // Chart javascript code
        var densityCanvas = document.getElementById("densityChart");

        Chart.defaults.global.defaultFontFamily = "Calibri";
        Chart.defaults.global.defaultFontSize = 11.5;
        var densityData = {
            label: ' Presentasi Kepatuhan Prokes',
            data: jumlah_penduduk,
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1,
            hoverBorderWidth: 0
        };

        var chartOptions = {
            scales: {
                yAxes: [{
                    barPercentage: 0.4
                }]
            },
            elements: {
                rectangle: {
                    borderSkipped: 'left',
                }
            }
        };
        var barChart = new Chart(densityCanvas, {
        type: 'horizontalBar',
            data: {
                labels: kecamatan,
                datasets: [densityData],
            },
            options: chartOptions
        });
    })
    $('body').on('change','.kecamatan', function(){
        var data = JSON.parse($(this).val())
        window.location.href = '{{ route("institusi.desa") }}' + "?kecamatan=" + data.code_kecamatan + "&periode_kasus=" + "{{ request()->periode_kasus }}" + "&latitude=" + data.latitude + "&longitude=" + data.longitude + "";
    })
    $('body').on('click','.kasus', function(){
        var kasus = $(this).attr('data-bind');
        if(kasus == 'cuci'){
            $('.cuci').addClass('btn-warning')
            $('.suhu').removeClass('btn-warning')
            $('.petugas').removeClass('btn-warning')
            $('.sosialisasi').removeClass('btn-warning')
            $('.desinfeksi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-cuci').show()
            $('#densityChart-suhu').hide()
            $('#densityChart-petugas').hide()
            $('#densityChart-sosialisasi').hide()
            $('#densityChart-desinfeksi').hide()
        }else if(kasus == 'sosialisasi'){
            $('.cuci').removeClass('btn-warning')
            $('.suhu').removeClass('btn-warning')
            $('.petugas').removeClass('btn-warning')
            $('.sosialisasi').addClass('btn-warning')
            $('.desinfeksi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-cuci').hide()
            $('#densityChart-suhu').hide()
            $('#densityChart-petugas').hide()
            $('#densityChart-sosialisasi').show()
            $('#densityChart-desinfeksi').hide()
        }else if(kasus == 'suhu'){
            $('.cuci').removeClass('btn-warning')
            $('.suhu').addClass('btn-warning')
            $('.petugas').removeClass('btn-warning')
            $('.sosialisasi').removeClass('btn-warning')
            $('.desinfeksi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-cuci').hide()
            $('#densityChart-suhu').show()
            $('#densityChart-petugas').hide()
            $('#densityChart-sosialisasi').hide()
            $('#densityChart-desinfeksi').hide()
        }else if(kasus == 'petugas'){
            $('.cuci').removeClass('btn-warning')
            $('.suhu').removeClass('btn-warning')
            $('.petugas').addClass('btn-warning')
            $('.sosialisasi').removeClass('btn-warning')
            $('.desinfeksi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-cuci').hide()
            $('#densityChart-suhu').hide()
            $('#densityChart-petugas').show()
            $('#densityChart-sosialisasi').hide()
            $('#densityChart-desinfeksi').hide()
        }else if(kasus == 'desinfeksi'){
            $('.cuci').removeClass('btn-warning')
            $('.suhu').removeClass('btn-warning')
            $('.petugas').removeClass('btn-warning')
            $('.sosialisasi').removeClass('btn-warning')
            $('.desinfeksi').addClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-cuci').hide()
            $('#densityChart-suhu').hide()
            $('#densityChart-petugas').hide()
            $('#densityChart-sosialisasi').hide()
            $('#densityChart-desinfeksi').show()
        }
        var url = "{{ route('sebaran.prokes_institusi_desa') }}";
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: url,
            data: {
                sebaran_kasus: kasus,
                code: "{{ request()->kecamatan }}",
                periode_kasus: "{{ request()->periode_kasus }}"
            },
            beforeSend: function(){
                var loading = '<div id="fountainG_1" class="fountainG"></div>';
                loading += '<div id="fountainG_1" class="fountainG"></div>';
                loading += '<div id="fountainG_2" class="fountainG"></div>';
                loading += '<div id="fountainG_3" class="fountainG"></div>';
                loading += '<div id="fountainG_4" class="fountainG"></div>';
                loading += '<div id="fountainG_5" class="fountainG"></div>';
                loading += '<div id="fountainG_6" class="fountainG"></div>';
                loading += '<div id="fountainG_7" class="fountainG"></div>';
                $('.fountainX').html(loading)
            }
        }).done(function(states){
            $(".fountainX").html(' ')
            // console.log(states.features)
            var kecamatan = [];
            var jumlah_penduduk = [];
            var color = [];
            $.each(states.features, function(index, value){
                kecamatan.push(value.properties.name)
                jumlah_penduduk.push(value.properties.density)
                color.push('#FD8D3C')
            })
            console.log(states)
            geojson = L.geoJson(states, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);

            // map.attributionControl.addAttribution('Population data &copy; <a href="http://census.gov/">US Census Bureau</a>');

            var legend = L.control({
                position: 'bottomleft'
            });
            

            // Chart javascript code
            var densityCanvas = document.getElementById("densityChart-" + kasus);

            Chart.defaults.global.defaultFontFamily = "Calibri";
            Chart.defaults.global.defaultFontSize = 13.5;
            var densityData = {
                label: ' Presentasi Kepatuhan Prokes',
                data: jumlah_penduduk,
                backgroundColor: color,
                borderColor: color,
                borderWidth: 1,
                hoverBorderWidth: 0
            };

            var chartOptions = {
                scales: {
                    yAxes: [{
                        barPercentage: 0.4
                    }]
                },
                elements: {
                    rectangle: {
                        borderSkipped: 'left',
                    }
                }
            };
            var barChart = new Chart(densityCanvas, {
            type: 'horizontalBar',
                data: {
                    labels: kecamatan,
                    datasets: [densityData],
                },
                options: chartOptions
            });
        })
    })
</script>
<script type='text/javascript'>
    Highcharts.chart('container', {
        data: {
            table: 'datatable'
        },
        chart: {
            type: 'column'
        },
        title: {
            text: 'Grafik Perkembangan Protokol Kesehatan Institusi Kecamatan'
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: 'Units'
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }
        }
    });
</script>
<script>
$.ajax({
    dataType: 'json',
    method: 'get',
    url: '{{ route("lokasi.pantau_institusi_desa") }}',
    data: {
        "kode_kecamatan" : "{{ request()->kecamatan }}",
        "tanggal_pantau" : "{{ $tanggal_pantau }}"
    },
}).done(function(response){
    $("#hotel").html(response.hotel.toFixed(2) + '%')
    $("#sebud").html(response.sebud.toFixed(2) + '%')
    $("#resto").html(response.resto.toFixed(2) + '%')
    $("#ibadah").html(response.ibadah.toFixed(2) + '%')
    $("#publik").html(response.publik.toFixed(2) + '%')
    $("#wisata").html(response.wisata.toFixed(2) + '%')
    $("#belanja").html(response.belanja.toFixed(2) + '%')
    $("#transport").html(response.transport.toFixed(2) + '%')
    Highcharts.chart('lokasipantau', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Kepatuhan Prokes Institusi Berdasarkan Lokasi Pantau'
        },
        // subtitle: {
        //     text: 'Source: <a href="https://en.wikipedia.org/wiki/World_population">Wikipedia.org</a>'
        // },
        xAxis: {
            categories: ['Hotel', 'Kegiatan Seni Budaya', 'Restoran, Cafe, Tempat Hiburan', 'Tempat Ibadah', 'Area Publik', 'Objek Wisata', 'Pusat Perbelanjaan', 'Transportasi Umum'],
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                // text: 'Population (millions)',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        // tooltip: {
        //     valueSuffix: ' millions'
        // },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Fasilitas Cuci Tangan',
            data: [parseFloat(response.cuci_tangan_hotel.toFixed(2)), parseFloat(response.cuci_tangan_sebud.toFixed(2)), parseFloat(response.cuci_tangan_resto.toFixed(2)), parseFloat(response.cuci_tangan_ibadah.toFixed(2)) ,parseFloat(response.cuci_tangan_publik.toFixed(2)), parseFloat(response.cuci_tangan_wisata.toFixed(2)), parseFloat(response.cuci_tangan_belanja), parseFloat(response.cuci_tangan_transport.toFixed(2))]
            },{
                name: 'Sosialisasi Prokes',
                data: [parseFloat(response.prokes_hotel.toFixed(2)), parseFloat(response.prokes_sebud.toFixed(2)), parseFloat(response.prokes_resto.toFixed(2)), parseFloat(response.prokes_ibadah.toFixed(2)) ,parseFloat(response.prokes_publik.toFixed(2)), parseFloat(response.prokes_wisata.toFixed(2)), parseFloat(response.prokes_belanja.toFixed(2)), parseFloat(response.prokes_transport.toFixed(2))]
            },{
                name: 'Cek Suhu Tubuh',
                data: [parseFloat(response.suhu_hotel.toFixed(2)), parseFloat(response.suhu_sebud.toFixed(2)), parseFloat(response.suhu_resto.toFixed(2)), parseFloat(response.suhu_ibadah.toFixed(2)) , parseFloat(response.suhu_publik.toFixed(2)), parseFloat(response.suhu_wisata.toFixed(2)), parseFloat(response.suhu_belanja.toFixed(2)), parseFloat(response.suhu_transport.toFixed(2))]
            },{
                name: 'Petugas Pengawas Prokes',
                data: [parseFloat(response.pengawas_hotel.toFixed(2)), parseFloat(response.pengawas_sebud.toFixed(2)), parseFloat(response.pengawas_resto.toFixed(2)), parseFloat(response.pengawas_ibadah.toFixed(2)) ,parseFloat(response.pengawas_publik.toFixed(2)), parseFloat(response.pengawas_wisata.toFixed(2)), parseFloat(response.pengawas_belanja.toFixed(2)), parseFloat(response.pengawas_transport.toFixed(2))]
            },{
                name: 'Desinfeksi Berkala',
                data: [parseFloat(response.desinfeksi_hotel.toFixed(2)), parseFloat(response.desinfeksi_sebud.toFixed(2)), parseFloat(response.desinfeksi_resto.toFixed(2)), parseFloat(response.desinfeksi_ibadah.toFixed(2)) ,parseFloat(response.desinfeksi_publik.toFixed(2)), parseFloat(response.desinfeksi_wisata.toFixed(2)), parseFloat(response.desinfeksi_belanja.toFixed(2)), parseFloat(response.desinfeksi_transport.toFixed(2))]
        }]
    });
})
</script>
<script type="text/javascript">
    var url = "{{ route('sebaran.prokes_institusi_desa') }}";
    $.ajax({
        data: 'json',
        method: 'get',
        url: url,
        data: {
            code: "{{ request()->kecamatan }}",
            periode_kasus: "{{ request()->periode_kasus }}"
        },
        beforeSend: function(){
            var loading = '<div id="fountainG_1" class="fountainG"></div>';
            loading += '<div id="fountainG_1" class="fountainG"></div>';
            loading += '<div id="fountainG_2" class="fountainG"></div>';
            loading += '<div id="fountainG_3" class="fountainG"></div>';
            loading += '<div id="fountainG_4" class="fountainG"></div>';
            loading += '<div id="fountainG_5" class="fountainG"></div>';
            loading += '<div id="fountainG_6" class="fountainG"></div>';
            loading += '<div id="fountainG_7" class="fountainG"></div>';
            $('.fountainX').html(loading)
        }
    }).done(function(states) {
        const properties = []
        $.each(states.features, function(index, value){
            properties.push(value.properties)
        })
        var kecamatan = properties
        Highcharts.chart('pie', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Kepatuhan Prokes Individu'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} %</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} %'
                    }
                }
            },
            series: [{
                name: 'Level ',
                colorByPoint: true,
                data: kecamatan
            }]
        });
    })
</script>
@endpush