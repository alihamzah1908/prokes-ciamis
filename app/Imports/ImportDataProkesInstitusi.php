<?php

namespace App\Imports;

use DB;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportDataProkesInstitusi implements WithHeadingRow, ToCollection
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
        foreach ($arr_new as $val) {
            $date1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val["tanggal_pantau"]);
            if ($val["tanggal_pantau"]) {
                $date1 = (array) $date1;
                $strDate = date('Y-m-d', strtotime($date1["date"]));
            } else {
                $strDate = null;
            }
            $prokes = \App\Models\ProkesInstitusi::where('desa_id', $val["desa"])
                ->where('lokasi_pantau', $val["lokasi_pantau"])
                ->first();
            if ($prokes) {
                $obj = \App\Models\ProkesInstitusi::findOrFail($prokes->id);
            } else {
                $obj = new \App\Models\ProkesInstitusi();
                $obj->created_by = Auth::user()->id;
            }
            $obj->kecamatan_id = $val["kecamatan"];
            $obj->desa_id = $val["desa"];
            $obj->lokasi_pantau = $val["lokasi_pantau"];
            $obj->tanggal_pantau = $strDate;
            $obj->jam_pantau = $val["jam_pantau"];
            $obj->selesai_jam_pantau = $val["jam_selesai_pantau"];
            $obj->fasilitas_cuci_tangan = $val["fasilitas_cuci_tangan"];
            $obj->sosialisasi_prokes = $val["sosialisasi_prokes"];
            $obj->cek_suhu_tubuh = $val["cek_suhu_tubuh"];
            $obj->petugas_pengawas_prokes = $val["petugas_pengawas_prokes"];
            $obj->desinfeksi_berkala = $val["desinfeksi_berkala"];
            $obj->save();
        }
    }
}
