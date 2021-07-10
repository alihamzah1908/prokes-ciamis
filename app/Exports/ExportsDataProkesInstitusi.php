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

class ExportsDataProkesInstitusi implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping
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
            $data = DB::table('prokes_institusi')
                ->get();
        } else if (Auth::user()->role == 'Admin') {
            $data = DB::table('prokes_institusi')
                ->where('kecamatan_id', $this->kode)
                ->get();
        } else {
            $data = DB::table('prokes_institusi')
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
            'Fasilitas Cuci Tangan',
            'Sosialisasi Prokes',
            'Cek Suhu Tubuh',
            'Petugas Pengawas Prokes',
            'Desinfeksi Berkala',
        ];
    }

    public function map($data): array
    {
        return [
            $data->kecamatan_id,
            $data->desa_id,
            $data->lokasi_pantau,
            Date::stringToExcel($data->tanggal_pantau),
            $data->jam_pantau,
            $data->selesai_jam_pantau,
            $data->fasilitas_cuci_tangan,
            $data->sosialisasi_prokes,
            $data->cek_suhu_tubuh,
            $data->petugas_pengawas_prokes,
            $data->desinfeksi_berkala,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
