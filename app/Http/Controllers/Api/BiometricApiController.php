<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use ParagonIE\Sodium\Compat as Sodium;
use App\Models\Log;

class BiometricApiController extends Controller
{
    /**
     * Generate challenge (user harus login).
     */
    public function challenge(Request $request)
    {
        $user = $request->user();

        $challenge = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        Cache::put(
            'biometric_challenge_' . $user->id,
            $challenge,
            now()->addMinutes(5)
        );

        return response()->json([
            'challenge' => $challenge
        ]);
    }

    /**
     * Register public key.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'public_key' => 'nullable|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->biometric_key = $request->public_key ?: null;
        $user->biometric_device_id = $request->device_id;
        $user->save();

        $description = $request->public_key
            ? "User {$user->employee->fullname} registered biometric key"
            : "User {$user->employee->fullname} registered device (biometric not supported)";

        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'biometric_register';
        $insert->description = $description;
        $insert->save();

        return response()->json([
            'message' => $description
        ]);
    }

    /**
     * Verify biometric signature.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user->biometric_key) {
            return response()->json([
                'error' => 'Biometric not registered'
            ], 400);
        }

        $cacheKey = 'biometric_challenge_' . $user->id;
        $challenge = Cache::pull($cacheKey);

        if (!$challenge) {
            return response()->json([
                'error' => 'Challenge expired'
            ], 400);
        }

        try {

            $publicKey = base64_decode($user->biometric_key);
            $signature = base64_decode($request->signature);

            $challengeBytes = base64_decode(strtr($challenge, '-_', '+/'));

           $verified = Sodium::crypto_sign_verify_detached($signature, $challengeBytes, $publicKey);

            if (!$verified) {
                return response()->json([
                    'error' => 'Invalid signature'
                ], 401);
            }
            $user->tokens()->delete();

            $token = $user->createToken('mobile-token')->plainTextToken;

            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'biometric_verify';
            $insert->description = "User {$user->employee->fullname} login with biometric success";
            $insert->save();

            return response()->json([
                'message' => $insert->description,
                'token' => $token
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Verification failed',
                'details' => $e->getMessage()
            ], 500);

        }
    }
    /**
     * Remove biometric key.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->biometric_key = null;
        $user->biometric_device_id = null;
        $user->save();

        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'biometric_remove';
        $insert->description = "User {$user->employee->fullname} removed biometric key";
        $insert->save();

        return response()->json([
            'message' => $insert->description
        ]);
    }

    public function loginChallenge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$user->biometric_key) {
            return response()->json(['error' => 'Biometric not registered for this user'], 400);
        }

        $challenge = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        Cache::put('biometric_challenge_' . $user->email, $challenge, now()->addMinutes(5));

        return response()->json(['challenge' => $challenge]);
    }

    public function loginVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'signature' => 'required|string',
            'device_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if (!$user->biometric_key) {
            return response()->json(['error' => 'Biometric not registered'], 400);
        }
        if ($user->biometric_device_id !== $request->device_id) {
            return response()->json([
                'error' => 'Biometric tidak terdaftar di device ini'
            ], 403);
        }

        $cacheKey = 'biometric_challenge_' . $user->email;
        $challenge = Cache::pull($cacheKey);
        if (!$challenge) {
            return response()->json(['error' => 'Challenge expired'], 400);
        }

        try {
            $signature = base64_decode($request->signature);
            // Log::info('Signature length: ' . strlen($signature));
            // Log::info('Signature (hex): ' . bin2hex($signature));
            $publicKey = base64_decode($user->biometric_key);
            // Log::info('Public key length: ' . strlen($publicKey)); // harus 32
            $challengeBytes = base64_decode(strtr($challenge, '-_', '+/'));
            // Log::info('Challenge bytes length: ' . strlen($challengeBytes)); // 32

            // $verified = Sodium::crypto_sign_verify_detached($signature, $challengeBytes, $publicKey);

            $verified = sodium_crypto_sign_verify_detached(
                $signature,
                $challengeBytes,
                $publicKey
            );
            
            if (!$verified) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            if ($user->tokens()->exists()) {
                return response()->json([
                    'error' => 'Sesi aktif ditemukan di perangkat lain. Silakan logout terlebih dahulu.'
                ], 403);
            }

            $token = $user->createToken('mobile-token')->plainTextToken;
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'biometric_verify';
            $insert->description = "User {$user->employee->fullname} login with biometric success";
            $insert->save();

            return response()->json(['token' => $token]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Verification failed', 'details' => $e->getMessage()], 500);
        }
    }
}
