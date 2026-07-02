<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\News;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;


class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     $user = User::with([
    //         'employee.department',
    //         'employee.area',
    //         'employee.section',
    //         'employee.position',
    //         'employee.level',
    //         'employee.building'
    //     ])
    //     ->where('email', $request->email)
    //     ->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => 'Email atau password salah'
    //         ], 401);
    //     }

    //     // hapus token lama
    //     $user->tokens()->delete();

    //     // buat token baru
    //     $token = $user->createToken('mobile_token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login berhasil',
    //         'token'   => $token,
    //         'user'    => [
    //             'id'    => $user->id,
    //             'name'  => $user->name,
    //             'email' => $user->email,
    //         ]
    //     ]);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        if ($user->tokens()->exists()) {
            return response()->json([
                'message' => 'Akun ini masih login di perangkat lain. Silakan logout terlebih dahulu.'
            ], 403);
        }

         $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        Cache::put('otp_' . $user->email, $otp, now()->addMinutes(30));

        Mail::raw("Hello,\n\nHere is your verification code: $otp\nThis code will expire in 30 minutes.\nDo not give this code to anyone.", function ($message) use ($user, $otp) {
            $message->to($user->email)->subject("Verification OTP: $otp");
        });

        return response()->json(['message' => 'OTP telah dikirim ke email']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:4'
        ]);

        // Ambil OTP dari cache
        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 400);
        }

        // Hapus OTP dari cache setelah digunakan
        Cache::forget('otp_' . $request->email);

        $user = User::where('email', $request->email)->first();

        if ($user->tokens()->exists()) {
            return response()->json([
                'message' => 'Anda masih memiliki sesi aktif di perangkat lain. Silakan logout terlebih dahulu.'
            ], 403);
        }

        // Buat token baru
        $token = $user->createToken('mobile_token')->plainTextToken;

        // Cek apakah user baru saja reset password (ada di cache)
        $mustChangePassword = Cache::pull('temp_password_' . $user->id) ? true : false;

        // Muat relasi employee
        $user->load([
            'employee.department',
            'employee.area',
            'employee.section',
            'employee.position',
            'employee.level',
            'employee.building'
        ]);

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

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'employee' => $user->employee
            ],
            'must_change_password' => $mustChangePassword
        ]);
    }

    public function showAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user->employee->avatar) {
            return response()->json([
                'message' => 'Avatar tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/avatars/' . $user->employee->avatar);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File avatar tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048' // max 2MB
        ]);

        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
        }

        if ($employee->avatar && Storage::disk('public')->exists('avatars/' . $employee->avatar)) {
            Storage::disk('public')->delete('avatars/' . $employee->avatar);
        }

        $file = $request->file('avatar');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $fileName, 'public');

        $employee->avatar = $fileName;
        $employee->save();

        return response()->json([
            'message' => 'Avatar berhasil diperbarui',
            'avatar_url' => url('/api/me/avatar')
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $user = auth()->user();

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

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = $request->user();

        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->delete();

        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'reset_password';
        $insert->description = "User {$user->email} reset password and logged out from all devices";
        $insert->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login kembali.'
        ]);
    }

    public function requestForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Jika email terdaftar, OTP akan dikirim'
            ]);
        }

        // Generate OTP
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Simpan ke cache
        Cache::put('reset_otp_' . $user->email, $otp, now()->addMinutes(10));

        // Kirim email
        Mail::raw("Hello,\n\nHere is your verification code reset password: $otp\nThis code will expire in 30 minutes.\nDo not give this code to anyone.", function ($message) use ($user, $otp) {
            $message->to($user->email)->subject("Verification OTP Reset Password: $otp");
        });

        return response()->json([
            'message' => 'Jika email terdaftar, OTP akan dikirim'
        ]);
    }

    public function verifyForgotOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|size:4'
        ]);

        $cachedOtp = Cache::get('reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'message' => 'OTP tidak valid atau kadaluarsa'
            ], 400);
        }

        // Tandai bahwa user boleh reset password
        Cache::put('reset_verified_' . $request->email, true, now()->addMinutes(10));

        return response()->json([
            'message' => 'OTP valid'
        ]);
    }

    // Tambahkan dua method baru di dalam AuthController

public function requestForceLogout(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // Tetap balas sukses agar tidak ada email enumeration
        return response()->json(['message' => 'Jika email terdaftar, OTP akan dikirim']);
    }

    // Generate OTP 4 digit
    $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Simpan ke cache dengan masa berlaku 10 menit
    Cache::put('force_logout_otp_' . $user->email, $otp, now()->addMinutes(10));

    // Kirim email
    Mail::raw("Hello,\n\nAnda meminta untuk menghapus semua sesi login di semua perangkat.\nKode verifikasi Anda: $otp\nKode ini berlaku 10 menit.\nJika Anda tidak merasa melakukan permintaan ini, abaikan email ini.", function ($message) use ($user, $otp) {
        $message->to($user->email)
                ->subject("Verifikasi Hapus Semua Sesi: $otp");
    });

    return response()->json(['message' => 'Jika email terdaftar, OTP akan dikirim']);
}

public function verifyForceLogout(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|string|size:4'
    ]);

    $cachedOtp = Cache::get('force_logout_otp_' . $request->email);

    if (!$cachedOtp || $cachedOtp != $request->otp) {
        return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }

    // Hapus semua token (semua perangkat)
    $user->tokens()->delete();

    // Hapus cache OTP
    Cache::forget('force_logout_otp_' . $request->email);

    // Catat log
    $log = new Log;
    $log->user_id = $user->id;
    $log->ip_address = $request->ip();
    $log->action = 'force_logout_all';
    $log->description = "User {$user->email} menghapus semua sesi dari perangkat lain";
    $log->save();

    return response()->json(['message' => 'Semua sesi berhasil dihapus. Silakan login kembali.']);
}

    public function resetNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        $isVerified = Cache::get('reset_verified_' . $request->email);

        if (!$isVerified) {
            return response()->json([
                'message' => 'OTP belum diverifikasi'
            ], 403);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus semua cache terkait
        Cache::forget('reset_otp_' . $request->email);
        Cache::forget('reset_verified_' . $request->email);

        // Logout semua device
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password berhasil direset, silakan login'
        ]);
    }

    // private function generateRandomPassword($length = 8)
    // {
    //     $lower = 'abcdefghijklmnopqrstuvwxyz';
    //     $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //     $numbers = '0123456789';

    //     // Pastikan masing-masing karakter muncul minimal sekali
    //     $password = '';
    //     $password .= $lower[random_int(0, strlen($lower) - 1)];
    //     $password .= $upper[random_int(0, strlen($upper) - 1)];
    //     $password .= $numbers[random_int(0, strlen($numbers) - 1)];

    //     // Gabungkan semua karakter
    //     $all = $lower . $upper . $numbers;

    //     // Isi sisa karakter hingga mencapai panjang yang diinginkan
    //     for ($i = 3; $i < $length; $i++) {
    //         $password .= $all[random_int(0, strlen($all) - 1)];
    //     }

    //     // Acak urutan karakter
    //     return str_shuffle($password);
    // }

    // public function forgotPassword(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Jika email terdaftar, password baru akan dikirim.'
    //         ]);
    //     }

    //     // Generate password random
    //     $newPassword = $this->generateRandomPassword(8);

    //     // Update password
    //     $user->password = Hash::make($newPassword);
    //     $user->save();

    //     // Hapus token lama (opsional)
    //     $user->tokens()->delete();

    //     // Simpan ke cache bahwa user ini baru saja reset password (berlaku 24 jam)
    //     Cache::put('temp_password_' . $user->id, true, now()->addHours(24));

    //     // Kirim email berisi password baru
    //     Mail::raw(
    //         "Hallo,\n\n" .
    //         "Password Anda telah di-reset. Berikut adalah password baru Anda:\n\n" .
    //         "$newPassword\n\n" .
    //         "Segera login dan ganti password Anda.\n\n" .
    //         "Jika Anda tidak meminta reset ini, segera hubungi administrator.",
    //         function ($message) use ($user) {
    //             $message->to($user->email)
    //                     ->subject('Reset Password - Password Baru Anda');
    //         }
    //     );

    //     return response()->json([
    //         'message' => 'Jika email terdaftar, password baru akan dikirim.'
    //     ]);
    // }

    public function loginDirect(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }


        if ($user->tokens()->exists()) {
            return response()->json([
                'message' => 'Anda masih memiliki sesi aktif di perangkat lain. Silakan logout terlebih dahulu.'
            ], 403);
        }

        // Hapus token lama (opsional, sesuai kebijakan)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('mobile_token')->plainTextToken;

        // Muat relasi employee
        $user->load([
            'employee.department',
            'employee.area',
            'employee.section',
            'employee.position',
            'employee.level',
            'employee.building'
        ]);

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

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'employee' => $user->employee
            ]
        ]);
    }

}
