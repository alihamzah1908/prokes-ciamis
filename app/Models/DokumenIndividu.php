<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenIndividu extends Model
{
    //
    protected $table = 'dokumen_individu';

    public function get_desa()
    {
        return $this->belongsTo('\App\Models\Desa', 'kode_desa', 'kode_kelurahan');
    }
    public function get_kecamatan()
    {
        return $this->belongsTo('\App\Models\Kecamatan', 'kode_kecamatan', 'code_kecamatan');
    }
}
