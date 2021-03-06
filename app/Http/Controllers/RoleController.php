<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class RoleController extends Controller
{
    // 'manage_roles' => [
    //     'role_show',
    //     'role_create',
    //     'role_update',
    //     'role_detail',
    //     'role_delete'
    // ],

    public function __construct()
    {
        $this->middleware('permission:role_show', ['only' => 'index']);
        $this->middleware('permission:role_create', ['only' => ['create','store']]);
        $this->middleware('permission:role_update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role_detail', ['only' => 'show']);
        $this->middleware('permission:role_delete', ['only' => 'destroy']);
    }

    private $perPage = 5;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = $request->get('keyword') 
            ? Role::search($request->keyword)->paginate($this->perPage)
            : Role::paginate($this->perPage);

        return view('roles.index', [
            'roles' => $roles->appends(['keyword' => $request->keyword]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create', [
            'authorities' => config('permission.authorities'),
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
                'name' => 'required|string|max:50|unique:roles,name',
                'permissions' => 'required',
            ],
            [],
            $this->customAttributes(),
        );

        if($validator->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            // pivot table role_has_permission
            $role->givePermissionTo($request->permissions);

            Alert::success(
                trans('roles.alert.create.title'),
                trans('roles.alert.create.message.success'),
            );

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            DB::rollback();

            Alert::error(
                trans('roles.alert.create.title'),
                trans('roles.alert.create.message.error', ['error' => $th->getMessage()]),
            );

            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('roles.detail', [
            'role' => $role,
            'authorities' => config('permission.authorities'),
            'rolePermissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role' => $role,
            'authorities' => config('permission.authorities'),
            'checkedPermission' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50|unique:roles,name,'. $role->id,
                'permissions' => 'required',
            ],
            [],
            $this->customAttributes(),
        );

        if($validator->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $role->name = $request->name;
            // pivot table role_has_permission
            $role->syncPermissions($request->permissions);
            $role->save();

            Alert::success(
                trans('roles.alert.update.title'),
                trans('roles.alert.update.message.success'),
            );

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            DB::rollback();

            Alert::error(
                trans('roles.alert.update.title'),
                trans('roles.alert.update.message.error', ['error' => $th->getMessage()]),
            );

            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        // validate if role user still in used
        if(User::role($role->name)->count()) {
            Alert::warning(
                trans('roles.alert.delete.title'),
                trans('roles.alert.delete.message.warning', ['name' => $role->name]),
            );
            
            return redirect()->back();
        }

        // delete logic
        DB::beginTransaction();
        try {
            // remove role's permissions
            $role->revokePermissionTo($role->permissions->pluck('name')->toArray());
            $role->delete();

            Alert::success(
                trans('roles.alert.delete.title'),
                trans('roles.alert.delete.message.success'),
            );
        } catch (\Throwable $th) {
            DB::rollback();

            Alert::error(
                trans('roles.alert.delete.title'),
                trans('roles.alert.delete.message.error', ['error' => $th->getMessage()]),
            );
        } finally {
            DB::commit();
            return redirect()->back();
        }
    }

    private function customAttributes()
    {
        return [
            'name' => trans('roles.form.input.name.label'),
            'permissions' => trans('roles.form.input.permission.label'),
        ];
    }
}
