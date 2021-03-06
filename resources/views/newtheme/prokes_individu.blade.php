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

    #container {
        height: 400px;
        width: 100%;
    }
    #lokasipantau {
        height: 1000px;
    }
    .text-card-color {
        color: #b300b3 !important;
    },
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
                $var = \App\Models\Prokes::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
                $tanggal_pantau = $var->tanggal_pantau;
            }
        ?>
        <h5><strong>Dashboard Summary Protokol Kesehatan Individu <br />Kabupaten Ciamis</strong></h5>
        <h5> {{ date('d M Y', strtotime($tanggal_pantau)) }}</h5>
    </div>
    <div class="col-lg-6" style="margin-top: 25px;">
        <form action='{{ route("prokes.individu") }}' method="get">
            <div class="datepicker date input-group p-0 shadow-sm">
                <input type="text" name="periode_kasus" placeholder="Pilih Tanggal Pantau" class="form-control py-4 px-4" id="reservationDate" value="{{ request()->periode_kasus }}">
                <div class="input-group-append">
                    <button class="input-group-text px-4 btn-warning" type="submit"><i class="fa fa-search"></i>&nbsp;Cari</button>
                </div>
            </div>
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
                        <span class="text-card-color">KECAMATAN</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                            @php
                            $kecamatan = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)
                            ->groupBy('kode_kecamatan')
                            ->get();
                            @endphp
                            {{ count($kecamatan) }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">DESA</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $desa = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)
                            ->groupBy('kode_desa')
                            ->get();
                        @endphp
                        {{ count($desa) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
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
                        <span class="text-card-color">PAKAI MASKER</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
                        @endphp
                        {{ $arr->sum('pakai_masker') }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">JAGA JARAK</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
                        $arr->sum('jaga_jarak');
                        @endphp
                        {{ $arr->sum('jaga_jarak') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            TINGKAT KEPATUHAN</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.flaticon.com/icons/png/512/595/595812.png"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">PAKAI MASKER</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
                        $jumlah = $arr->sum('pakai_masker') + $arr->sum('tidak_pakai_masker');
                        @endphp
                        @php
                        if($jumlah != 0) {
                        $pakai_masker = ($arr->sum('pakai_masker') / ($arr->sum('pakai_masker') + $arr->sum('tidak_pakai_masker'))) * 100;
                        }else{
                        $pakai_masker = 0;
                        }
                        @endphp
                        {{ round($pakai_masker, 2) . '%' }}
                        </div>
                    </div>
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">JAGA JARAK</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php
                        $arr = App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
                        $jumlah = $arr->sum('jaga_jarak') + $arr->sum('tidak_jaga_jarak');
                        @endphp
                        @php
                        if($jumlah != 0) {
                            $jaga_jarak = ($arr->sum('jaga_jarak') / ($arr->sum('jaga_jarak') + $arr->sum('tidak_jaga_jarak'))) * 100;
                        }else{
                            $jaga_jarak = 0;
                        }
                        @endphp
                        {{ round($jaga_jarak, 2) . '%' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-center text-xs font-weight-bold text-uppercase mb-1 text-card-color">
                            KEPATUHAN PROKES</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color"></div>
                    </div>
                    <div class="col-auto">
                        <!-- <img height='100' width="100" src="https://image.flaticon.com/icons/png/512/595/595812.png"> -->
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mr-2 text-center">
                        <span class="text-card-color">MASKER & JAGA JARAK</span>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 total-upload text-card-color">
                        @php 
                        $kepatuhan_prokes = App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
                        $total_masker = $kepatuhan_prokes->pluck('pakai_masker')->sum() + $kepatuhan_prokes->pluck('tidak_pakai_masker')->sum();
                        $total_jarak = $kepatuhan_prokes->pluck('jaga_jarak')->sum() + $kepatuhan_prokes->pluck('tidak_jaga_jarak')->sum();
                        $kepatuhan_masker = ($kepatuhan_prokes->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
                        $kepatuhan_jaga_jarak = ($kepatuhan_prokes->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
                        $kepatuhan = ($kepatuhan_masker + $kepatuhan_jaga_jarak) / 2;
                        @endphp
                        {{ round($kepatuhan, 2) . '%' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row ml-3 mt-4" style='border-top: 3px solid #b300b3;'>
    <div class="col-lg-6" style="margin-top: 40px;">
        <div class="row mt-1">
            <h5><strong>Peta Kepatuhan Prokes Individu COVID-19 di Kabupaten Ciamis</strong></h5>
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
            <div class="col-md-6 pilih-kecamatan mt-3">
                <label><strong>Pilih Kecamatan</strong></label>
                <select name="kecamatan" class="form-control kecamatan" style="height: auto">
                    @php
                    $kecamatan = App\Models\Kecamatan::orderBy('kecamatan')->get();
                    @endphp
                    <option value="#">PILIH KECAMATAN</option>
                    @foreach($kecamatan as $val)
                        <option value="{{ $val }}">{{ $val->kecamatan }}</option>
                    @endforeach
                </select> 
            </div>
            <input type="hidden" id="code_kecamatan" value="" />
            <input type="hidden" id="latitude" value="" />
            <input type="hidden" id="longitude" value="" />
        </div>
        <div class="row">
            <div class="col-md-12 pilih-kecamatan mt-3">
                <a href="javascript:void(0)" class="btn masker kasus border mt-2 mr-2" data-bind="masker">
                    <i class="fa fa-meh-o" aria-hidden="true"></i> Kepatuhan Masker
                </a>
                <a href="javascript:void(0)" class="btn jarak kasus border mt-2 mr-2" data-bind="jarak">
                    <i class="fa fa-smile-o" aria-hidden="true"></i> Kepatuhan Jarak
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 pilih-kecamatan mt-3">
                <div class="fountainX"></div>
                <div id="map-prokes">
            </div>
        </div>
    </div>
</div>
<div class="row mt-4" style='border-top: 3px solid #b300b3;'>
    <div class="col-lg-6 mt-4">
        <h4>Peta Kepatuhan Prokes</h4>
        <h5>Peta Kepatuhan Prokes merupakan pemetaan tingkat kepatuhan masyarakat terhadap protokol kesehatan khususnya protokol menggunakan masker dan menjaga jarak. Data diperoleh dari hasil pengamatan secara sampel di beberapa titik dan pada jam tertentu. Perbedaan warna merepresentasikan perbedaan tingkat kepatuhan masyarakat terhadap protokol kesehatan.</h5>
        <table id="datatable" class='table table-striped mt-4'>
            <thead>
                <tr>
                    <th>KECAMATAN</th>
                    <th>Kepatuhan Masker</th>
                    <th>Kepatuhan Jaga Jarak</th>
                    <th>Kepatuhan Individu</th>
                </tr>
            </thead>
            <tbody>
            @php 
            $data = \App\Models\Kecamatan::orderBy('kecamatan','asc')->get();
            @endphp
            @foreach($data as $val)
            @php 
            $kepatuhan_prokes1 = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->where('kode_kecamatan', $val->code_kecamatan)->get();
            $total_masker1 = $kepatuhan_prokes1->pluck('pakai_masker')->sum() + $kepatuhan_prokes1->pluck('tidak_pakai_masker')->sum();
            $total_jarak1 = $kepatuhan_prokes1->pluck('jaga_jarak')->sum() + $kepatuhan_prokes1->pluck('tidak_jaga_jarak')->sum();
            $kepatuhan_masker1 = ($kepatuhan_prokes1->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes1->pluck('pakai_masker')->sum() / $total_masker1) * 100 : 0;
            $kapatuhan_jarak1 = ($kepatuhan_prokes1->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes1->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
            @endphp
                <tr>
                    <td>{{ $val->kecamatan }}</td>
                    <td>{{ round(($kepatuhan_prokes1->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes1->pluck('pakai_masker')->sum() / $total_masker1) * 100 : 0, 2) }}</td>
                    <td>{{ round(($kepatuhan_prokes1->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes1->pluck('jaga_jarak')->sum() / $total_jarak1) * 100 : 0, 2) }}</td>
                    <td>{{ round(($kepatuhan_masker1 + $kapatuhan_jarak1) / 2, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-6 mt-4 mb-4">
        <!-- <canvas id="densityChart" width="500" height="800"></canvas>
        <canvas id="densityChart-masker" width="500" height="800" style="display:none;"></canvas>
        <canvas id="densityChart-jarak" width="500" height="800" style="display:none;"></canvas>
        <canvas id="densityChart-institusi" width="500" height="800" style="display:none;"></canvas> -->
        <!-- <figure class="highcharts-figure">
            <div id="containers"></div>
        </figure> -->
        <h4>Grafik Kepatuhan Prokes</h4>
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
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script type="text/javascript">
    var map = L.map('map-prokes').setView([-7.300000, 108.400000], 10);
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
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
    var url = "{{ route('sebaran.prokes_individu') }}";
    $.ajax({
        data: 'json',
        method: 'get',
        url: url,
        data: {
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
        window.location.href = '{{ route("individu.desa") }}' + "?kecamatan=" + data.code_kecamatan + "&periode_kasus=" + "{{ request()->periode_kasus }}" + "&latitude=" + data.latitude + "&longitude=" + data.longitude + "";
    })
    $('body').on('click','.kasus', function(){
        var kasus = $(this).attr('data-bind');
        $('#densityChart').hide()
        if(kasus == 'masker'){
            $('.masker').addClass('btn-warning')
            $('.jarak').removeClass('btn-warning')
            $('.institusi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-masker').show()
            $('#densityChart-jarak').hide()
            $('#densityChart-institusi').hide()
        }else if(kasus == 'jarak'){
            $('.masker').removeClass('btn-warning')
            $('.jarak').addClass('btn-warning')
            $('.institusi').removeClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-masker').hide()
            $('#densityChart-jarak').show()
            $('#densityChart-institusi').hide()
        }else{
            $('.masker').removeClass('btn-warning')
            $('.jarak').removeClass('btn-warning')
            $('.institusi').addClass('btn-warning')
            $('#densityChart').hide()
            $('#densityChart-masker').hide()
            $('#densityChart-jarak').hide()
            $('#densityChart-institusi').show()
        }
        var url = "{{ route('sebaran.prokes_individu') }}";
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: url,
            data: {
                sebaran_kasus: kasus,
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
            

            // Chart javascript code
            var densityCanvas = document.getElementById("densityChart-" + kasus);

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
            text: 'Grafik Perkembangan Protokol Kesehatan Individu Kabupaten Ciamis'
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
        url: '{{ route("lokasi.pantau") }}',
        data: {
            "tanggal_pantau": "{{ $tanggal_pantau }}"
        },
    }).done(function(response){
        Highcharts.chart('lokasipantau', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Kepatuhan Prokes Individu Berdasarkan Lokasi Pantau'
            },
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
            tooltip: {
                valueSuffix: ''
            },
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
                name: 'Kepatuhan Pakai Masker',
                data: [response.kepatuhan_masker_hotel, response.kepatuhan_masker_sebud, response.kepatuhan_masker_resto, response.kepatuhan_masker_ibadah, response.kepatuhan_masker_publik, response.kepatuhan_masker_wisata, response.kepatuhan_masker_belanja, response.kepatuhan_masker_transport]
            }, {
                name: 'Kepatuhan Jaga Jarak',
                data: [response.kepatuhan_jaga_jarak_hotel, response.kepatuhan_jaga_jarak_sebud, response.kepatuhan_jaga_jarak_resto, response.kepatuhan_jaga_jarak_ibadah, response.kepatuhan_jaga_jarak_publik, response.kepatuhan_jaga_jarak_wisata, response.kepatuhan_jaga_jarak_belanja, response.kepatuhan_jaga_jarak_transport]
            }, {
                name: 'Kepatuhan Individu',
                data: [response.kepatuhan_hotel, response.kepatuhan_sebud, response.kepatuhan_resto, response.kepatuhan_ibadah, response.kepatuhan_publik, response.kepatuhan_wisata, response.kepatuhan_belanja, response.kepatuhan_transport]
            }]
        });
    })
    
</script>
<script type="text/javascript">
    var url = "{{ route('sebaran.prokes_individu_pie') }}";
    $.ajax({
        data: 'json',
        method: 'get',
        url: url,
        data: {
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