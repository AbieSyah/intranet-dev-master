<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;

class EmployeeApiController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }
}
