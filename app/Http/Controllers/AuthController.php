<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function login()
    {
        return view('auth.login');
    }

    public function prosess_login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->except(['_token']);
        Auth::attempt($credentials);
        if (Auth::check()) {
            $user = \App\Models\User::where('email', $request["email"])->first();
            if ($user) {
                return redirect(route('dashboard'));
            } else {
                return redirect(route('login'));
                // return redirect()->back()->withErrors(['Email is not valid']);
            }
        } else {
            return redirect(route('login'));
        }
    }

    public function masuk_dashboard(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->except(['_token']);
        Auth::attempt($credentials);
        if (Auth::check()) {
            $user = \App\Models\User::where('email', $request["email"])->first();
            $kecamatan = \App\Models\Kecamatan::where('code_kecamatan', $user->kode_kecamatan)->first();
            if ($user) {
                if ($kecamatan) {
                    // return redirect(route('individu.desa', ['kecamatan' => $user->kode_kecamatan, 'latitude' => $kecamatan->latitude, 'longitude' => $kecamatan->longitude]));
                    return '<a href="https://prokes.ciamiskab.go.id/admin">prokes.ciamiskab.go.id</a>';
                } else {
                    return redirect(route('prokes.individu'));
                }
            } else {
                return redirect(route('login.prokes'));
                // return redirect()->back()->withErrors(['Email is not valid']);
            }
        } else {
            return redirect(route('login.prokes'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }

    public function logout_dashboard()
    {
        Auth::logout();
        return redirect(route('login.prokes'));
    }
}
