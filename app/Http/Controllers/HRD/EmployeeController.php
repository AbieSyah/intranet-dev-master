<?php

namespace App\Http\Controllers\HRD;

use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Imports\EmployeeImport;
use Illuminate\Support\Facades\File;
use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\Section;
use App\Models\Position;
use App\Models\Level;
use App\Models\Log;
use App\Models\Master\Building;
use App\Models\Master\Contract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Employee::query()
                ->with(['section', 'area', 'department', 'position', 'level']);
            if (!empty($request->form_status) && $request->form_status != 'ALL') {
                $query->where('status', $request->form_status);
            } else {
                $query->where('status', '!=', 'TERMINATED');
            }
            return DataTables::of($query)
                ->addColumn('area_kode', function ($data) {
                    return $data->area->kode ?? '-';
                })
                ->addColumn('department_name', function ($data) {
                    return $data->department->name ?? '-';
                })
                ->addColumn('section_nama', function ($data) {
                    return $data->section->nama ?? '-';
                })
                ->addColumn('position_nama', function ($data) {
                    return $data->position->nama ?? '-';
                })
                ->addColumn('service_year', fn($employee) => $employee->service_years)
                ->filterColumn('service_year', function($query, $keyword) {
                    $sql = "CONCAT(
                        TIMESTAMPDIFF(YEAR, joindate, IF(status = 'TERMINATED' AND enddate IS NOT NULL, enddate, NOW())), 
                        ' Years ', 
                        MOD(TIMESTAMPDIFF(MONTH, joindate, IF(status = 'TERMINATED' AND enddate IS NOT NULL, enddate, NOW())), 12), 
                        ' Months'
                    ) LIKE ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('service_year', function ($query, $order) {
                    $query->orderByRaw('DATEDIFF(IF(status = "TERMINATED" AND enddate IS NOT NULL, enddate, NOW()), joindate) ' . $order);
                })
                ->orderColumn('area.kode', function ($query, $order) {
                    $query->orderByRaw("(SELECT kode FROM areas WHERE areas.id = employees.area_id) $order");
                })
                ->orderColumn('department.name', function ($query, $order) {
                    $query->orderByRaw("(SELECT name FROM departments WHERE departments.id = employees.department_id) $order");
                })
                ->orderColumn('position.nama', function ($query, $order) {
                    $query->orderByRaw("(SELECT nama FROM master_position WHERE master_position.id = employees.position_id) $order");
                })
                ->orderColumn('section.nama', function ($query, $order) {
                    $query->orderByRaw("(SELECT nama FROM master_section WHERE master_section.id = employees.section_id) $order");
                })
                ->addColumn('status', function ($data) {
                    if ($data->status == 'PERMANENT') {
                        return '<span class="badge text-bg-success">'.$data->status.'</span>';
                    } elseif ($data->status == 'PROBATION') {
                        return '<span class="badge text-bg-secondary">'.$data->status.'</span>';
                    } elseif ($data->status == 'CONTRACT') {
                        return '<span class="badge text-bg-primary">'.$data->status.'</span>';
                    } elseif ($data->status == 'OUTSOURCING') {
                        return '<span class="badge text-bg-info">'.$data->status.'</span>';
                    } else {
                        return '<span class="badge text-bg-danger">'.$data->status.'</span>';
                    }
                })
                ->addColumn('avatar', function ($data) {
                    $avatar = !empty($data['avatar']) && Storage::disk('public')->exists('avatars/' . $data['avatar']) 
                              ? $data['avatar'] : 'user.jpg';
                    $url = asset('storage/avatars/' . $avatar);
                    return '<div class="avatar-group"><a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Avatar"><img src="' . $url . '" alt="" class="rounded-circle avatar-xxs"></a></div>';
                })
                ->addColumn('action', function ($data) {
                    $btn = '';
                    if(Auth::user()->can('hrd.employee.update')){
                        $btn .= '<a href="' . route('employee.form', encrypt($data['id'])) . '" class="btn btn-warning btn-sm edit-btn me-1"><i class="ri-quill-pen-line"></i></a>';
                    }
                    if(Auth::user()->can('hrd.employee.detail')){
                        $btn .= '<a href="' . route('employee.detail', encrypt($data['id'])) . '" class="btn btn-info btn-sm edit-btn me-1"><i class="ri-eye-2-line"></i></a>';
                    }
                    if(Auth::user()->can('hrd.employee.read')){
                        $btn .= '<a href="' . route('employee.milestone.index', encrypt($data['id'])) . '" class="btn btn-success btn-sm edit-btn"><i class="ri-profile-line"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'avatar'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.employee.index');
    }

    public function form(string $id = null)
    {
        if ($id) $id = decrypt($id);
        $employee = Employee::find($id);
        $areas = Area::orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();
        $sections = Section::orderBy('nama', 'asc')->get();
        $positions = Position::orderBy('nama', 'asc')->get();
        $levels = Level::orderBy('nama', 'asc')->get();
        $buildings = Building::orderBy('nama', 'asc')->get();
        $contracts = Contract::orderBy('name', 'asc')->get();
        $path = storage_path('app/city-list.json');
        $city_data = json_decode(file_get_contents($path), true);
        $city = collect($city_data)->sortBy('nama')->values()->all();
        return view('pages.hrd.employee.form', compact('employee', 'areas', 'departments','city','sections','positions','levels','buildings','contracts'));
    }

    public function detail($id){
        $id = decrypt($id);
        $employee = Employee::find($id);
        $areas = Area::all();
        $departments = Department::all();
        $sections = Section::all();
        $positions = Position::all();
        $levels = Level::all();
        $buildings = Building::all();
        $contracts = Contract::all();
        $countEval = Evaluation::where('employee_id', $id)->where('status', 'DONE')->count();
        //city
        $path = storage_path('app/city-list.json');
        $city = json_decode(file_get_contents($path), true);
        return view('pages.hrd.employee.detail', compact('employee', 'areas', 'departments','city','sections','positions','levels','buildings','contracts','countEval'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'      => ['required', 'string', Rule::unique('employees', 'nik')->ignore($request->id)],
        ],[
            'nik.unique'        => 'NIK is already registered.',
            'nik.required'      => 'NIK is required.',
        ]);
        DB::beginTransaction();
        try {
            $employeeId = $request->id;
            $isNew      = empty($employeeId);
            $employee = $isNew ? new Employee() : Employee::findOrFail($employeeId);
            $avatarPath = $employee->avatar;
            if (!empty($request->image_base64)) {
                $this->deleteAvatar($employee->avatar);
                $avatarPath = $this->storeBase64($request->image_base64);
            } elseif ($request->remove_file == 1) {
                $this->deleteAvatar($employee->avatar);
                $avatarPath = null;
            }
            $data = $request->except(['image_base64', 'remove_file', 'image', 'id']);
            $data['avatar'] = $avatarPath;
            $employee->fill($data);
            $employee->save();
            $action = $isNew ? 'insert' : 'update';
            $desc   = ($isNew ? 'Create New Employee "' : 'Modify Employee "') . $employee->fullname . '"';
            Log::create([
                'user_id'     => auth()->id(),
                'ip_address'  => $request->ip(),
                'action'      => $action,
                'description' => $desc
            ]);
            DB::commit();
            return response()->json([
                'message'  => "Employee {$employee->fullname} has been saved",
                'redirect' => route('employee.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Something went wrong!',
                'errors'  => ['system' => ['An unexpected error occurred. Please try again.'.$e->getMessage()]], 
            ], 500);
        }
    }

    private function deleteAvatar($filename)
    {
        if (!empty($filename)) {
            $path = storage_path('app/public/avatars/' . $filename);
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    private function storeBase64($imageBase64)
    {
        list($type, $imageBase64) = explode(';', $imageBase64);
        list(, $imageBase64)      = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);
        $imageName= time().'.png';
        $path = storage_path() . "/app/public/avatars/" . $imageName;
        file_put_contents($path, $imageBase64); 
        return $imageName;
    }

    public function exportExcel(Request $request) 
    {
        $fileName = 'Employee_Intranet_' . date('Y-m-d_His') . '.xlsx';
        $formStatus = $request->get('form_status', 'ALL');
        return Excel::download(new EmployeeExport($formStatus), $fileName);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_xls' => 'required|mimes:xlsx,xls'
        ]);
        DB::beginTransaction();
        try {
            $import = new EmployeeImport();
            Excel::import($import, $request->file('file_xls'));
            $importedCount = $import->importedCount;
            if ($importedCount <= 0) {
                DB::rollback();
                return response()->json([
                    'message' => 'File is empty or only contains headers'
                ], 422);
            }
            Log::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'action' => 'import',
                'description' => 'Import ' . $importedCount . ' Employee(s) from Excel',
            ]);
            DB::commit();
            return response()->json([
                'message' => $importedCount . ' Employee(s) was successfully imported',
                'redirect' => route('employee.index')
            ], 200);
        } catch (ValidationException $e) {
            DB::rollback();
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $attributeName = strtolower($failure->attribute());
                $rowNumber = $failure->row();
                $errorMessages = $failure->errors();
                $value = $failure->values()[$attributeName] ?? '';
                $columnIndex = array_search($attributeName, array_map('strtolower', array_keys($failure->values())));
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
                $errorString = "Column " . $columnLetter . $rowNumber . " : " . implode(", ", $errorMessages);
                if (!empty($value)) {
                    $errorString .= " [" . $value . "]";
                }
                $errors[] = $errorString;
            }
            return response()->json([
                'message' => 'The following data is invalid',
                'responseText' => $errors,
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'An error occurred during the import process',
                'responseText' => $e->getMessage(),
            ], 500);
        }
    }
}
