<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prokes extends Model
{
    protected $table = 'kepatuhan_prokes';

    public function get_kecamatan(){
        return $this->belongsTo('App\Models\Kecamatan','kode_kecamatan','code_kecamatan');
    }

    public function get_desa(){
        return $this->belongsTo('App\Models\Desa','kode_desa','kode_kelurahan');
    }

    public function get_user(){
        return $this->belongsTo('App\Models\User','created_by','id');
    }
}
