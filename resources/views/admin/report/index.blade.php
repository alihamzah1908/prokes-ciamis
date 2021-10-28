@extends('newmaster')
@section('content')
<form action="{{ route('report.index') }}" method="get">
    <div class="row">
        <div class="col-md-2">
            <input type="text" class="form-control" id="startDate" name="startDate" placeholder="mulai periode" value="{{ request()->startDate }}"/>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" id="endDate" name="endDate" placeholder="akhir periode" value="{{ request()->endDate }}"/>
        </div>
        <div class="col-md-2">
            @php
            $kecamatan = App\Models\Kecamatan::orderBy('kecamatan', 'asc')->get();
            @endphp
            <select name="kecamatan" id="kecamatan" class="form-control">
                <option value="">Pilih Kecamatan</option>
                @foreach($kecamatan as $val)
                    <option value="{{ $val->code_kecamatan }}" {{ $val->code_kecamatan == request()->kecamatan ? ' selected' : '' }}>{{ $val->kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">cari</button>
        </div>
    </div>
</form>
<div class="row mt-4">
    <div class="col-md-4">
        @php
        if(request()->startDate != '' || request()->endDate != '' || request()->kecamatan){
            $startDate = request()->startDate;
            $endDate = request()->endDate;
            $kecamatan = request()->kecamatan;
        }else{
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
            $kecamatan = '';
        }
        @endphp
        <button class="btn btn-success btn-sm mb-4 download-template" data-start="{{ $startDate }}" data-end="{{ $endDate }}" data-kecamatan="{{ $kecamatan }}">
            <div id="text-button-voucher" class="text-button">
                <i class="fa fa-download" aria-hidden="true"></i> Download Excel
            </div>
            <div id="loading-wrap-voucher" class="text-center loading-wrap" style="display: none;">
                <div class="spinner-border text-light" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </button>
        <a href="{{ route('blank.page') }}?startDate={{ $startDate }}&endDate={{ $endDate }}&kecamatan={{ $kecamatan }}" target="_blank">
            <button class="btn btn-primary btn-sm mb-4">
                <div id="text-button-voucher" class="text-button">
                    <i class="fa fa-search" aria-hidden="true"></i> Lihat Laporan
                </div>
            </button>
        </a>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-6">
        @if(request()->startDate != '' || request()->endDate != '')
        <span class="font-weight-bold">Laporan kepatuhan prokes mulai tanggal {{ date('d M Y', strtotime(request()->startDate)) }} sampai tanggal {{ date('d M Y', strtotime(request()->endDate)) }}
        @else
        <span class="font-weight-bold">Laporan kepatuhan prokes mulai tanggal {{ date('01 M Y') }} sampai tanggal {{ date('d M Y') }}
        @endif
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="example">
        <thead>
            <tr>
                <th>Kecamatan</th>
                <th>Desa/Kelurahan</th>
                <th>Tanggal Pantau</th>
                <th>Jenis Kepatuhan</th>
                <th>Kepatuhan Individu</th>
                <th>Kepatuhan Institusi</th>
                <th>Kepatuhan Prokes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $val)
            @php
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
            <tr>
                <td>{{ $val->kecamatan }}</td>
                <td>{{ $val->nama_kelurahan }}</td>
                <td>{{ date('d M Y', strtotime($val->tanggal_pantau)) }}</td>
                <td>{{ $val->jenis_kepatuhan }}</td>
                <td>{{ round($kepatuhan_masker) }} %</td>
                <td>{{ round($institusi) }} %</td>
                <td>{{ round($kepatuhan_prokes) }} %</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#example').DataTable({})
        $("#startDate").datepicker({
            dateFormat: 'yy-mm-dd',
        });
        $("#endDate").datepicker({ dateFormat: 'yy-mm-dd' });
        $('body').on('click','.download-template', function(){
            var url = '{{ route("download.report") }}';
            $.ajax({
                url: url,
                method: 'get',
                xhrFields: {
                    responseType: 'blob'
                },
                data : {
                    'startDate': $(this).attr('data-start'),
                    'endDate' : $(this).attr('data-end'),
                    'kecamatan' : $(this).attr('data-kecamatan')
                },
                success: function(data){
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = 'laporan.xlsx';
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
    })
</script>
@endpush
