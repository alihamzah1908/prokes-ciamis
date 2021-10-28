<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($startDate, $endDate, $kecamatan){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kecamatan = $kecamatan;
    }
    public function collection()
    {
        if ($this->kecamatan == '') {
            $individu = DB::table('kepatuhan_prokes as a')
                ->select(['a.tanggal_pantau', 'b.kecamatan', 'c.nama_kelurahan', 'a.kode_lokasi_pantau as lokasi_pantau', DB::raw('SUM(a.pakai_masker) as pakai_masker'),
                    DB::raw('SUM(a.tidak_pakai_masker) as tidak_pakai_masker'), DB::raw('SUM(a.jaga_jarak) as jaga_jarak'),
                    DB::raw('SUM(a.tidak_jaga_jarak) as tidak_jaga_jarak'), DB::raw("'Individu' as jenis_kepatuhan"),
                    DB::raw("'0' as fasilitas_cuci_tangan, '0' as sosialisasi_prokes, '0' as cek_suhu_tubuh, '0' as petugas_pengawas_prokes, '0' as desinfeksi_berkala")])
                ->join('kecamatan as b', 'a.kode_kecamatan', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.kode_desa', 'c.kode_kelurahan')
                ->whereBetween('a.tanggal_pantau', [$this->startDate, $this->endDate])
                ->groupBy('a.kode_kecamatan', 'a.tanggal_pantau');
            $result = DB::table('prokes_institusi as a')
                ->select(['a.tanggal_pantau', 'b.kecamatan', 'c.nama_kelurahan', 'a.lokasi_pantau as lokasi_pantau', DB::raw("'0' as pakai_masker, '0' as tidak_pakai_masker,
                            '0' as jaga_jarak, '0' as tidak_jaga_jarak, 'Institusi' as jenis_kepatuhan"), DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                    DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
                ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
                ->whereBetween('a.tanggal_pantau', [$this->startDate, $this->endDate])
                ->groupBy('a.kecamatan_id', 'a.tanggal_pantau')
                ->union($individu)
                ->get();
            // dd($result);
        } else {
            $individu = DB::table('kepatuhan_prokes as a')
                ->select(['a.tanggal_pantau', 'b.kecamatan', 'c.nama_kelurahan', 'a.kode_lokasi_pantau as lokasi_pantau', DB::raw('SUM(a.pakai_masker) as pakai_masker'),
                    DB::raw('SUM(a.tidak_pakai_masker) as tidak_pakai_masker'), DB::raw('SUM(a.jaga_jarak) as jaga_jarak'),
                    DB::raw('SUM(a.tidak_jaga_jarak) as tidak_jaga_jarak'), DB::raw("'Individu' as jenis_kepatuhan"),
                    DB::raw("'0' as fasilitas_cuci_tangan, '0' as sosialisasi_prokes, '0' as cek_suhu_tubuh, '0' as petugas_pengawas_prokes, '0' as desinfeksi_berkala")])
                ->join('kecamatan as b', 'a.kode_kecamatan', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.kode_desa', 'c.kode_kelurahan')
                ->where('a.kode_kecamatan', $this->kecamatan)
                ->whereBetween('a.tanggal_pantau', [$this->startDate, $this->endDate])
                ->groupBy('a.kode_kecamatan', 'a.tanggal_pantau');
            $result = DB::table('prokes_institusi as a')
                ->select(['a.tanggal_pantau', 'b.kecamatan', 'c.nama_kelurahan', 'a.lokasi_pantau as lokasi_pantau', DB::raw("'0' as pakai_masker, '0' as tidak_pakai_masker,
                        '0' as jaga_jarak, '0' as tidak_jaga_jarak, 'Institusi' as jenis_kepatuhan"), DB::raw('AVG(a.fasilitas_cuci_tangan) as fasilitas_cuci_tangan'), DB::raw('AVG(a.sosialisasi_prokes) as sosialisasi_prokes'),
                    DB::raw('AVG(a.cek_suhu_tubuh) as cek_suhu_tubuh'), DB::raw('AVG(a.petugas_pengawas_prokes) as petugas_pengawas_prokes'), DB::raw('AVG(a.desinfeksi_berkala) as desinfeksi_berkala')])
                ->join('kecamatan as b', 'a.kecamatan_id', 'b.code_kecamatan')
                ->join('desa_master as c', 'a.desa_id', 'c.kode_kelurahan')
                ->where('a.kecamatan_id', $this->kecamatan)
                ->whereBetween('a.tanggal_pantau', [$this->startDate, $this->endDate])
                ->groupBy('a.kecamatan_id', 'a.tanggal_pantau')
                ->union($individu)
                ->get();
            // dd($result);
        };
        $arr = [];
        foreach($result as $key => $val){
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
            $arrx["kecamatan"] = $val->kecamatan;
            $arrx["desa"] = $val->nama_kelurahan;
            $arrx["lokasi_pantau"] = $val->lokasi_pantau;
            $arrx["tanggal_pantau"] = $val->tanggal_pantau;
            $arrx["jenis_kepatuhan"] = $val->jenis_kepatuhan;
            $arrx["kepatuhan_individu"] = round($kepatuhan_masker);
            $arrx["kepatuhan_institusi"] = round($institusi);
            $arrx["kepatuhan_prokes"] = round($kepatuhan_prokes);
            $arr[] = $arrx;
        }
        $data = collect($arr);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Desa',
            'Lokasi Pantau',
            'Tanggal Pantau',
            'Jenis Kepatuhan',
            'Kepatuhan Individu',
            'Kepatuhan Institusi',
            'Kepatuhan Prokes',
        ];
    }

    public function map($data): array
    {
        return [
            $data->kecamatan,
            $data->nama_kelurahan,
            $data->lokasi_pantau,
            Date::stringToExcel($data->tanggal_pantau),
            $data->jenis_kapatuhan,
            $data->kepatuhan_individu,
            $data->kepatuhan_institusi,
            $data->kepatuhan_prokes
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
