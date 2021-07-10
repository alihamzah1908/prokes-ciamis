<?php

namespace App\Http\Controllers;

use App\Exports\ExportsDataProkesIndividu;
use App\Imports\ImportDataProkesIndividu;
use Auth;
use DB;
use Excel;
use DataTables;
use Illuminate\Http\Request;

class ProkesIndividuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role == 'Admin') {
            $data["prokes"] = \App\Models\Prokes::select('*',
                DB::raw('(CASE WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 61 THEN 1
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 76 THEN 2
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 91 THEN 3
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 <= 100 THEN 4
                END) as level_masker'),
                DB::raw('(
                    CASE WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 61 THEN 1
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 76 THEN 2
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 91 THEN 3
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 <= 100 THEN 4
                END) as level_jaga_jarak'))
                ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
                ->get();
        } else if (Auth::user()->role == 'Staff') {
            $data["prokes"] = \App\Models\Prokes::select('*',
                DB::raw('(CASE WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 61 THEN 1
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 76 THEN 2
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 91 THEN 3
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 <= 100 THEN 4
            END) as level_masker'),
                DB::raw('(
                CASE WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 61 THEN 1
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 76 THEN 2
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 91 THEN 3
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 <= 100 THEN 4
            END) as level_jaga_jarak'))
                ->where('created_by', Auth::user()->id)
                ->get();
        } else {
            $data["prokes"] = \App\Models\Prokes::all();
        }
        return view('admin.prokes_individu.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request["id"]) {
            $data = \App\Models\Prokes::findOrFail($request["id"]);
        } else {
            $data = new \App\Models\Prokes();
        }
        $data->kode_kecamatan = $request["kecamatan_id"];
        $data->kode_desa = $request["desa_id"];
        $data->kode_lokasi_pantau = $request["master_lokasi_pantau"] . ' - ' . $request["lokasi_pantau"];
        $data->jam_pantau = $request["jam_pantau"];
        $data->tanggal_pantau = date('Y-m-d', strtotime($request["tanggal_pantau"]));
        $data->selesai_jam_pantau = $request["selesai_jam_pantau"];
        $data->pakai_masker = $request["jumlah_pakai_masker"];
        $data->tidak_pakai_masker = $request["jumlah_tidak_pakai"];
        $data->jaga_jarak = $request["jumlah_jaga_jarak"];
        $data->tidak_jaga_jarak = $request["jumlah_tidak_jaga_jarak"];
        $data->created_by = Auth::user()->id;
        $data->save();
        return redirect(route('prokes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $prokes = \App\Models\Prokes::where('id', $request["id"])->first();
        if ($prokes) {
            $prokes->delete();
            return response()->json(["response" => 'success']);
        }
    }

    public function get_desa(Request $request)
    {
        $desa = \App\Models\Desa::where('kode_kecamatan', $request["code_kecamatan"])->get();
        return response()->json($desa);
    }

    public function get_sebaran_individu(Request $request)
    {
        $data = \App\Models\Kecamatan::orderBy('kecamatan')->get();
        $arry["type"] = "FeatureCollection";
        $arr = [];
        foreach ($data as $key => $val) {
            $tanggal_screaning = \App\Models\Prokes::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal = $request["periode_kasus"];
            if ($tanggal != '') {
                $var = $request["periode_kasus"];
                $date = str_replace('/', '-', $var);
                $periode_kasus = date('Y-m-d', strtotime($date));
            } else {
                $periode_kasus = $tanggal_screaning->tanggal_pantau;
            }
            $kepatuhan_prokes = \App\Models\Prokes::select('*')
                ->where('kode_kecamatan', $val->code_kecamatan)
                ->where('tanggal_pantau', $periode_kasus)
                ->get();
            $total_masker = $kepatuhan_prokes->pluck('pakai_masker')->sum() + $kepatuhan_prokes->pluck('tidak_pakai_masker')->sum();
            $kepatuhan_masker = ($kepatuhan_prokes->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
            $total_jarak = $kepatuhan_prokes->pluck('jaga_jarak')->sum() + $kepatuhan_prokes->pluck('tidak_jaga_jarak')->sum();
            $kepatuhan_jaga_jarak = ($kepatuhan_prokes->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
            switch ($request["sebaran_kasus"]) {
                case 'masker':
                    $total = $kepatuhan_masker;
                    break;
                case 'jarak':
                    $total = $kepatuhan_jaga_jarak;
                    break;
                case 'institusi':
                    $total = $total_kepatuhan_institusi;
                    break;
                default:
                    $total = ($kepatuhan_masker + $kepatuhan_jaga_jarak) / 2;
            }
            $arrx["type"] = "Feature";
            $arrx["id"] = "$val->id";
            $arrx["properties"] = [
                "name" => $val->kecamatan,
                "density" => round($total),
                "total_kasus" => 0,
                // "total_vaksin_2" => $peserta[0]->total_vaksin_2,
                // "kasus" => ucfirst($request["sebaran_kasus"]),
            ];
            $coordinate = \App\Models\Koordinat::select('koordinat')
                ->where('kode_kecamatan', $val->code_kecamatan)->get();
            $arrcoor = [];
            foreach ($coordinate as $key => $val) {
                $arrcoor[$key] = explode(', ', $val->koordinat);
            }
            $arrx["geometry"] = [
                "type" => "Polygon",
                "coordinates" => [
                    $arrcoor,
                ],
            ];
            $arr[] = $arrx;
        }
        $arry["features"] = $arr;
        return response()->json($arry);
    }

    public function get_sebaran_individu_desa(Request $request)
    {
        $data = \App\Models\Desa::where('kode_kecamatan', $request["code"])
            ->distinct()->select('kode_kelurahan', 'nama_kelurahan')
            ->orderBy('nama_kelurahan', 'asc')
            ->get();
        $arry["type"] = "FeatureCollection";
        $arr = [];
        foreach ($data as $key => $val) {
            $tanggal_screaning = \App\Models\Prokes::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal = $request["periode_kasus"];
            if ($tanggal != '') {
                $var = $request["periode_kasus"];
                $date = str_replace('/', '-', $var);
                $periode_kasus = date('Y-m-d', strtotime($date));
            } else {
                $periode_kasus = $tanggal_screaning->tanggal_pantau;
            }
            $kepatuhan_prokes = \App\Models\Prokes::select('*', DB::raw('(CASE WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 61 THEN 1
                        WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 76 THEN 2
                        WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 91 THEN 3
                        WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 <= 100 THEN 4
                    END) as level_masker'),
                DB::raw('(
                        CASE WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 61 THEN 1
                        WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 76 THEN 2
                        WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 91 THEN 3
                        WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 <= 100 THEN 4
                    END) as level_jaga_jarak'))
                ->where('kode_desa', $val->kode_kelurahan)
                ->where('tanggal_pantau', $periode_kasus)
                ->get();
            $total_masker = $kepatuhan_prokes->pluck('pakai_masker')->sum() + $kepatuhan_prokes->pluck('tidak_pakai_masker')->sum();
            $kepatuhan_masker = ($kepatuhan_prokes->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
            $total_jarak = $kepatuhan_prokes->pluck('jaga_jarak')->sum() + $kepatuhan_prokes->pluck('tidak_jaga_jarak')->sum();
            $kepatuhan_jaga_jarak = ($kepatuhan_prokes->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
            switch ($request["sebaran_kasus"]) {
                case 'masker':
                    $total = $kepatuhan_masker;
                    break;
                case 'jarak':
                    $total = $kepatuhan_jaga_jarak;
                    break;
                default:
                    $total = ($kepatuhan_masker + $kepatuhan_jaga_jarak) / 2;
            }
            $arrx["type"] = "Feature";
            $arrx["id"] = "$val->id";
            $arrx["properties"] = [
                "name" => $val->nama_kelurahan,
                "density" => round($total),
                "total_kasus" => 0,
                // "total_vaksin_2" => $peserta[0]->total_vaksin_2,
                // "kasus" => ucfirst($request["sebaran_kasus"]),
            ];
            $coordinate = \App\Models\KoordinatDesa::select('koordinat')
                ->where('kode_desa', $val->kode_kelurahan)->get();
            $arrcoor = [];
            foreach ($coordinate as $key => $val) {
                $arrcoor[$key] = explode(', ', $val->koordinat);
            }
            $arrx["geometry"] = [
                "type" => "Polygon",
                "coordinates" => [
                    $arrcoor,
                ],
            ];
            $arr[] = $arrx;
        }
        $arry["features"] = $arr;
        return response()->json($arry);
    }

    public function get_lokasi_pantau_individu(Request $request){
        $hotel = 'Hotel';
        $sebud = 'Kegiatan Seni Budaya';
        $belanja = 'Pusat Perbelanjaan';
        $publik = 'Area Publik';
        $resto = 'Restoran';
        $ibadah = 'Tempat Ibadah';
        $transport = 'Transportasi Umum';
        $wisata = 'Obyek Wisata';

        //kepatuhan hotal
        $hotel = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$hotel}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $hotel->pluck('pakai_masker')->sum() + $hotel->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $hotel->pluck('jaga_jarak')->sum() + $hotel->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_hotel"] = ($hotel->pluck('pakai_masker')->sum() != 0) ? ($hotel->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_hotel"] = ($hotel->pluck('jaga_jarak')->sum() != 0) ? ($hotel->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        //Kepatuhan seni budaya
        $sebud = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$sebud}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $sebud->pluck('pakai_masker')->sum() + $sebud->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $sebud->pluck('jaga_jarak')->sum() + $sebud->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_sebud"] = ($sebud->pluck('pakai_masker')->sum() != 0) ? ($sebud->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_sebud"] = ($sebud->pluck('jaga_jarak')->sum() != 0) ? ($sebud->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;


        $belanja = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$belanja}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $belanja->pluck('pakai_masker')->sum() + $belanja->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $belanja->pluck('jaga_jarak')->sum() + $belanja->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_belanja"] = ($belanja->pluck('pakai_masker')->sum() != 0) ? ($belanja->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_belanja"] = ($belanja->pluck('jaga_jarak')->sum() != 0) ? ($belanja->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;


        $publik = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$publik}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $publik->pluck('pakai_masker')->sum() + $publik->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $publik->pluck('jaga_jarak')->sum() + $publik->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_publik"] = ($publik->pluck('pakai_masker')->sum() != 0) ? ($publik->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_publik"] = ($publik->pluck('jaga_jarak')->sum() != 0) ? ($publik->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        $resto = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$resto}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $resto->pluck('pakai_masker')->sum() + $resto->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $resto->pluck('jaga_jarak')->sum() + $resto->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_resto"] = ($resto->pluck('pakai_masker')->sum() != 0) ? ($resto->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_resto"] = ($resto->pluck('jaga_jarak')->sum() != 0) ? ($resto->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        $transport = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$transport}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $transport->pluck('pakai_masker')->sum() + $transport->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $transport->pluck('jaga_jarak')->sum() + $transport->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_transport"] = ($transport->pluck('pakai_masker')->sum() != 0) ? ($transport->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_transport"] = ($transport->pluck('jaga_jarak')->sum() != 0) ? ($transport->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;

        $wisata = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$wisata}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $wisata->pluck('pakai_masker')->sum() + $wisata->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $wisata->pluck('jaga_jarak')->sum() + $wisata->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_wisata"] = ($wisata->pluck('pakai_masker')->sum() != 0) ? ($wisata->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_wisata"] = ($wisata->pluck('jaga_jarak')->sum() != 0) ? ($wisata->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;

        $ibadah = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$ibadah}%")->where('tanggal_pantau', $request["tanggal_pantau"])->get();
        $total_masker = $ibadah->pluck('pakai_masker')->sum() + $ibadah->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $ibadah->pluck('jaga_jarak')->sum() + $ibadah->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_ibadah"] = ($ibadah->pluck('pakai_masker')->sum() != 0) ? ($ibadah->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_ibadah"] = ($ibadah->pluck('jaga_jarak')->sum() != 0) ? ($ibadah->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        return response()->json($data);
    }

    public function get_lokasi_pantau_individu_desa(Request $request){
        $hotel = 'Hotel';
        $sebud = 'Kegiatan Seni Budaya';
        $belanja = 'Pusat Perbelanjaan';
        $publik = 'Area Publik';
        $resto = 'Restoran';
        $ibadah = 'Tempat Ibadah';
        $transport = 'Transportasi Umum';
        $wisata = 'Obyek Wisata';

        //kepatuhan hotal
        $hotel = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$hotel}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $hotel->pluck('pakai_masker')->sum() + $hotel->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $hotel->pluck('jaga_jarak')->sum() + $hotel->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_hotel"] = ($hotel->pluck('pakai_masker')->sum() != 0) ? ($hotel->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_hotel"] = ($hotel->pluck('jaga_jarak')->sum() != 0) ? ($hotel->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        //Kepatuhan seni budaya
        $sebud = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$sebud}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $sebud->pluck('pakai_masker')->sum() + $sebud->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $sebud->pluck('jaga_jarak')->sum() + $sebud->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_sebud"] = ($sebud->pluck('pakai_masker')->sum() != 0) ? ($sebud->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_sebud"] = ($sebud->pluck('jaga_jarak')->sum() != 0) ? ($sebud->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;


        $belanja = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$belanja}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $belanja->pluck('pakai_masker')->sum() + $belanja->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $belanja->pluck('jaga_jarak')->sum() + $belanja->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_belanja"] = ($belanja->pluck('pakai_masker')->sum() != 0) ? ($belanja->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_belanja"] = ($belanja->pluck('jaga_jarak')->sum() != 0) ? ($belanja->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;


        $publik = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$publik}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $publik->pluck('pakai_masker')->sum() + $publik->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $publik->pluck('jaga_jarak')->sum() + $publik->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_publik"] = ($publik->pluck('pakai_masker')->sum() != 0) ? ($publik->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_publik"] = ($publik->pluck('jaga_jarak')->sum() != 0) ? ($publik->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        $resto = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$resto}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $resto->pluck('pakai_masker')->sum() + $resto->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $resto->pluck('jaga_jarak')->sum() + $resto->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_resto"] = ($resto->pluck('pakai_masker')->sum() != 0) ? ($resto->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_resto"] = ($resto->pluck('jaga_jarak')->sum() != 0) ? ($resto->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        
        $transport = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$transport}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $transport->pluck('pakai_masker')->sum() + $transport->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $transport->pluck('jaga_jarak')->sum() + $transport->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_transport"] = ($transport->pluck('pakai_masker')->sum() != 0) ? ($transport->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_transport"] = ($transport->pluck('jaga_jarak')->sum() != 0) ? ($transport->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;

        $wisata = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$wisata}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $wisata->pluck('pakai_masker')->sum() + $wisata->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $wisata->pluck('jaga_jarak')->sum() + $wisata->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_wisata"] = ($wisata->pluck('pakai_masker')->sum() != 0) ? ($wisata->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_wisata"] = ($wisata->pluck('jaga_jarak')->sum() != 0) ? ($wisata->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;

        $ibadah = \App\Models\Prokes::where('kode_lokasi_pantau', 'LIKE', "%{$ibadah}%")->where('tanggal_pantau', $request["tanggal_pantau"])->where('kode_kecamatan', $request["kode_kecamatan"])->get();
        $total_masker = $ibadah->pluck('pakai_masker')->sum() + $ibadah->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $ibadah->pluck('jaga_jarak')->sum() + $ibadah->pluck('tidak_jaga_jarak')->sum();
        $data["kepatuhan_masker_ibadah"] = ($ibadah->pluck('pakai_masker')->sum() != 0) ? ($ibadah->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $data["kepatuhan_jaga_jarak_ibadah"] = ($ibadah->pluck('jaga_jarak')->sum() != 0) ? ($ibadah->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        return response()->json($data);
    }

    public function download_template(Request $request)
    {
        return Excel::download(new ExportsDataProkesIndividu($request["kode"]), 'template.xlsx');
    }

    public function import_prokes(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file'); //GET FILE
            Excel::import(new ImportDataProkesIndividu, $file); //IMPORT FILE
            return redirect()->back()->with(['success' => 'Upload success']);
        }
        return redirect()->back()->with(['error' => 'Please choose file before']);
    }

    public function datatable_individu(Request $request){
        if (Auth::user()->role == 'Admin') {
            $data = \App\Models\Prokes::select('*',
                DB::raw('(CASE WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 61 THEN 1
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 76 THEN 2
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 91 THEN 3
                    WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 <= 100 THEN 4
                END) as level_masker'),
                DB::raw('(
                    CASE WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 61 THEN 1
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 76 THEN 2
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 91 THEN 3
                    WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 <= 100 THEN 4
                END) as level_jaga_jarak'))
                ->where('kode_kecamatan', Auth::user()->kode_kecamatan)
                ->get();
        } else if (Auth::user()->role == 'Staff') {
            $data = \App\Models\Prokes::select('*',
                DB::raw('(CASE WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 61 THEN 1
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 76 THEN 2
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 < 91 THEN 3
                WHEN pakai_masker/(pakai_masker+tidak_pakai_masker)*100 <= 100 THEN 4
            END) as level_masker'),
                DB::raw('(
                CASE WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 61 THEN 1
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 76 THEN 2
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 < 91 THEN 3
                WHEN jaga_jarak/(jaga_jarak+tidak_jaga_jarak)*100 <= 100 THEN 4
            END) as level_jaga_jarak'))
                ->where('created_by', Auth::user()->id)
                ->get();
        } else {
            $data = \App\Models\Prokes::all();
        }
        return Datatables::of($data)
        ->addColumn('nama_user', function($val){
            return $val->get_user ? $val->get_user->name : '';
        })
        ->addColumn('kelurahan', function($val){
            return $val->get_desa ? $val->get_desa->nama_kelurahan : '';
        })
        ->addColumn('kecamatan', function($val){
            return $val->get_kecamatan ? $val->get_kecamatan->kecamatan : '';
        })
        ->addColumn('tanggal_pantau', function($val){
            return date('d M Y', strtotime($val->tanggal_pantau));
        })
        ->addColumn('mulai_jam_pantau', function($val){
            return date('H:i:s', strtotime($val->jam_pantau));
        })
        ->addColumn('selesai_jam_pantau', function($val){
            return date('H:i:s', strtotime($val->selesai_jam_pantau));
        })
        ->addColumn('total_masker', function($val){
            return $val->pakai_masker + $val->tidak_pakai_masker;
        })
        ->addColumn('kepatuhan_prokes', function($val){
            // $pakai_masker = $val->pakai_masker == 0 ? null : $val->pakai_masker;
            // $tidak_pakai_masker = $val->tidak_pakai_masker == 0 ? null : $val->tidak_pakai_masker;
            $jmlh_pakai_masker = $val->pakai_masker + $val->tidak_pakai_masker;
            if($jmlh_pakai_masker != 0){
                $iii = ($val->pakai_masker / ($val->pakai_masker + $val->tidak_pakai_masker)) * 100;
            }else{
                $iii = 0;
            }
            return round($iii) . ' ' . '%';
        })
        ->addColumn('total_jaga_jarak', function($val){
            return $val->jaga_jarak + $val->tidak_jaga_jarak ;
        })
        ->addColumn('kepatuhan_jaga_jarak', function($val){
            // $jaga_jarak = $val->jaga_jarak == 0 ? null : $val->jaga_jarak;
            // $tidak_jaga_jarak = $val->tidak_jaga_jarak == 0 ? null : $val->tidak_jaga_jarak;
            $jmlh_jaga_jarak = $val->jaga_jarak + $val->tidak_jaga_jarak;
            if($jmlh_jaga_jarak != 0){
                $iii = ($val->jaga_jarak / ($val->jaga_jarak + $val->tidak_jaga_jarak))* 100;
            }else{
                $iii = 0;
            }
            return round($iii) .' '. '%';
        })
        ->addColumn('level_masker', function($val){
            return $val ? $val->level_masker : '';
        })
        ->addColumn('level_jaga_jarak', function($val){
            return $val ? $val->level_jaga_jarak : '';
        })
        ->addColumn('created_at', function($val){
            return date('d M Y', strtotime($val->created_at));
        })
        ->addColumn('aksi', function ($val) {
            return '<div class="dropdown">
                            <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">Aksi</button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item edit" data-bind=\'' . $val . '\' role="presentation" href="javascript:void(0)" data-toggle="modal">Edit</a>
                                <a class="dropdown-item delete" data-bind="' . $val->id . '" role="presentation" href="javascript:void(0)">Hapus</a>
                            </div>
                        </div>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }
}