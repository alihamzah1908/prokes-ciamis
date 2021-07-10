@extends('home')
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
</style>
<div class="container">
    <div class="row">
        <div class="col-md-6 title-sebaran-peta-risiko">
            <?php
                $tanggal_pantau = \App\Models\ProkesInstitusi::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            ?>
            <h4><strong>Peta Kepatuhan Prokes Institusi COVID-19 di Kabupaten Ciamis</strong></h4>
            @php
            $tanggal_pantau = $tanggal_pantau->tanggal_pantau != '' ? date('d M Y', strtotime($tanggal_pantau->tanggal_pantau)) : 'Tidak ada data';
            @endphp
            <h5>Update Terakhir Data : {{ $tanggal_pantau }}</h5>
        </div>
        <div class="col-md-6 d-flex justify-content-end p-2">
            <form action='{{ route("peta.peta_institusi_desa") }}' method="get">
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
    <div class="row">
        <div class="col-md-8 mb-4">
            @if(Auth::user()->kode_kecamatan == '')
            <a href="{{ route('peta.peta_institusi') }}" class="btn mt-2 border mr-2" data-bind="institusi">
                <i class="fa fa-globe" aria-hidden="true"></i> <strong>BERANDA</strong>
            </a>
            @else
            <a href="{{ route('peta.peta_prokes_desa') }}?kecamatan={{request()->kecamatan}}&periode_kasus={{request()->periode_kasus}}&latitude={{request()->latitude}}&longitude={{request()->longitude}}" class="btn mr-2 border" data-bind="kasus">
                <i class="fa fa-male" aria-hidden="true"></i> <strong>PROKES INDIVIDU</strong>
            </a>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-8" id="sebaran-kasus">
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
        @if(Auth::user()->kode_kecamatan == '')
        <div class="col-md-4 pilih-kecamatan mt-3">
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
        @endif
        <input type="hidden" id="code_kecamatan" value="" />
        <input type="hidden" id="latitude" value="" />
        <input type="hidden" id="longitude" value="" />
    </div>
    <div class="row mt-4">
        <div class="col-md-6 mt-2" id="search_map">
            <div id="map-prokes"></div>
        </div>
        <div class="col-md-6">
            <canvas id="densityChart" width="500" height="600"></canvas>
            <canvas id="densityChart-cuci" width="500" height="600"></canvas>
            <canvas id="densityChart-sosialisasi" width="500" height="600"></canvas>
            <canvas id="densityChart-desinfeksi" width="500" height="600"></canvas>
            <canvas id="densityChart-suhu" width="500" height="600"></canvas>
            <canvas id="densityChart-petugas" width="500" height="600"></canvas>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<!-- <script src="{{ asset('js/peta_desa.js') }}"></script> -->
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
            'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
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
        }
    }).done(function(states) {
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
        window.location.href = '{{ route("peta.peta_institusi_desa") }}' + "?kecamatan=" + data.code_kecamatan + "&periode_kasus=" + "{{ request()->periode_kasus }}" + "&latitude=" + data.latitude + "&longitude=" + data.longitude + "";
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
            }
        }).done(function(states){
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
@endpush