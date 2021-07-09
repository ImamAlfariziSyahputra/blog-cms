<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index', [
            'users' => User::all(),
        ]);
    }

    public function selectInput(Request $request)
    {
        $users = Role::select('id', 'name')->limit(6)->get();

        if ($request->has('q')) {
            $users = Role::select('id', 'name')
                ->where('name', 'LIKE', "%{$request->q}%")
                ->limit(6)
                ->get();
        }

        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create', [
            'users' => User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
            'name' => 'required|string|min:3|max:40',
            'role' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:3|confirmed',
            ],
            [],
            $this->customAttributes(),
        );

        if($validator->fails()) {
            $request['role'] = Role::select('id', 'name')->find($request->role);
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    private function customAttributes()
    {
        return [
            'name' => trans('users.form.input.name.attribute'),
            'role' => trans('users.form.select.role.attribute'),
            'email' => trans('users.form.input.email.attribute'),
            'password' => trans('users.form.input.password.attribute'),
        ];
    }
}
