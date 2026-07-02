<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Permission::with('roles')->latest()->get();
            return DataTables::of($query)

                ->addColumn('action', function ($data) {
                    $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    return $button;
                })

                ->addColumn('roles', function ($data) {
                    return $data->roles->pluck('name')->implode(', ');
                })
                ->addIndexColumn()
                ->make(true);
        }

        $roles = Role::all();

        return view('pages.administrator.permission.index', compact('roles'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->input('id');
            $name = $request->input('name');
            $roles = $request->input('roles');

            $permission = Permission::updateOrCreate(['id' => $id], ['name' => $name]);

            if ($roles) {
                $permission->syncRoles($roles);
            }

            DB::commit();

            return response()->json(['message' => "Permission $permission->name has been saved"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $id = decrypt($request->input('id'));
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        return response()->json($permission);
    }
}
