<?php

namespace App\Imports;

use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportDataProkesIndividu implements WithHeadingRow, ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $arr_new = collect();
        foreach ($rows as $row) {
            if ($row->filter()->isNotEmpty()) {
                $arr_new->push($row);
            }
        }

        // dd($arr_new);
        foreach ($arr_new as $val) {
            $date1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val["tanggal_pantau"]);
            if ($val["tanggal_pantau"]) {
                $date1 = (array) $date1;
                $strDate = date('Y-m-d', strtotime($date1["date"]));
            } else {
                $strDate = null;
            }
            $prokes = \App\Models\Prokes::where('kode_desa', $val["desa"])
                ->where('kode_lokasi_pantau', $val["lokasi_pantau"])
                ->first();
            if ($prokes) {
                $obj = \App\Models\Prokes::findOrFail($prokes->id);
            } else {
                $obj = new \App\Models\Prokes();
                $obj->created_by = Auth::user()->id;
            }
            $obj->kode_kecamatan = $val["kecamatan"];
            $obj->kode_desa = $val["desa"];
            $obj->kode_lokasi_pantau = $val["lokasi_pantau"];
            $obj->tanggal_pantau = $strDate;
            $obj->jam_pantau = $val["jam_pantau"];
            $obj->selesai_jam_pantau = $val["jam_selesai_pantau"];
            $obj->jaga_jarak = $val["jumlah_jaga_jarak"];
            $obj->tidak_jaga_jarak = $val["tidak_jaga_jarak"];
            $obj->pakai_masker = $val["jumlah_pakai_masker"];
            $obj->tidak_pakai_masker = $val["tidak_pakai_masker"];
            $obj->save();
        }
    }
}
