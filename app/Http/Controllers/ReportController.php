<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use DB;
use Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request["startDate"];
        $end = $request["endDate"];
        if ($start != '' || $end != '' || $request["kecamatan"] != '') {
            $var1 = $request["startDate"];
            $var2 = $request["endDate"];
            $date1 = str_replace('/', '-', $var1);
            $date2 = str_replace('/', '-', $var2);
            $startDate = date('Y-m-d', strtotime($date1));
            $endDate = date('Y-m-d', strtotime($date2));
            $kecamatan = $request["kecamatan"];
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
            $kecamatan = '';
        }
        $individu = DB::table('kepatuhan_prokes as a')
            ->select(['a.tanggal_pantau', 'a.kode_kecamatan', 'b.kecamatan', 'c.nama_kelurahan', 'a.kode_lokasi_pantau as lokasi_pantau', DB::raw('SUM(a.pakai_masker) as pakai_masker'),
                DB::raw('SUM(a.tidak_pakai_masker) as tidak_pakai_masker'), DB::raw('SUM(a.jaga_jarak) as jaga_jarak'),
                DB::raw('SUM(a.tidak_jaga_jarak) as tidak_jaga_jarak'), DB::raw("'Individu' as jenis_kepatuhan"),
                DB::raw("'0' as fasilitas_cuci_tangan, '0' as sosialisasi_prokes, '0' as cek_suhu_tubuh, '0' as petugas_pengawas_prokes, '0' as desinfeksi_berkala")])
            ->join('kecamatan as b', 'a.kode_kecamatan', 'b.code_kecamatan')
            ->join('desa_master as c', 'a.kode_desa', 'c.kode_kelurahan')
            ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
            ->groupBy('a.kode_kecamatan', 'a.tanggal_pantau');
        $institusi = DB::table('prokes_institusi as a')
            ->select(['a.tanggal_pantau', 'a.kecamatan_id as kode_kecamatan', 'b.kecamatan', 'c.nama_kelurahan', 'a.lokasi_pantau as lokasi_pantau', DB::raw("'0' as pakai_masker, '0' as tidak_pakai_masker,
                            '0' as jaga_jarak, '0' as tidak_jaga_jarak, 'Institusi' as jenis_kepatuhan"), DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
            ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
            ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
            ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
            ->groupBy('a.kecamatan_id', 'a.tanggal_pantau')
            ->union($individu)
            ->get();
        // dd($result);
        if ($kecamatan != '') {
            $result = $institusi->where('kode_kecamatan', $kecamatan);
        } else {
            $result = $institusi;
        }
        $data["result"] = $result;
        return view('admin/report.index', $data);
    }

    public function blank_page(Request $request)
    {
        $start = $request["startDate"];
        $end = $request["endDate"];
        if ($start != '' || $end != '' || $request["kecamatan"] != '') {
            $var1 = $request["startDate"];
            $var2 = $request["endDate"];
            $date1 = str_replace('/', '-', $var1);
            $date2 = str_replace('/', '-', $var2);
            $startDate = date('Y-m-d', strtotime($date1));
            $endDate = date('Y-m-d', strtotime($date2));
            $kecamatan = $request["kecamatan"];
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
            $kecamatan = '';
        }
        if ($kecamatan == '') {
            $individu = DB::table('kepatuhan_prokes as a')
                ->select(['a.id', 'a.kode_kecamatan', 'a.tanggal_pantau', 'b.kecamatan', DB::raw('SUM(a.pakai_masker) as pakai_masker'),
                    DB::raw('SUM(a.tidak_pakai_masker) as tidak_pakai_masker'), DB::raw('SUM(a.jaga_jarak) as jaga_jarak'),
                    DB::raw('SUM(a.tidak_jaga_jarak) as tidak_jaga_jarak'), DB::raw("'Individu' as jenis_kepatuhan"),
                    DB::raw("'0' as fasilitas_cuci_tangan, '0' as sosialisasi_prokes, '0' as cek_suhu_tubuh, '0' as petugas_pengawas_prokes, '0' as desinfeksi_berkala")])
                ->join('kecamatan as b', 'a.kode_kecamatan', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.kode_desa', 'c.kode_kelurahan')
                ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
                ->groupBy('jenis_kepatuhan');
            $institusi = DB::table('prokes_institusi as a')
                ->select(['a.id', 'a.kecamatan_id', 'a.tanggal_pantau', 'b.kecamatan', DB::raw("'0' as pakai_masker, '0' as tidak_pakai_masker,
                '0' as jaga_jarak, '0' as tidak_jaga_jarak, 'Institusi' as jenis_kepatuhan"), DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                    DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
                ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
                ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
                ->groupBy('jenis_kepatuhan')
                ->union($individu)
                ->get();
        } else {
            $individu = DB::table('kepatuhan_prokes as a')
                ->select(['a.id', 'a.kode_kecamatan', 'a.tanggal_pantau', 'b.kecamatan', DB::raw('SUM(a.pakai_masker) as pakai_masker'),
                    DB::raw('SUM(a.tidak_pakai_masker) as tidak_pakai_masker'), DB::raw('SUM(a.jaga_jarak) as jaga_jarak'),
                    DB::raw('SUM(a.tidak_jaga_jarak) as tidak_jaga_jarak'), DB::raw("'Individu' as jenis_kepatuhan"),
                    DB::raw("'0' as fasilitas_cuci_tangan, '0' as sosialisasi_prokes, '0' as cek_suhu_tubuh, '0' as petugas_pengawas_prokes, '0' as desinfeksi_berkala")])
                ->join('kecamatan as b', 'a.kode_kecamatan', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.kode_desa', 'c.kode_kelurahan')
                ->where('a.kode_kecamatan', $kecamatan)
                ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
                ->groupBy('a.kode_kecamatan');
            $institusi = DB::table('prokes_institusi as a')
                ->select(['a.id', 'a.kecamatan_id', 'a.tanggal_pantau', 'b.kecamatan', DB::raw("'0' as pakai_masker, '0' as tidak_pakai_masker,
                '0' as jaga_jarak, '0' as tidak_jaga_jarak, 'Institusi' as jenis_kepatuhan"), DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                    DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
                ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
                ->where('a.kecamatan_id', $kecamatan)
                ->whereBetween('a.tanggal_pantau', [$startDate, $endDate])
                ->groupBy('a.kecamatan_id')
                ->union($individu)
                ->get();
        }
        $result = $institusi;
        $data["result"] = $result;
        return view('admin/report.blank_page', $data);
    }

    public function chart_show_institusi(Request $result)
    {
        $desa_institusi = DB::table('prokes_institusi as a')
            ->join('desa_master as b', 'a.desa_id', 'b.kode_kelurahan')
            ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
            ->groupBy('a.kecamatan_id', 'a.desa_id')
            ->get();
        if (request()->kecamatan != '') {
            $result = $desa_institusi->where('kecamatan_id', request()->kecamatan);
        } else {
            $result = $desa_institusi;
        }
        $arr = [];
        foreach ($result as $key => $val) {
            $arrx["tanggal_pantau"] = date('d-M-Y', strtotime($val->tanggal_pantau)) . ' (' . $val->nama_kelurahan . ' )';
            $arrx["total_kepatuhan"] = round($val->fasilitas_cuci_tangan + $val->sosialisasi_prokes + $val->cek_suhu_tubuh + $val->petugas_pengawas_prokes + $val->desinfeksi_berkala);
            $arr[] = $arrx;
        }
        return response()->json($arr);
    }
    public function chart_show_individu(Request $result)
    {
        $desa_individu = DB::table('kepatuhan_prokes as a')
            ->join('desa_master as b', 'a.kode_desa', 'b.kode_kelurahan')
            ->whereBetween('a.tanggal_pantau', [request()->startDate, request()->endDate])
            ->groupBy('a.kode_kecamatan', 'a.kode_desa')
            ->get();
        if (request()->kecamatan != '') {
            $result = $desa_individu->where('kecamatan_id', request()->kecamatan);
        } else {
            $result = $desa_individu;
        }
        $arr = [];
        foreach ($result as $key => $val) {
            $masker = $val->pakai_masker + $val->tidak_pakai_masker;
            $kepatuhan_individu = ($val->pakai_masker != 0) ? ($val->pakai_masker / $masker) * 100 : 0;
            $arrx["tanggal_pantau"] = date('d-M-Y', strtotime($val->tanggal_pantau)) . ' (' . $val->nama_kelurahan . ' )';
            $arrx["total_kepatuhan"] = round($kepatuhan_individu);
            $arr[] = $arrx;
        }
        return response()->json($arr);
    }

    public function download_report(Request $request)
    {
        return Excel::download(new ReportExport($request["startDate"], $request["endDate"], $request["kecamatan"]), 'template.xlsx');
    }
}
