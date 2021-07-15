<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private $perPage = 5;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = [];
        if($request->get('keyword')) {
            $users = User::search($request->keyword)->paginate($this->perPage);
        } else {
            $users = User::paginate($this->perPage);
        }
        return view('users.index', [
            'users' => $users->withQueryString(),
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

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'role' => $request->role,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);

            Alert::success(
                trans('users.alert.create.title'),
                trans('users.alert.create.message.success'),
            );

            return redirect()->route('users.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('users.alert.create.title'),
                trans('users.alert.create.message.error', ['error' => $th->getMessage()]),
            );

            $request['role'] = Role::select('id', 'name')->find($request->role);
            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
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
        return view('users.edit', [
            'user' => $user,
            'selectedRole' => $user->roles->first(),
        ]);
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
        $validator = Validator::make(
            $request->all(),
            [
            'role' => 'required',
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

        DB::beginTransaction();
        try {
            $user->update([
                'role' => $request->role,
            ]);
            $user->syncRoles($request->role);

            Alert::success(
                trans('users.alert.update.title'),
                trans('users.alert.update.message.success'),
            );

            return redirect()->route('users.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('users.alert.update.title'),
                trans('users.alert.update.message.error', ['error' => $th->getMessage()]),
            );

            $request['role'] = Role::select('id', 'name')->find($request->role);
            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();

        try {
            $user->removeRole($user->roles->first());

            $user->delete();

            Alert::success(
                trans('users.alert.delete.title'),
                trans('users.alert.delete.message.success'),
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('users.alert.delete.title'),
                trans('users.alert.delete.message.error', ['error' => $th->getMessage()]),
            );
        } finally {
            DB::commit();
            return redirect()->back();
        }

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
