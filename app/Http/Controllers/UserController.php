<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use App\Notifications\AccountNotification;
use Illuminate\Auth\Passwords\PasswordBroker;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::get();
        foreach ($users as $user) {
            $index = $user->id;
            $data[$index] = array();
            $data[$index]['id'] = $user->id;
            $data[$index]['name'] = $user->employee->fullname ?? $user->name;
            $data[$index]['department'] = $user->employee->department->name ?? '-';
            $data[$index]['area'] = $user->employee->area->kode ?? '-';
            $data[$index]['email'] = $user->email;
            $data[$index]['role'] = $user->getRoleNames()->first();
            $data[$index]['status'] = $user->status;
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    if ($data['status'] == 1) return '<span class="badge text-bg-success">Active</span>';
                    if ($data['status'] == 0) return '<span class="badge text-bg-danger">Inactive</span>';
                })
                ->addColumn('role', function ($data) {
                    return $data['role'];
                })
                ->addColumn('action', function ($data) {
                    // return '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-sm edit-btn"><i class="ki-outline ki-notepad-edit fs-5"></i></button>';
                    return '<button data-toggle="tooltip" title="Edit"  data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                })
                ->rawColumns(['status', 'action','role'])
                ->addIndexColumn()
                ->make(true);
        }

        // $employees = Employee::whereIn('status', ['ACTIVE','PERMANENT','CONTRACT','PROBATION'])->with('department')->get();
        $employees = Employee::with('department')->get();
        $roles = Role::all();

        return view('pages.administrator.user.index', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->input();
            if(!empty($data['password'])){
                if($data['cek_email'] == 'yes'){
                    // $code = random_int(100000, 999999);
                    // $password = Hash::make($code);
                    $user = User::updateOrCreate(['id' => $data['id']], [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'employee_id' => $data['employee_id'],
                        'status' => $data['cek_status'],
                        // 'status' => isset($data['status']) ? 1 : 0,
                        'password' => Hash::make($data['password'])
                        // 'password' => $password
                    ]);

                    // $employee = Employee::where('id', $data['employee_id'])->first();
                    if(!empty($user->email)){
                        $password_broker = app(PasswordBroker::class); //so we can have dependency injection
                        $token = $password_broker->createToken($user);
                        $user->notify(new ResetPasswordNotification($token));
                    }
                }else{
                    $user = User::updateOrCreate(['id' => $data['id']], [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'employee_id' => $data['employee_id'],
                        'status' => $data['cek_status'],
                        // 'status' => isset($data['status']) ? 1 : 0,
                        'password' => Hash::make($data['password'])
                    ]);
                }
            }else{
                if($data['cek_email'] == 'yes'){
                    $code = random_int(100000, 999999);
                    $password = Hash::make($code);
                    $user = User::updateOrCreate(['id' => $data['id']], [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'employee_id' => $data['employee_id'],
                        'status' => $data['cek_status'],
                        'password' => $password
                    ]);
                    // $employee = Employee::where('id', $data['employee_id'])->first();
                    if(!empty($user->email)){
                        $password_broker = app(PasswordBroker::class); //so we can have dependency injection
                        $token = $password_broker->createToken($user);
                        $user->notify(new ResetPasswordNotification($token));
                    }
                }else{
                    $user = User::updateOrCreate(['id' => $data['id']], [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'employee_id' => $data['employee_id'],
                        'status' => $data['cek_status']
                        // 'password' => $password,
                    ]);
                }                
            }
            // $user->removeRole();
            

            $user->syncRoles($data['role_id']);

            DB::commit();

            return response()->json(['message' => "User $user->name telah disimpan"], 200);
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
        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function form(Request $request){
        $users = User::get();
        foreach ($users as $user) {
            if(!empty($user->employee_id)){
                $data[] = $user->employee_id;
            }
        }
        // dd($data);
        $employees = Employee::whereNotIn('status', ['TERMINATED'])->whereNotIn('id', $data)->orderBy('nik', 'asc')->get();
        return view('pages.administrator.user.form', compact('employees'));
    }

    public function multiple_store(Request $request){
        $user = auth()->user();
        if(empty($request->employee)){
            return redirect()->route('user.form')->with('error', 'The input form selected employees is empty, please check again.');
        }else{
            $employees = $request->employee;
            // dd($employees);
            for($i = 0; $i < count($employees); $i++){
                $employee = Employee::where('id', $employees[$i])->first();
                do {
                    $code = random_int(100000, 999999);
                    $password = Hash::make($code);
                } while (User::where("password", "=", $password)->first());
                
                $insert = [
                    'name' => $employee->fullname,
                    'email' => $employee->email,
                    'password' => $password,
                    'status' => 1,
                    'employee_id' => $employee->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $post = User::updateOrCreate(['employee_id' => $employees[$i]],$insert);
                $post->assignRole('4');
                // $users = User::where('employee_id', $employee->id)->first();
                //notification mail
                if(!empty($post->email)){
                    $details = [
                        'greeting' => 'Hi '.$employee->fullname,
                        'subject' => 'Account Intranet Hisamitsu Pharma Indonesia',
                        'body' => 'Ingin Menginformasikan bahwa akun intranet anda telah di buat silahkan masukkan kata sandi sebagai berikut : '.$code,
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                    //send mail
                    $post->notify(new AccountNotification($details));
                }
            }
            
            return redirect()->route('user.form')->with('success', 'Create Account Successfully.');
        }
    }

    // public function generateUniqueCode()
    // {
    //     do {
    //         $code = random_int(100000, 999999);
    //         $password = Hash::make($code)
    //     } while (User::where("password", "=", $password)->first());
  
    //     return $code;
    // }

    // public function profile_index()
    // {
    //     $user = User::with('employee.department', 'employee.area')->findOrFail(Auth::user()->id);
    //     return view('pages.profile.index', compact('user'));
    // }

    // public function profile_store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $user = User::findOrFail(Auth::user()->id);
    //         $data = $request->input();

    //         $user->update([
    //             'name' => $data['name'],
    //             'password' => Hash::make($data['password']),
    //         ]);

    //         if ($request->hasFile('avatar')) {
    //             $avatar = $request->file('avatar');
    //             $avatarName = 'avatar_' . $request->user()->id . '.jpeg';
    
    //             // Store the uploaded file with the unique filename in the "avatars" directory
    //             $path = $avatar->storeAs('avatars', $avatarName, 'public');
    //         }

    //         return view('pages.profile.index', compact('user'));
    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //     }
    // }
}
