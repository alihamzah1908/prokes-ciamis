<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function get_summary_individu(Request $request)
    {
        if ($request["periode_kasus"]) {
            $tanggal_pantau = request()->periode_kasus;
        } else {
            $var = \App\Models\Prokes::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal_pantau = $var->tanggal_pantau;
        }
        $kecamatan = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)
            ->groupBy('kode_kecamatan')
            ->get();
        $desa = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)
            ->groupBy('kode_desa')
            ->get();
        $arr = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)
            ->get();
        $masker = $arr->sum('pakai_masker') + $arr->sum('tidak_pakai_masker');
        $jarak = $arr->sum('jaga_jarak') + $arr->sum('tidak_jaga_jarak');

        // Paki Masker
        if ($masker != 0) {
            $pakai_masker = ($arr->sum('pakai_masker') / ($arr->sum('pakai_masker') + $arr->sum('tidak_pakai_masker'))) * 100;
        } else {
            $pakai_masker = 0;
        };

        // Jaga Jarak
        if ($jarak != 0) {
            $jaga_jarak = ($arr->sum('jaga_jarak') / ($arr->sum('jaga_jarak') + $arr->sum('tidak_jaga_jarak'))) * 100;
        } else {
            $jaga_jarak = 0;
        };

        // Total kepatuhan prokes
        $kepatuhan_prokes = \App\Models\Prokes::where('tanggal_pantau', $tanggal_pantau)->get();
        $total_masker = $kepatuhan_prokes->pluck('pakai_masker')->sum() + $kepatuhan_prokes->pluck('tidak_pakai_masker')->sum();
        $total_jarak = $kepatuhan_prokes->pluck('jaga_jarak')->sum() + $kepatuhan_prokes->pluck('tidak_jaga_jarak')->sum();
        $kepatuhan_masker = ($kepatuhan_prokes->pluck('pakai_masker')->sum() != 0) ? ($kepatuhan_prokes->pluck('pakai_masker')->sum() / $total_masker) * 100 : 0;
        $kepatuhan_jaga_jarak = ($kepatuhan_prokes->pluck('jaga_jarak')->sum() != 0) ? ($kepatuhan_prokes->pluck('jaga_jarak')->sum() / $total_jarak) * 100 : 0;
        $kepatuhan = ($kepatuhan_masker + $kepatuhan_jaga_jarak) / 2;

        $data = [
            'kecamatan' => count($kecamatan),
            'desa' => count($desa),
            'pakai_masker' => $arr->sum('pakai_masker'),
            'jaga_jarak' => $arr->sum('jaga_jarak'),
            'kepatuhan_pakai_masker' => round($pakai_masker, 2) . '%',
            'kepatuhan_jaga_jarak' => round($jaga_jarak, 2) . '%',
            'kepatuhan_prokes' => round($kepatuhan, 2) . '%',
            'periode_kasus' => date('d M Y', strtotime($tanggal_pantau)),
        ];
        return response()->json($data);
    }

    public function get_summary_institusi(Request $request)
    {
        if ($request["periode_kasus"]) {
            $tanggal_pantau = request()->periode_kasus;
        } else {
            $var = \App\Models\Prokes::select('tanggal_pantau')
                ->orderBy('tanggal_pantau', 'desc')->first();
            $tanggal_pantau = $var->tanggal_pantau;
        }
        if ($request["periode_kasus"]) {
            $arr = \App\Models\ProkesInstitusi::where('tanggal_pantau', $tanggal_pantau)->get();
        } else {
            $arr = \App\Models\ProkesInstitusi::get();
        }
        $cuci_tangan = $arr->pluck('fasilitas_cuci_tangan')->avg() * 5;
        $sosialisasi_prokes = $arr->pluck('sosialisasi_prokes')->avg() * 5;
        $suhu = $arr->pluck('cek_suhu_tubuh')->avg() * 5;
        $pengawas = $arr->pluck('petugas_pengawas_prokes')->avg() * 5;
        $desinfeksi = $arr->pluck('desinfeksi_berkala')->avg() * 5;
        $average = $arr->pluck('fasilitas_cuci_tangan')->avg() + $arr->pluck('sosialisasi_prokes')->avg() + $arr->pluck('cek_suhu_tubuh')->avg() + $arr->pluck('petugas_pengawas_prokes')->avg() + $arr->pluck('desinfeksi_berkala')->avg();
        $data = [
            'cuci_tangan' => round($cuci_tangan, 2) . ' %',
            'sosialisasi_prokes' => round($sosialisasi_prokes, 2) . ' %',
            'cek_suhu_tubuh' => round($suhu, 2) . ' %',
            'petugas_pengawas_prokes' => round($pengawas, 2) . ' %',
            'desinfeksi_berkala' => round($desinfeksi, 2) . ' %',
            'kepatuhan_prokes_institusi' => round($average, 2) . ' %',
        ];
        return response()->json($data);
    }
}
