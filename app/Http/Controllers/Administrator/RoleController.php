<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::withCount('users')->get();
            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $button = '<a href="' . route('role.form', encrypt($data->id)) . '" data-toggle="tooltip" title="Edit" data-original-title="Edit" class="edit btn btn-warning btn-sm"><i class="ri-settings-4-line"></i></a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<button data-toggle="tooltip" title="Delete" data-id="' . encrypt($data->id) . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.administrator.role.index');
    }

    public function form(string $id = null)
    {
        if ($id) $id = decrypt($id);
        $role = Role::with('permissions')->find($id);
        $permissions = Permission::all()->filter(function ($permission) {
            return !str_starts_with($permission->name, 'e-sign.') || in_array($permission->name, ['e-sign.menu', 'e-sign.profile']);
        });
        $permissionGroups = $this->groupPermissions($permissions);

        return view('pages.administrator.role.form', compact('role', 'permissions', 'permissionGroups'));
    }

    public function destroy(Request $request){
        // dd(decrypt($request->id));
        $post = Role::where('id', decrypt($request->id))->delete();
        return redirect()->route('role.index')->with('status','Delete Role Successfully');
    }

    // Define a function to group permissions (you can customize this logic)
    private function groupPermissions($permissions)
    {
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            // Extract the prefix and subprefix from the permission name
            $parts = explode('.', $permission->name);
            $prefix = $parts[0];
            $subprefix = count($parts) > 1 ? $parts[1] : '';

            // Group permissions by prefix and subprefix
            if (!isset($groupedPermissions[$prefix])) {
                $groupedPermissions[$prefix] = [];
            }

            if ($subprefix) {
                if (!isset($groupedPermissions[$prefix][$subprefix])) {
                    $groupedPermissions[$prefix][$subprefix] = [];
                }
                $groupedPermissions[$prefix][$subprefix][] = $permission;
            } else {
                $groupedPermissions[$prefix][] = $permission;
            }
        }

        return $groupedPermissions;
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->input();

            $role = Role::updateOrCreate(['id' => $data['id']], ['name' => $data['name']]);

            $role->syncPermissions($data['permissions']);

            DB::commit();

            return response()->json(['message' => "Role $role->name has been saved"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        $id = decrypt($id);

        //
    }
}
