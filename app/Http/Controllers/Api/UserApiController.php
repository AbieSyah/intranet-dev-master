<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Employee;

class UserApiController extends Controller
{
    public function index()
    {
        $users = User::select('id','name','email')->get();
        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'data' => $users
        ]);

        // $permissions = User::first()->getAllPermissions()
        //     ->filter(function ($permission) {
        //         return str_starts_with($permission->name, 'mobile.');
        //     })
        //     ->values();

        // return response()->json([
        //     'success' => true,
        //     'count'   => $permissions->count(),
        //     'data'    => $permissions
        // ]);

        // $employees = Employee::with([
        //     'department',
        //     'area',
        //     'section',
        //     'position',
        //     'level',
        //     'building'
        // ])->get();


        // return response()->json([
        //     'success' => true,
        //     'data' => $employees
        // ]);
    }

    public function show($id)
    {
        $employee = Employee::with([
            'department',
            'area',
            'section',
            'position',
            'level',
            'building'
        ])->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created',
            'data' => $user
        ], 201);
    }
}

