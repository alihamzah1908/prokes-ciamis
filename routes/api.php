<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/prokes_individu','ProkesIndividuController@get_sebaran_individu')->name('sebaran.prokes_individu');
Route::get('/prokes_institusi','ProkesInstitusiController@get_sebaran_institusi')->name('sebaran.prokes_institusi');
Route::get('/prokes_individu_desa','ProkesIndividuController@get_sebaran_individu_desa')->name('sebaran.prokes_individu_desa');
Route::get('/prokes_institusi_desa','ProkesInstitusiController@get_sebaran_institusi_desa')->name('sebaran.prokes_institusi_desa');
Route::get('/get_lokasi_pantau_individu','ProkesIndividuController@get_lokasi_pantau_individu')->name('lokasi.pantau');
Route::get('/get_lokasi_pantau_individu_desa','ProkesIndividuController@get_lokasi_pantau_individu_desa')->name('lokasi.pantau_desa');
Route::get('/get_lokasi_pantau_institusi','ProkesInstitusiController@get_lokasi_pantau_institusi')->name('lokasi.pantau_institusi');
Route::get('/get_lokasi_pantau_institusi_desa','ProkesInstitusiController@get_lokasi_pantau_institusi_desa')->name('lokasi.pantau_institusi_desa');
