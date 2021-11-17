<?php

namespace App\Http\Controllers;

use App\Exports\ExportsDataProkesInstitusi;
use App\Imports\ImportDataProkesInstitusi;
use Auth;
use DB;
use DataTables;
use Excel;
use Illuminate\Http\Request;

class ProkesInstitusiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.prokes_institusi.index');
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
            $data = \App\Models\ProkesInstitusi::find($request["id"]);
        } else {
            $data = new \App\Models\ProkesInstitusi();
        }
        $data->kecamatan_id = $request["kecamatan_id"];
        $data->desa_id = $request["desa_id"];
        $data->lokasi_pantau = $request["master_lokasi_pantau"] . ' - ' . $request["lokasi_pantau"];
        $data->tanggal_pantau = date('Y-m-d', strtotime($request["tanggal_pantau"]));
        $data->jam_pantau = $request["jam_pantau"];
        $data->selesai_jam_pantau = $request["selesai_jam_pantau"];
        $data->fasilitas_cuci_tangan = $request["cuci_tangan"];
        $data->sosialisasi_prokes = $request["sosialisasi_prokes"];
        $data->cek_suhu_tubuh = $request["suhu_tubuh"];
        $data->petugas_pengawas_prokes = $request["pengawas_prokes"];
        $data->desinfeksi_berkala = $request["desinfeksi_berkala"];
        $data->created_by = Auth::user()->id;
        $data->save();
        if ($request->hasfile('image')) {
            foreach ($request->file('image') as $key => $file) {
                $name = $key . '-' . time() . '.' . $file->extension();
                $file->move(public_path() . '/dokumen_institusi/', $name);
                $file = new \App\Models\DokumenInstitusi();
                $file->institusi_id = $data->id;
                $file->kode_kecamatan = $request["kecamatan_id"];
                $file->kode_desa = $request["desa_id"];
                $file->lokasi_pantau = $request["master_lokasi_pantau"] . '-' . $request["lokasi_pantau"];
                $file->tanggal_pantau = date('Y-m-d', strtotime($request["tanggal_pantau"]));
                $file->deskripsi_image = $request["deskripsi_dokumen"];
                $file->image = $name;
                $file->save();
            }
        }
        return redirect(route('institusi.index'));
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
        $prokes = \App\Models\ProkesInstitusi::where('id', $request["id"])->first();
        if ($prokes) {
            $prokes->delete();
            return response()->json(["response" => 'success']);
        }
    }

    public function get_sebaran_institusi(Request $request)
    {
        $data = \App\Models\Kecamatan::orderBy('kecamatan')->get();
        $arry["type"] = "FeatureCollection";
        $arr = [];
        foreach ($data as $key => $val) {
            $tanggal_screaning = \App\Models\ProkesInstitusi::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal = $request["periode_kasus"];
            if ($tanggal != '') {
                $var = $request["periode_kasus"];
                $date = str_replace('/', '-', $var);
                $periode_kasus = date('Y-m-d', strtotime($date));
            } else {
                $periode_kasus = $tanggal_screaning->tanggal_pantau;
            }
            $kepatuhan_prokes = \App\Models\ProkesInstitusi::select('*')
                ->where('kecamatan_id', $val->code_kecamatan)
                ->where('tanggal_pantau', $periode_kasus)
                ->get();
            $total_prokes = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() + $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() + $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() + $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() + $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg();
            switch ($request["sebaran_kasus"]) {
                case 'cuci':
                    $total = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() * 5;
                    break;
                case 'sosialisasi':
                    $total = $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() * 5;
                    break;
                case 'suhu':
                    $total = $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() * 5;
                    break;
                case 'petugas':
                    $total = $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() * 5;
                    break;
                case 'desinfeksi':
                    $total = $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg() * 5;
                    break;
                default:
                    $total = $total_prokes;
            }
            $arrx["type"] = "Feature";
            $arrx["id"] = "$val->id";
            $arrx["properties"] = [
                "name" => $val->kecamatan,
                "density" => round($total),
                "y" => round($total),
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

    public function get_sebaran_institusi_desa(Request $request)
    {
        $data = \App\Models\Desa::where('kode_kecamatan', $request["code"])
            ->distinct()->select('kode_kelurahan', 'nama_kelurahan')
            ->orderBy('nama_kelurahan', 'asc')
            ->get();
        $arry["type"] = "FeatureCollection";
        $arr = [];
        foreach ($data as $key => $val) {
            $tanggal_screaning = \App\Models\ProkesInstitusi::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal = $request["periode_kasus"];
            if ($tanggal != '') {
                $var = $request["periode_kasus"];
                $date = str_replace('/', '-', $var);
                $periode_kasus = date('Y-m-d', strtotime($date));
            } else {
                $periode_kasus = $tanggal_screaning->tanggal_pantau;
            }
            $kepatuhan_prokes = \App\Models\ProkesInstitusi::select('*')
                ->where('desa_id', $val->kode_kelurahan)
                ->where('tanggal_pantau', $periode_kasus)
                ->get();
            $total_prokes = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() + $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() + $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() + $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() + $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg();
            switch ($request["sebaran_kasus"]) {
                case 'cuci':
                    $total = $kepatuhan_prokes->pluck('fasilitas_cuci_tangan')->avg() * 5;
                    break;
                case 'sosialisasi':
                    $total = $kepatuhan_prokes->pluck('sosialisasi_prokes')->avg() * 5;
                    break;
                case 'suhu':
                    $total = $kepatuhan_prokes->pluck('cek_suhu_tubuh')->avg() * 5;
                    break;
                case 'petugas':
                    $total = $kepatuhan_prokes->pluck('petugas_pengawas_prokes')->avg() * 5;
                    break;
                case 'desinfeksi':
                    $total = $kepatuhan_prokes->pluck('desinfeksi_berkala')->avg() * 5;
                    break;
                default:
                    $total = $total_prokes;
            }
            $arrx["type"] = "Feature";
            $arrx["id"] = "$val->id";
            $arrx["properties"] = [
                "name" => $val->nama_kelurahan,
                "density" => round($total),
                "y" => round($total),
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

    public function download_institusi(Request $request)
    {
        return Excel::download(new ExportsDataProkesInstitusi($request["kode"]), 'template.xlsx');
    }

    public function import_prokes_institusi(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file'); //GET FILE
            Excel::import(new ImportDataProkesInstitusi, $file); //IMPORT FILE
            return redirect()->back()->with(['success' => 'Upload success']);
        }
        return redirect()->back()->with(['error' => 'Please choose file before']);
    }

    public function dokumen(Request $request)
    {
        return view('admin.prokes_institusi.dokumen');
    }

    public function upload_dokumen_institusi(Request $request)
    {
        // $this->validate($request, [
        //     'image' => 'required',
        //     'image.*' => 'mimes:doc,pdf,docx,zip',
        // ]);
        if ($request->hasfile('image')) {
            foreach ($request->file('image') as $key => $file) {
                $name = $key . '-' . time() . '.' . $file->extension();
                $file->move(public_path() . '/dokumen_institusi/', $name);
                $data[] = $name;
                $file = new \App\Models\DokumenInstitusi();
                $file->institusi_id = $request["institusi_id"];
                $file->kode_kecamatan = $request["kecamatan_id"];
                $file->kode_desa = $request["desa_id"];
                $file->lokasi_pantau = $request["master_lokasi_pantau"] . '-' . $request["lokasi_pantau"];
                $file->tanggal_pantau = date('Y-m-d', strtotime($request["tanggal_pantau"]));
                $file->deskripsi_image = $request["deskripsi_dokumen"];
                $file->image = $name;
                $file->save();
            }
        }
        return redirect(route('institusi.dokumen', ['institusi_id' => $request["institusi_id"]]));
    }

    public function get_image_institusi(Request $request)
    {
        $doc = \App\Models\DokumenInstitusi::where('institusi_id', $request["institusi_id"])->get();
        return response()->json($doc);
    }

    public function get_prokes_institusi(Request $request)
    {
        $data = \App\Models\ProkesInstitusi::all();
        $arr = [];
        foreach ($data as $val) {
            $arrx = [
                "id" => $val->id,
                "nama_user" => $val->get_user->name,
                "desa" => $val->get_desa->nama_kelurahan,
                "kecamatan" => $val->get_kecamatan->kecamatan,
                "lokasi_pantau" => $val->kode_lokasi_pantau,
                "tanggal_pantau" => $val->tanggal_pantau,
                "jam_pantau" => $val->jam_pantau,
                "selesai_jam_pantau" => $val->selesai_jam_pantau,
                "fasilitas_cuci_tangan" => $val->fasilitas_cuci_tangan,
                "sosialisasi_prokes" => $val->sosialisasi_prokes,
                "cek_suhu_tubuh" => $val->cek_suhu_tubuh,
                "petugas_pengawas_prokes" => $val->petugas_pengawas_prokes,
                "desinfeksi_berkala" => $val->desinfeksi_berkala,
            ];
            $arr[] = $arrx;
        }
        return response()->json($arr);
    }

    public function get_prokes_institusi_raw(Request $request)
    {
        $data = \App\Models\ProkesInstitusi::all();
        $arr = [];
        foreach ($data as $val) {
            $arrx = [
                "kecamatan" => $val->kecamatan_id,
                "desa" => $val->desa_id,
                "lokasi_pantau" => $val->lokasi_pantau,
                "tanggal_pantau" => $val->tanggal_pantau,
                "jam_pantau" => $val->jam_pantau,
                "jam_selesai_pantau" => $val->selesai_jam_pantau,
                "fasilitas_cuci_tangan" => $val->fasilitas_cuci_tangan,
                "sosialisasi_prokes" => $val->sosialisasi_prokes,
                "cek_suhu_tubuh" => $val->cek_suhu_tubuh,
                "petugas_pengawas_prokes" => $val->petugas_pengawas_prokes,
                "desinfeksi_berkala" => $val->desinfeksi_berkala,
            ];
            $arr[] = $arrx;
        }
        return response()->json($arr);
    }

    public function get_lokasi_pantau_institusi(Request $request)
    {
        $hotel = 'Hotel';
        $sebud = 'Kegiatan Seni Budaya';
        $belanja = 'Pusat Perbelanjaan';
        $publik = 'Area Publik';
        $resto = 'Restoran';
        $ibadah = 'Tempat Ibadah';
        $transport = 'Transportasi Umum';
        $wisata = 'Obyek Wisata';

        //kepatuhan hotal
        $hotel = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$hotel}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();

        $data["cuci_tangan_hotel"] = $hotel->pluck('fasilitas_cuci_tangan')->avg() ? $hotel->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_hotel"] = $hotel->pluck('sosialisasi_prokes')->avg() ? $hotel->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_hotel"] = $hotel->pluck('cek_suhu_tubuh')->avg() ? $hotel->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_hotel"] = $hotel->pluck('petugas_pengawas_prokes')->avg() ? $hotel->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_hotel"] = $hotel->pluck('desinfeksi_berkala')->avg() ? $hotel->pluck('desinfeksi_berkala')->avg() : 0;
        $data["hotel"] = $hotel->pluck('fasilitas_cuci_tangan')->avg() + $hotel->pluck('sosialisasi_prokes')->avg() + $hotel->pluck('cek_suhu_tubuh')->avg() + $hotel->pluck('petugas_pengawas_prokes')->avg() + $hotel->pluck('desinfeksi_berkala')->avg();

        //Kepatuhan seni budaya
        $sebud = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$sebud}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_sebud"] = $sebud->pluck('fasilitas_cuci_tangan')->avg() ? $sebud->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_sebud"] = $sebud->pluck('sosialisasi_prokes')->avg() ? $sebud->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_sebud"] = $sebud->pluck('cek_suhu_tubuh')->avg() ? $sebud->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_sebud"] = $sebud->pluck('petugas_pengawas_prokes')->avg() ? $sebud->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_sebud"] = $sebud->pluck('desinfeksi_berkala')->avg() ? $sebud->pluck('desinfeksi_berkala')->avg() : 0;
        $data["sebud"] = $sebud->pluck('fasilitas_cuci_tangan')->avg() + $sebud->pluck('sosialisasi_prokes')->avg() + $sebud->pluck('cek_suhu_tubuh')->avg() + $sebud->pluck('petugas_pengawas_prokes')->avg() + $sebud->pluck('desinfeksi_berkala')->avg();

        $belanja = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$belanja}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_belanja"] = $belanja->pluck('fasilitas_cuci_tangan')->avg() ? $belanja->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_belanja"] = $belanja->pluck('sosialisasi_prokes')->avg() ? $belanja->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_belanja"] = $belanja->pluck('cek_suhu_tubuh')->avg() ? $belanja->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_belanja"] = $belanja->pluck('petugas_pengawas_prokes')->avg() ? $belanja->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_belanja"] = $belanja->pluck('desinfeksi_berkala')->avg() ? $belanja->pluck('desinfeksi_berkala')->avg() : 0;
        $data["belanja"] = $belanja->pluck('fasilitas_cuci_tangan')->avg() + $belanja->pluck('sosialisasi_prokes')->avg() + $belanja->pluck('cek_suhu_tubuh')->avg() + $belanja->pluck('petugas_pengawas_prokes')->avg() + $belanja->pluck('desinfeksi_berkala')->avg();

        $publik = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$publik}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_publik"] = $publik->pluck('fasilitas_cuci_tangan')->avg() ? $publik->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_publik"] = $publik->pluck('sosialisasi_prokes')->avg() ? $publik->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_publik"] = $publik->pluck('cek_suhu_tubuh')->avg() ? $publik->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_publik"] = $publik->pluck('petugas_pengawas_prokes')->avg() ? $publik->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_publik"] = $publik->pluck('desinfeksi_berkala')->avg() ? $publik->pluck('desinfeksi_berkala')->avg() : 0;
        $data["publik"] = $publik->pluck('fasilitas_cuci_tangan')->avg() + $publik->pluck('sosialisasi_prokes')->avg() + $publik->pluck('cek_suhu_tubuh')->avg() + $publik->pluck('petugas_pengawas_prokes')->avg() + $publik->pluck('desinfeksi_berkala')->avg();

        $resto = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$resto}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_resto"] = $resto->pluck('fasilitas_cuci_tangan')->avg() ? $resto->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_resto"] = $resto->pluck('sosialisasi_prokes')->avg() ? $resto->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_resto"] = $resto->pluck('cek_suhu_tubuh')->avg() ? $resto->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_resto"] = $resto->pluck('petugas_pengawas_prokes')->avg() ? $resto->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_resto"] = $resto->pluck('desinfeksi_berkala')->avg() ? $resto->pluck('desinfeksi_berkala')->avg() : 0;
        $data["resto"] = $resto->pluck('fasilitas_cuci_tangan')->avg() + $resto->pluck('sosialisasi_prokes')->avg() + $resto->pluck('cek_suhu_tubuh')->avg() + $resto->pluck('petugas_pengawas_prokes')->avg() + $resto->pluck('desinfeksi_berkala')->avg();

        $transport = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$transport}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_transport"] = $transport->pluck('fasilitas_cuci_tangan')->avg() ? $transport->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_transport"] = $transport->pluck('sosialisasi_prokes')->avg() ? $transport->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_transport"] = $transport->pluck('cek_suhu_tubuh')->avg() ? $transport->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_transport"] = $transport->pluck('petugas_pengawas_prokes')->avg() ? $transport->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_transport"] = $transport->pluck('desinfeksi_berkala')->avg() ? $transport->pluck('desinfeksi_berkala')->avg() : 0;
        $data["transport"] = $transport->pluck('fasilitas_cuci_tangan')->avg() + $transport->pluck('sosialisasi_prokes')->avg() + $transport->pluck('cek_suhu_tubuh')->avg() + $transport->pluck('petugas_pengawas_prokes')->avg() + $transport->pluck('desinfeksi_berkala')->avg();

        $wisata = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$wisata}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_wisata"] = $wisata->pluck('fasilitas_cuci_tangan')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_wisata"] = $wisata->pluck('sosialisasi_prokes')->avg() ? $wisata->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_wisata"] = $wisata->pluck('cek_suhu_tubuh')->avg() ? $wisata->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_wisata"] = $wisata->pluck('petugas_pengawas_prokes')->avg() ? $wisata->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_wisata"] = $wisata->pluck('desinfeksi_berkala')->avg() ? $wisata->pluck('desinfeksi_berkala')->avg() : 0;
        $data["wisata"] = $wisata->pluck('fasilitas_cuci_tangan')->avg() + $wisata->pluck('sosialisasi_prokes')->avg() + $wisata->pluck('cek_suhu_tubuh')->avg() + $wisata->pluck('petugas_pengawas_prokes')->avg() + $wisata->pluck('desinfeksi_berkala')->avg();

        $ibadah = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$ibadah}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->get();
        $data["cuci_tangan_ibadah"] = $ibadah->pluck('fasilitas_cuci_tangan')->avg() ? $ibadah->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_ibadah"] = $ibadah->pluck('sosialisasi_prokes')->avg() ? $ibadah->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_ibadah"] = $ibadah->pluck('cek_suhu_tubuh')->avg() ? $ibadah->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_ibadah"] = $ibadah->pluck('petugas_pengawas_prokes')->avg() ? $ibadah->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_ibadah"] = $ibadah->pluck('desinfeksi_berkala')->avg() ? $ibadah->pluck('desinfeksi_berkala')->avg() : 0;
        $data["ibadah"] = $ibadah->pluck('fasilitas_cuci_tangan')->avg() + $ibadah->pluck('sosialisasi_prokes')->avg() + $ibadah->pluck('cek_suhu_tubuh')->avg() + $ibadah->pluck('petugas_pengawas_prokes')->avg() + $ibadah->pluck('desinfeksi_berkala')->avg();
        // dd($wisata);
        return response()->json($data);
    }
    public function get_lokasi_pantau_institusi_desa(Request $request)
    {
        $hotel = 'Hotel';
        $sebud = 'Kegiatan Seni Budaya';
        $belanja = 'Pusat Perbelanjaan';
        $publik = 'Area Publik';
        $resto = 'Restoran';
        $ibadah = 'Tempat Ibadah';
        $transport = 'Transportasi Umum';
        $wisata = 'Objek Wisata';

        //kepatuhan hotal
        $hotel = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$hotel}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();

        $data["cuci_tangan_hotel"] = $hotel->pluck('fasilitas_cuci_tangan')->avg() ? $hotel->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_hotel"] = $hotel->pluck('sosialisasi_prokes')->avg() ? $hotel->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_hotel"] = $hotel->pluck('cek_suhu_tubuh')->avg() ? $hotel->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_hotel"] = $hotel->pluck('petugas_pengawas_prokes')->avg() ? $hotel->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_hotel"] = $hotel->pluck('desinfeksi_berkala')->avg() ? $hotel->pluck('desinfeksi_berkala')->avg() : 0;
        $data["hotel"] = $hotel->pluck('fasilitas_cuci_tangan')->avg() + $hotel->pluck('sosialisasi_prokes')->avg() + $hotel->pluck('cek_suhu_tubuh')->avg() + $hotel->pluck('petugas_pengawas_prokes')->avg() + $hotel->pluck('desinfeksi_berkala')->avg();

        //Kepatuhan seni budaya
        $sebud = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$sebud}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();

        $data["cuci_tangan_sebud"] = $sebud->pluck('fasilitas_cuci_tangan')->avg() ? $sebud->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_sebud"] = $sebud->pluck('sosialisasi_prokes')->avg() ? $sebud->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_sebud"] = $sebud->pluck('cek_suhu_tubuh')->avg() ? $sebud->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_sebud"] = $sebud->pluck('petugas_pengawas_prokes')->avg() ? $sebud->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_sebud"] = $sebud->pluck('desinfeksi_berkala')->avg() ? $sebud->pluck('desinfeksi_berkala')->avg() : 0;
        $data["sebud"] = $sebud->pluck('fasilitas_cuci_tangan')->avg() + $sebud->pluck('sosialisasi_prokes')->avg() + $sebud->pluck('cek_suhu_tubuh')->avg() + $sebud->pluck('petugas_pengawas_prokes')->avg() + $sebud->pluck('desinfeksi_berkala')->avg();

        $belanja = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$belanja}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();

        $data["cuci_tangan_belanja"] = $belanja->pluck('fasilitas_cuci_tangan')->avg() ? $belanja->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_belanja"] = $belanja->pluck('sosialisasi_prokes')->avg() ? $belanja->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_belanja"] = $belanja->pluck('cek_suhu_tubuh')->avg() ? $belanja->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_belanja"] = $belanja->pluck('petugas_pengawas_prokes')->avg() ? $belanja->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_belanja"] = $belanja->pluck('desinfeksi_berkala')->avg() ? $belanja->pluck('desinfeksi_berkala')->avg() : 0;
        $data["belanja"] = $belanja->pluck('fasilitas_cuci_tangan')->avg() + $belanja->pluck('sosialisasi_prokes')->avg() + $belanja->pluck('cek_suhu_tubuh')->avg() + $belanja->pluck('petugas_pengawas_prokes')->avg() + $belanja->pluck('desinfeksi_berkala')->avg();

        $publik = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$publik}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();
        $data["cuci_tangan_publik"] = $publik->pluck('fasilitas_cuci_tangan')->avg() ? $publik->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_publik"] = $publik->pluck('sosialisasi_prokes')->avg() ? $publik->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_publik"] = $publik->pluck('cek_suhu_tubuh')->avg() ? $publik->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_publik"] = $publik->pluck('petugas_pengawas_prokes')->avg() ? $publik->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_publik"] = $publik->pluck('desinfeksi_berkala')->avg() ? $publik->pluck('desinfeksi_berkala')->avg() : 0;
        $data["publik"] = $publik->pluck('fasilitas_cuci_tangan')->avg() + $publik->pluck('sosialisasi_prokes')->avg() + $publik->pluck('cek_suhu_tubuh')->avg() + $publik->pluck('petugas_pengawas_prokes')->avg() + $publik->pluck('desinfeksi_berkala')->avg();

        $resto = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$resto}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();
        $data["cuci_tangan_resto"] = $resto->pluck('fasilitas_cuci_tangan')->avg() ? $resto->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_resto"] = $resto->pluck('sosialisasi_prokes')->avg() ? $resto->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_resto"] = $resto->pluck('cek_suhu_tubuh')->avg() ? $resto->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_resto"] = $resto->pluck('petugas_pengawas_prokes')->avg() ? $resto->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_resto"] = $resto->pluck('desinfeksi_berkala')->avg() ? $resto->pluck('desinfeksi_berkala')->avg() : 0;
        $data["resto"] = $resto->pluck('fasilitas_cuci_tangan')->avg() + $resto->pluck('sosialisasi_prokes')->avg() + $resto->pluck('cek_suhu_tubuh')->avg() + $resto->pluck('petugas_pengawas_prokes')->avg() + $resto->pluck('desinfeksi_berkala')->avg();

        $transport = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$transport}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();
        $data["cuci_tangan_transport"] = $transport->pluck('fasilitas_cuci_tangan')->avg() ? $transport->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_transport"] = $transport->pluck('sosialisasi_prokes')->avg() ? $transport->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_transport"] = $transport->pluck('cek_suhu_tubuh')->avg() ? $transport->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_transport"] = $transport->pluck('petugas_pengawas_prokes')->avg() ? $transport->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_transport"] = $transport->pluck('desinfeksi_berkala')->avg() ? $transport->pluck('desinfeksi_berkala')->avg() : 0;
        $data["transport"] = $transport->pluck('fasilitas_cuci_tangan')->avg() + $transport->pluck('sosialisasi_prokes')->avg() + $transport->pluck('cek_suhu_tubuh')->avg() + $transport->pluck('petugas_pengawas_prokes')->avg() + $transport->pluck('desinfeksi_berkala')->avg();

        $wisata = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$wisata}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();
        $data["cuci_tangan_wisata"] = $wisata->pluck('fasilitas_cuci_tangan')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_wisata"] = $wisata->pluck('sosialisasi_prokes')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["suhu_wisata"] = $wisata->pluck('cek_suhu_tubuh')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["pengawas_wisata"] = $wisata->pluck('petugas_pengawas_prokes')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["desinfeksi_wisata"] = $wisata->pluck('desinfeksi_berkala')->avg() ? $wisata->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["wisata"] = $wisata->pluck('fasilitas_cuci_tangan')->avg() + $wisata->pluck('sosialisasi_prokes')->avg() + $wisata->pluck('cek_suhu_tubuh')->avg() + $wisata->pluck('petugas_pengawas_prokes')->avg() + $wisata->pluck('desinfeksi_berkala')->avg();

        $ibadah = \App\Models\ProkesInstitusi::where('lokasi_pantau', 'LIKE', "%{$ibadah}%")
            ->where('tanggal_pantau', $request["tanggal_pantau"])
            ->where('kecamatan_id', $request["kode_kecamatan"])
            ->get();
        $data["cuci_tangan_ibadah"] = $ibadah->pluck('fasilitas_cuci_tangan')->avg() ? $ibadah->pluck('fasilitas_cuci_tangan')->avg() : 0;
        $data["prokes_ibadah"] = $ibadah->pluck('sosialisasi_prokes')->avg() ? $ibadah->pluck('sosialisasi_prokes')->avg() : 0;
        $data["suhu_ibadah"] = $ibadah->pluck('cek_suhu_tubuh')->avg() ? $ibadah->pluck('cek_suhu_tubuh')->avg() : 0;
        $data["pengawas_ibadah"] = $ibadah->pluck('petugas_pengawas_prokes')->avg() ? $ibadah->pluck('petugas_pengawas_prokes')->avg() : 0;
        $data["desinfeksi_ibadah"] = $ibadah->pluck('desinfeksi_berkala')->avg() ? $ibadah->pluck('desinfeksi_berkala')->avg() : 0;
        $data["ibadah"] = $ibadah->pluck('fasilitas_cuci_tangan')->avg() + $ibadah->pluck('sosialisasi_prokes')->avg() + $ibadah->pluck('cek_suhu_tubuh')->avg() + $ibadah->pluck('petugas_pengawas_prokes')->avg() + $ibadah->pluck('desinfeksi_berkala')->avg();

        return response()->json($data);
    }

    public function datatable_institusi(Request $request)
    {
        if (Auth::user()->role == 'super admin') {
            $data = \App\Models\ProkesInstitusi::all();
        } else if (Auth::user()->role == 'Admin') {
            $data = \App\Models\ProkesInstitusi::where('kecamatan_id', Auth::user()->kode_kecamatan)->get();
        } else {
            $data = \App\Models\ProkesInstitusi::where('created_by', Auth::user()->id)->get();
        }
        return Datatables::of($data)
            ->addColumn('nama_user', function ($val) {
                $user = $val->get_user ? $val->get_user->name : '';
                return "<a href=" . route('institusi.dokumen') . '?institusi_id=' . $val->id . ">" . $user . "</a>";
            })
            ->addColumn('kelurahan', function ($val) {
                return $val->get_desa ? $val->get_desa->nama_kelurahan : '';
            })
            ->addColumn('kecamatan', function ($val) {
                return $val->get_kecamatan ? $val->get_kecamatan->kecamatan : '';
            })
            ->addColumn('tanggal_pantau', function ($val) {
                return date('d M Y', strtotime($val->tanggal_pantau));
            })
            ->addColumn('mulai_jam_pantau', function ($val) {
                return date('H:i:s', strtotime($val->jam_pantau));
            })
            ->addColumn('selesai_jam_pantau', function ($val) {
                return date('H:i:s', strtotime($val->selesai_jam_pantau));
            })
            ->addColumn('total_prokes', function ($val) {
                return $val->fasilitas_cuci_tangan + $val->sosialisasi_prokes + $val->cek_suhu_tubuh + $val->petugas_pengawas_prokes + $val->desinfeksi_berkala;
            })
            ->addColumn('peta_zonasi_masker', function ($val) {
                return '';
            })
            ->addColumn('created_at', function ($val) {
                return date('d M Y', strtotime($val->created_at));
            })
            ->addColumn('aksi', function ($val) {
                return '<div class="dropdown">
                            <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">Aksi</button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" role="presentation" href=' . route('institusi.dokumen') . '?institusi_id=' . $val->id . '>Tambah Dokumen</a>
                                <a class="dropdown-item edit" data-bind=\'' . $val . '\' role="presentation" href="javascript:void(0)" data-toggle="modal">Edit</a>
                                <a class="dropdown-item delete" data-bind="' . $val->id . '" role="presentation" href="javascript:void(0)">Hapus</a>
                            </div>
                        </div>';
            })
            ->rawColumns(['aksi', 'nama_user'])
            ->make(true);
    }

    public function get_sebaran_institusi_pie(Request $request)
    {
        $tanggal_screaning = \App\Models\ProkesInstitusi::select('tanggal_pantau')
            ->orderBy('tanggal_pantau', 'desc')->first();
        $tanggal = $request["periode_kasus"];
        if ($tanggal != '') {
            $var = $request["periode_kasus"];
            $date = str_replace('/', '-', $var);
            $periode_kasus = date('Y-m-d', strtotime($date));
        } else {
            $periode_kasus = $tanggal_screaning->tanggal_pantau;
        }
        $kepatuhan_prokes = DB::table('prokes_institusi as a')
            ->select(['a.id','a.tanggal_pantau', 'a.kecamatan_id as kode_kecamatan', 'b.kecamatan', 'c.nama_kelurahan', 'a.lokasi_pantau as lokasi_pantau', DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
            ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
            ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
            ->where('a.tanggal_pantau', $periode_kasus)
            ->groupBy('a.kecamatan_id')
            ->get();
        foreach ($kepatuhan_prokes as $key => $val) {
            $total = $val->fasilitas_cuci_tangan + $val->sosialisasi_prokes + $val->cek_suhu_tubuh + $val->petugas_pengawas_prokes + $val->desinfeksi_berkala;
            $arrx["type"] = "Feature";
            $arrx["id"] = "$val->id";
            $arrx["properties"] = [
                "name" => $val->kecamatan,
                "density" => round($total),
                // total untuk pie grafik
                "y" => round($total),
                //
                "total_kasus" => 0,
                // "total_vaksin_2" => $peserta[0]->total_vaksin_2,
                // "kasus" => ucfirst($request["sebaran_kasus"]),
            ];
            $arr[] = $arrx;
        }
        $arry["features"] = $arr;
        return response()->json($arry);
    }
}
