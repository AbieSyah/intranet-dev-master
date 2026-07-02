<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Log;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $users = User::where('status', '1')->with('employee')->orderBy('name', 'asc')->get();

        return view('auth.login', compact('users'));
    }

    function authenticated(Request $request, $user)
    {
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'login';
        if(!empty($user->employee->fullname)){
            $insert->description = 'user '.'"'.$user->employee->fullname.'" '.' login';
        }else{
            $insert->description = 'user '.'"'.$user->name.'" '.' login';
        }
        $insert->save();

        $query = User::where('id', $user->id)->first();
        if(!empty($query->count_log)){
            $total = $query->count_log+1;
            $update_user = User::where('id', $user->id)->update(['count_log' => $total]);
        }else{
            $update_user = User::where('id', $user->id)->update(['count_log' => 1]);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'logout';
        if(!empty($user->employee->fullname)){
            $insert->description = 'user '.'"'.$user->employee->fullname.'" '.' logout';
        }else{
            $insert->description = 'user '.'"'.$user->name.'" '.' logout';
        }
        $insert->save();
        //disclaimer reset
        if(!empty($user->employee->fullname)){
            $post = User::where('id', $user->id)->update(['disclaimer' => 0]);
        }else{
            if($user->name == 'Security HPI'){
                $post = User::where('id', $user->id)->update([
                    'disclaimer' => 0,
                    'employee_id' => null,
                    'name' => 'Security HPI'
                ]);
            }else{
                $post = User::where('id', $user->id)->update([
                    'disclaimer' => 0,
                    'employee_id' => null,
                    'name' => 'DOCTOR'
                ]);
            }
        }
        Auth::guard('web')->logout();

        return redirect()->route('login');
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
}
