<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProkesInstitusi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prokes_institusi', function (Blueprint $table) {
            $table->id();
            $table->integer('desa_id')->nullable();
            $table->integer('kecamatan_id')->nullable();
            $table->string('lokasi_pantau')->nullable();
            $table->date('tanggal_pantau')->nullable();
            $table->time('jam_pantau')->nullable();
            $table->integer('fasilitas_cuci_tangan')->nullable();
            $table->integer('sosialisasi_prokes')->nullable();
            $table->integer('cek_suhu_tubuh')->nullable();
            $table->integer('petugas_pengawas_prokes')->nullable();
            $table->integer('desinfeksi_berkala')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prokes_institusi');
    }
}
