<html>
    <head>
        <title>LAPORAN PROKES</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style>
            body{
                font-family: Arial;
            }
            #container{
                width: 1000px;
            }
            @media print {
                .pagebreak { page-break-before: always; } /* page-break-after works, as well */
            }
        </style>
    </head>
    <body>
        @foreach($result as $val)
        @php 
        // Total kepatuhan prokes
        $masker = $val->pakai_masker + $val->tidak_pakai_masker;
        $kepatuhan_masker = ($val->pakai_masker != 0) ? ($val->pakai_masker / $masker) * 100 : 0;
        $institusi = $val->fasilitas_cuci_tangan + $val->sosialisasi_prokes + $val->cek_suhu_tubuh + $val->petugas_pengawas_prokes + $val->desinfeksi_berkala;
        if($kepatuhan_masker != 0 && $institusi != 0){
            $kepatuhan_prokes = $kepatuhan_masker + $institusi / 2;
        }else if($kepatuhan_masker != 0 || $institusi == 0){
            $kepatuhan_prokes = $kepatuhan_masker;
        }else if($kepatuhan_masker == 0 || $institusi != 0){
            $kepatuhan_prokes = $institusi;
        }
        @endphp
        <div class="row mt-3">
            <div class="col-md-12">
                <h1><center>Summary Report</center></h1>
                @if(request()->kecamatan != '')
                    <h3><center>Prokes Monitoring Kecamatan {{ $val->kecamatan }} </center></h3>
                @else 
                    <h3><center>Prokes Monitoring Kabupaten Ciamis </center></h3>
                @endif
                <p><center>Tanggal {{ date('d M Y', strtotime(request()->startDate))}} s/d {{ date('d M Y', strtotime(request()->endDate))}}</center></p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12" style="margin-left: 70px">
                <h5>Tingkat Kepatuhan Prokes Tanggal {{ date('d M Y', strtotime(request()->startDate))}} s/d {{ date('d M Y', strtotime(request()->endDate))}}</h5>
                <p>Laporan menunjukkan tingkat kepatuhan <b>{{ $val->jenis_kepatuhan }}</b> sampel yang diamati adalah {{ round($kepatuhan_prokes) }} %. </p>
            </div>
        </div>
        @if($val->jenis_kepatuhan == 'Institusi')
            <div class="row d-flex justify-content-center">
                <div class='col-md-7'>
                    <figure class="highcharts-figure">
                        <div id="container-{{$val->id}}"></div>
                    </figure>
                </div>
            </div>
        @else 
            <div class="row d-flex justify-content-center">
                <div class='col-md-7'>
                    <figure class="highcharts-figure">
                        <div id="individu-{{$val->id}}"></div>
                    </figure>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-10" style="margin-left: 70px;margin-right:70px;">
                <div class="table-responsive">          
                    <table class="table table-hover table-striped" id="example">
                        <thead>
                            <tr>
                                <th>Unsur Laporan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jumlah desa/kel yang melaporkan data</td>
                                @php 
                                if(request()->kecamatan == ''){
                                    $desa_individu = DB::table('kepatuhan_prokes as a')
                                        ->join('desa_master as b','a.kode_desa','b.kode_kelurahan')
                                        ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
                                        ->groupBy('a.kode_kecamatan','a.kode_desa')
                                        ->get();
                                    $desa_institusi = DB::table('prokes_institusi as a')
                                        ->join('desa_master as b','a.desa_id','b.kode_kelurahan')
                                        ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
                                        ->groupBy('a.kecamatan_id','a.desa_id')
                                        ->get();
                                }else{
                                    $desa_individu = DB::table('kepatuhan_prokes as a')
                                        ->join('desa_master as b','a.kode_desa','b.kode_kelurahan')
                                        ->where('a.kode_kecamatan', request()->kecamatan)
                                        ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
                                        ->groupBy('a.kode_kecamatan','a.kode_desa')
                                        ->get();
                                    $desa_institusi = DB::table('prokes_institusi as a')
                                        ->join('desa_master as b','a.desa_id','b.kode_kelurahan')
                                        ->where('a.kecamatan_id', request()->kecamatan)
                                        ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
                                        ->groupBy('a.kecamatan_id','a.desa_id')
                                        ->get();
                                }
                                @endphp
                                @if($val->jenis_kepatuhan == 'Institusi')
                                <td> {{ $desa_institusi->count() }} </td>
                                @else 
                                <td> {{ $desa_individu->count() }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td>Nama desa yang melaporkan data</td>
                                @if($val->jenis_kepatuhan == 'Institusi')
                                    <td>@foreach($desa_institusi as $key) {{ $key->nama_kelurahan }} , @endforeach</td>
                                @else 
                                    <td>@foreach($desa_individu as $key) {{ $key->nama_kelurahan }} , @endforeach</td>
                                @endif
                            </tr>   
                            <tr>
                                <td>Lokasi pantau</td>
                                @if($val->jenis_kepatuhan == 'Institusi')
                                    <td>@foreach($desa_institusi as $key) {{ $key->lokasi_pantau }} , @endforeach</td>
                                @else 
                                    <td>@foreach($desa_individu as $key) {{ $key->kode_lokasi_pantau }} , @endforeach</td>
                                @endif
                            </tr> 
                            @if($val->jenis_kepatuhan == 'Individu')
                            <tr>
                                <td>Jumlah laporan data prokes individu </td>
                                @if($val->jenis_kepatuhan == 'Individu')
                                    <td>{{ $desa_individu->count() }}</td>
                                @else 
                                    <td>0</td>
                                @endif
                            </tr>   
                            @endif
                            @if($val->jenis_kepatuhan == 'Institusi')
                            <tr>
                                <td>Jumlah laporan data prokes institusi </td>
                                @if($val->jenis_kepatuhan == 'Institusi')
                                    <td>{{ $desa_institusi->count() }}</td>
                                @else 
                                    <td>0</td>
                                @endif
                            </tr> 
                            @endif
                            <!-- <tr>
                                <td>Jumlah laporan prokes individu dan institusi  </td>
                                <td>{{ $desa_individu->count() + $desa_institusi->count() }}</td>
                            </tr>  -->
                            @if($val->jenis_kepatuhan == 'Individu')
                            <tr>
                                <td>Jumlah sampel pakai masker  </td>
                                <td>{{ $desa_individu->sum('pakai_masker') }} Orang</td>
                            </tr> 
                            <tr>
                                <td>Jumlah sampel jaga jarak </td>
                                <td>{{ $desa_individu->sum('tidak_pakai_masker') }} Orang</td>
                            </tr>   
                            @endif
                            <tr>
                                @if($val->jenis_kepatuhan == 'Institusi')
                                    <td>Jumlah titik pantau prokes institusi  </td>
                                    <td>{{ $desa_institusi->count('lokasi_pantau') }} Lokasi</td>
                                @else 
                                    <td>Jumlah titik pantau prokes individu  </td>
                                    <td>{{ $desa_individu->count('lokasi_pantau') }} Lokasi</td>
                                @endif
                            </tr>   
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <p class="pageBreak"></p>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="{{ asset('asset_admin/js/jquery-3.4.1.min.js') }}"></script>
        <script>
            $.ajax({
                url : '{{ route("get.data_institusi") }}',
                dataType: 'json',
                method: 'get',
                data: {
                    'startDate' : '{{ request()->startDate }}',
                    'endDate' : '{{ request()->endDate }}',
                    'kecamatan': '{{ request()->kecamatan }}'
                },
            }).done(function(response){
                var tanggal_pantau = []
                var total_kepatuhan = []
                $.each(response, function(index, value){
                    tanggal_pantau.push(value.tanggal_pantau)
                    total_kepatuhan.push(value.total_kepatuhan)
                })
                var id = "{{ $val->id }}"
                Highcharts.chart('container-'+ id, {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Laporan Kepatuhan Prokes Institusi'
                    },
                    xAxis: {
                        categories: tanggal_pantau
                    },
                    yAxis: {
                        title: {
                            text: 'Kepatuhan (%)'
                        },
                    },
                    tooltip: {
                        crosshairs: true,
                        shared: true
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                radius: 4,
                                lineColor: '#666666',
                                lineWidth: 1
                            }
                        }
                    },
                    series: [ {
                        name: 'Kepatuhan Institusi',
                        marker: {
                            symbol: 'diamond'
                        },
                        data: total_kepatuhan
                    }]
                });
            })
        </script>
        <script>
            $.ajax({
                url : '{{ route("get.data_individu") }}',
                dataType: 'json',
                method: 'get',
                data: {
                    'startDate' : '{{ request()->startDate }}',
                    'endDate' : '{{ request()->endDate }}',
                    'kecamatan': '{{ request()->kecamatan }}'
                }
            }).done(function(response){
                var tanggal_pantau = []
                var total_kepatuhan_individu = []
                $.each(response, function(index, value){
                    tanggal_pantau.push(value.tanggal_pantau)
                    total_kepatuhan_individu.push(value.total_kepatuhan)
                })
                var id = "{{ $val->id }}"
                Highcharts.chart('individu-'+ id, {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Laporan Kepatuhan Prokes Individu'
                    },
                    xAxis: {
                        categories: tanggal_pantau
                    },
                    yAxis: {
                        title: {
                            text: 'Kepatuhan (%)'
                        },
                    },
                    tooltip: {
                        crosshairs: true,
                        shared: true
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                radius: 4,
                                lineColor: '#666666',
                                lineWidth: 1
                            }
                        }
                    },
                    series: [{
                        name: 'Kepatuhan Individu',
                        marker: {
                            symbol: 'square'
                        },
                        data: total_kepatuhan_individu
                    }]
                });
            })
        </script>
        @endforeach
    </body>
</html>