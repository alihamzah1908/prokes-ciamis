<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Auth::user());
        $data["user"] = \App\Models\User::where('role', '!=', 'super admin')
            ->where('id', Auth::user()->id)
            ->get();
        return view('admin/user.index', $data);
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
        if ($request["id"]) {
            $user = \App\Models\User::findOrFail($request["id"]);
        } else {
            $user = new \App\Models\User();
            $user->password = Hash::make($request["password"]);
            $user->email = $request["email"];
        }
        $user->parent_admin = $request["id_user"];
        $user->name = $request["nama"];
        $user->role = $request["role"];
        $user->save();
        return redirect(route('user.index'));
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
    public function destroy(Request $request)
    {
        $user = \App\Models\User::where('id', $request["id"])->first();
        if ($user) {
            $user_parent = \App\Models\User::where('parent_admin', $request["id"])->first();
            $user->delete();
            $user_parent->delete();
            return response()->json(["user" => 'success', "user_parent" => 'success']);
        } else {
            return redirect(route('user.index'));
        }
    }

    public function datatable()
    {
        {
            if (Auth::user()->role == 'super admin') {
                $data = \App\Models\User::where('role', '!=', 'super admin')
                    ->where('parent_admin', '=', null)
                    ->get();
            } else {
                $data = \App\Models\User::where('role', '!=', 'super admin')
                    ->where('id', Auth::user()->id)
                    ->get();
            }
            return Datatables::of($data)
                ->addColumn('aksi', function ($val) {
                    if (Auth::user()->role == 'super admin') {
                        return '<div class="dropdown">
                                    <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">Aksi</button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item add-staff" data-bind="' . $val->id . '" href="javascript:void(0)">Tambah Staff</a>
                                        <a class="dropdown-item edit" data-bind=\'' . $val . '\' role="presentation" href="javascript:void(0)" data-toggle="modal">Edit</a>
                                        <a class="dropdown-item delete" data-bind="' . $val->id . '" role="presentation" href="javascript:void(0)">Hapus</a>
                                    </div>
                                </div>';
                    }
                    if (Auth::user()->role == 'Admin') {
                        return '<div class="dropdown">
                                    <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">Aksi</button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item add-staff" data-bind="' . $val->id . '" href="javascript:void(0)">Tambah Staff</a>
                                    </div>
                                </div>';
                    }
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function parent_datatable(Request $request)
    {
        $data = \App\Models\User::where('role', '!=', 'super admin')
            ->where('parent_admin', $request["id"])
            ->get();
        return Datatables::of($data)
            ->addColumn('aksi', function ($val) {
                return '<div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">Aksi</button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item edit" data-bind=\'' . $val . '\' role="presentation" href="javascript:void(0)" data-toggle="modal">Edit</a>
                                    <a class="dropdown-item delete" data-bind="' . $val->id . '" role="presentation" href="javascript:void(0)">Hapus</a>
                                </div>
                            </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
