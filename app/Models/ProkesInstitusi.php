<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProkesInstitusi extends Model
{
    protected $table = 'prokes_institusi';

    public function get_kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'kecamatan_id', 'code_kecamatan');
    }

    public function get_desa()
    {
        return $this->belongsTo('App\Models\Desa', 'desa_id', 'kode_kelurahan');
    }
    
    public function get_user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
