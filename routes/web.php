<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(['middleware' => 'auth.dashboard'], function () {
    Route::get('/', function () {
        // return view('frontend.peta');
        return "Halaman Tidak Tersedia";
    })->name('peta.peta_prokes');

    Route::get('/institusi', function () {
        return view('frontend.peta_institusi');
    })->name('peta.peta_institusi');

    Route::get('/institusi_desa', function () {
        return view('frontend.peta_institusi_desa');
    })->name('peta.peta_institusi_desa');

    Route::get('/peta_prokes', function () {
        return view('frontend.peta_prokes_desa');
    })->name('peta.peta_prokes_desa');

    Route::get('/v2', function(){
        return view('newtheme.prokes_individu');
    })->name('prokes.individu');

    Route::get('/v2/individu_desa', function(){
        return view('newtheme.prokes_individu_desa');
    })->name('individu.desa');
    
    Route::get('/v2/prokes_institusi', function(){
        return view('newtheme.prokes_institusi');
    })->name('prokes.institusi');

    Route::get('/v2/institusi_desa', function(){
        return view('newtheme.prokes_institusi_desa');
    })->name('institusi.desa');

    Route::get('/v2/dokumentasi', function(){
        return view('newtheme.dokumentasi');
    })->name('dokumentasi');

});
Route::get('/admin', function () {
    return view('auth.login');
});

Route::get('admin/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return view('auth.login_dashboard');
})->name('login.prokes');

Route::get('/admin/login', 'AuthController@login')->name('login');
Route::post('/admin/logout', 'AuthController@logout')->name('logout');
Route::post('/logout_dashboard', 'AuthController@logout_dashboard')->name('logout.dashboard');
Route::post('/admin/prosess_login', 'AuthController@prosess_login')->name('prosess.login');
Route::post('/masuk_dashboard', 'AuthController@masuk_dashboard')->name('masuk.dashboard');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin/user', 'UserController@index')->name('user.index');
    Route::get('/admin/user/datatable', 'UserController@datatable')->name('user.datatable');
    Route::get('/admin/user/parent_datatable', 'UserController@parent_datatable')->name('user_parent.datatable');
    Route::post('/admin/user', 'UserController@store')->name('user.store');
    Route::delete('/admin/user', 'UserController@destroy')->name('user.delete');
    Route::get('/admin/prokes_individu', 'ProkesIndividuController@index')->name('prokes.index');
    Route::get('/admin/datatable_individu', 'ProkesIndividuController@datatable_individu')->name('individu.datatable');
    Route::post('/admin', 'ProkesIndividuController@store')->name('prokes.store');
    Route::delete('/admin/prokes_individu', 'ProkesIndividuController@destroy')->name('individu.delete');
    Route::get('/admin/prokes_institusi', 'ProkesInstitusiController@index')->name('institusi.index');
    Route::get('/admin/datatable_institusi', 'ProkesInstitusiController@datatable_institusi')->name('institusi.datatable');
    Route::post('/admin/prokes_institusi', 'ProkesInstitusiController@store')->name('institusi.store');
    Route::delete('/admin/prokes_institusi', 'ProkesInstitusiController@destroy')->name('institusi.delete');
    Route::get('/admin/prokes_individu/dokumen', 'ProkesIndividuController@dokumen')->name('individu.dokumen');
    Route::post('/admin/upload_prokes_individu/dokumen', 'ProkesIndividuController@upload_dokumen_individu')->name('upload.dokumen_individu');
    Route::get('/admin/prokes_institusi/dokumen', 'ProkesInstitusiController@dokumen')->name('institusi.dokumen');
    Route::post('/admin/upload_prokes_institusi/dokumen', 'ProkesInstitusiController@upload_dokumen_institusi')->name('upload.dokumen_institusi');
    Route::get('/admin/get_desa', 'ProkesIndividuController@get_desa')->name('get.desa');
    Route::get('/admin/get_image_individu', 'ProkesIndividuController@get_image_individu')->name('get.image_individu');
    Route::get('/admin/get_image_institusi', 'ProkesInstitusiController@get_image_institusi')->name('get.image_institusi');
    Route::get('/admin/download_template', 'ProkesIndividuController@download_template')->name('download.template');
    Route::get('/admin/download_institusi', 'ProkesInstitusiController@download_institusi')->name('download.institusi');
    Route::get('/admin/report', 'ReportController@index')->name('report.index');
    Route::get('/admin/report/blank_page', 'ReportController@blank_page')->name('blank.page');
    Route::post('/admin/import_prokes', 'ProkesIndividuController@import_prokes')->name('import.prokes');
    Route::post('/admin/import_prokes_institusi', 'ProkesInstitusiController@import_prokes_institusi')->name('import.prokes_institusi');
    Route::get('/admin/download_report', 'ReportController@download_report')->name('download.report');
});
