<?php

namespace App\Exports;

use Auth;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportsDataProkesIndividu implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($kode)
    {
        $this->kode = $kode;
    }

    public function collection()
    {
        if (Auth::user()->role == 'super admin') {
            $data = DB::table('kepatuhan_prokes')
                ->get();
        } else if(Auth::user()->role == 'Admin') {
            $data = DB::table('kepatuhan_prokes')
                ->where('kode_kecamatan', $this->kode)
                ->get();
        }else{
            $data = DB::table('kepatuhan_prokes')
                ->where('created_by', Auth::user()->id)
                ->get();
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Desa',
            'Lokasi Pantau',
            'Tanggal Pantau',
            'Jam Pantau',
            'Jam Selesai Pantau',
            'Jumlah Jaga Jarak',
            'Tidak Jaga Jarak',
            'Jumlah Pakai Masker',
            'Tidak Pakai Masker',
        ];
    }

    public function map($data): array
    {
        return [
            $data->kode_kecamatan,
            $data->kode_desa,
            $data->kode_lokasi_pantau,
            Date::stringToExcel($data->tanggal_pantau),
            $data->jam_pantau,
            $data->selesai_jam_pantau,
            $data->jaga_jarak,
            $data->tidak_jaga_jarak,
            $data->pakai_masker,
            $data->tidak_pakai_masker,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

}
