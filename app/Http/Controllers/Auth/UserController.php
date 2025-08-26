<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword', 'loginWithOTP']]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: User login
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email_or_phone)
                    ->orWhere('phone', $request->email_or_phone)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_banned) {
            return response()->json(['message' => 'User is banned'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($request->email_or_phone === $user->email) {
            Mail::raw("Bạn vừa đăng nhập thành công vào hệ thống Những lời khó nói.", function($message) use ($user) {
                $message->to($user->email)
                        ->subject("Thông báo đăng nhập thành công");
            });
        }

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Forgot password with OTP
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Email or phone is required'], 400);
        }

        $user = $request->email 
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = rand(100000, 999999);

        $user->otp = Hash::make($otp);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->otp_attempts = 0;
        $user->last_otp_sent_at = now();
        $user->save();

        if ($request->email) {
            Mail::raw("Your OTP to reset password: {$otp}", function ($message) use ($user) {
                $message->to($user->email)->subject("Reset Password OTP");
            });
        } elseif ($request->phone) {
            //Todo SmsService
            // SmsService::send($user->phone, "Your OTP to reset password: {$otp}");
        }

        return response()->json([
            'message' => 'OTP sent successfully',
            'expires_in' => 600
        ]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Reset password with OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'otp' => 'required|digits:6',
            'new_password' => 'required|string|min:6'
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Email or phone is required'], 400);
        }

        $user = $request->email 
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        if ($user->otp_attempts >= 5) {
            return response()->json(['message' => 'Too many invalid OTP attempts, please request new OTP'], 429);
        }

        if (!Hash::check($request->otp, $user->otp)) {
            $user->otp_attempts++;
            $user->save();
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->last_otp_sent_at = null;
        $user->save();

        if ($request->email) {
            Mail::raw("Your password has been reset successfully.", function ($message) use ($user) {
                $message->to($user->email)->subject("Password Reset Success");
            });
        } elseif ($request->phone) {
            // Todo SmsService
            // SmsService::send($user->phone, "Your password has been reset successfully.");
        }

        return response()->json([
            'message' => 'Password reset successfully',
            'redirect_to' => '/login'
        ]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: User logout
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'User logged out successfully']);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Get user information
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: User registration
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|string|email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Email or phone is required'], 422);
        }

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email ?? null,
            'phone'      => $request->phone ?? null,
            'password'   => Hash::make($request->password),
            'avata_url'  => $request->avata_url ?? null,
            'bio'        => $request->bio ?? null,
        ]);

        $defaultRole = Role::where('name', 'user')->first();
        $user->roles()->sync([$defaultRole->id]);

        if ($user->email) {
            Mail::raw("Welcome {$user->name}, your account has been created successfully!", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject("Welcome to To Say App");
            });
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user->load('roles')
        ]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: send sms
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSms($phone, $message)
    {
        // TODO: Tích hợp Twilio, Nexmo, Viettel SMS...
        Log::info("Send SMS to {$phone}: {$message}");
    }

    public function test CICD() {
        Log::inforrr("Test CICD");
    }
}
