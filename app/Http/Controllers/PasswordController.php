<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\PasswordBroker;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\Employee;
use App\Models\User;
use Auth;
use App\Models\Log;

class PasswordController extends Controller
{
    use Notifiable;
    public function index(){
        if (Auth::user()->can('emp.menu')) {
            $user = auth()->user();
            return view('updatepassword', compact('user'));
        }else{
            return view('resetpassword');
        }
    }

    public function index_password(){
        return view('indexpassword');
    }

    public function update_password_year(Request $request){
        $user = auth()->user();
        $date_now  = 'Y-m-d';
        $date_last = date('Y-m-d', strtotime('-1 year'));

        $request->user()->update([
            'password' => Hash::make($request->get('password')),
            'last_update_password' => date('Y-m-d')
        ]);
        if(!empty($user->employee->fullname)){
            $nama_user = $user->employee->fullname;
        }else{
            if($user->name == 'Security HPI'){
                $nama_user = 'Security';
            }else{
                $nama_user = 'Dokter';
            }
        }
        //insert log reset password
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'user '.'"'.$nama_user.'" '.' reset password';
        $insert->save();

        if(empty($user->last_update_password)){
            return redirect()->route('password.index')->with('status','Reset Password Successfully');
        }else{
            if($user->last_update_password < $date_now && $user->last_update_password > $date_last){
                return redirect()->route('home')->with('status','Reset Password Successfully');
            }else{
                return redirect()->route('password.index')->with('status','Reset Password Successfully');
            }
        }
    }

    public function update(Request $request){
        $request->user()->update([
            'password' => Hash::make($request->get('password')),
            'last_update_password' => date('Y-m-d')
        ]);
        $user = auth()->user();
        //insert log reset password
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'user '.'"'.$user->employee->fullname.'" '.' reset password';
        $insert->save();

        return redirect()->route('user.password.index')->with('status','Reset Password Successfully');
    }

    //forgot password
    public function reset_password_email(Request $request){
        $employee = Employee::where('email', $request->email)->first();
        if(!empty($employee->email)){
            $user = User::select('id', 'name', 'email_verified_at', 'password', 'remember_token', 'status', 'employee_id', 'count_log', 'last_update_password', 'created_at', 'updated_at')->where('employee_id', $employee->id)->first();
            $user['email'] = $employee->email;
            $password_broker = app(PasswordBroker::class); //so we can have dependency injection
            $token = $password_broker->createToken($user); //create reset password token
            //send email
            if(!empty($employee->email)){
                $user->notify(new ResetPasswordNotification($token));
            }
            $post = User::where('id', $user->id)->update(['email' => $employee->email]);
            return back()->with('status','Password reset link has been sent to your email address');
        }else{
            return back()->with('error',' Your email address not found');
        }
    }
}
