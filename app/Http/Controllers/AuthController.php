<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\OneSignalService;
/**
 * @OA\Info(
 *     title="Chain Finance",
 *     version="1.0.0",
 *     description="This is the API documentation for my application.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */

class AuthController extends Controller
{
    use ApiResponse;

    private $walletService;
    protected $oneSignalService;


    public function __construct(WalletService $walletService, OneSignalService $oneSignalService)
    {
        $this->walletService = $walletService;
        $this->oneSignalService = $oneSignalService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     * tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "username", "password","player_id"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="username", type="string", example="johndoe123"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="password_confirmation", type="string", example="password123"),
     *                 @OA\Property(property="player_id", type="string", example="bhvbbdvsfgyuavbHUFYGAYVAUYDVBVAIDYBVIDAYFDBdvib"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */


    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|alpha_dash|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'uuid' => $this->generateUniqueUUID(),
            ]);
            $otp = $this->generateUniqueOtp($user->id);
            $title = 'OTP Code';
            $message = 'Your OTP code is: ' . $otp;


            $this->oneSignalService->sendNotificationToUser($request->player_id, $title, $message);


            $user->update(['otp' => $otp]);

            Mail::to($user->email)->send(new OtpMail($otp));

            return $this->successResponse($user->load('wallet'), 'User registered successfully. Please check your email for OTP verification.');

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during registration: ' . $e->getMessage(), 500);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     summary="Verify OTP for email verification",
     * tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email"},
     *                  required={"otp"},
     *                 @OA\Property(property="email", type="string", example="joe@mail.com"),
     *                  @OA\Property(property="otp", type="string", example="1234"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP"
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255',
                'otp' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && $user->otp === $request->otp) {
                $user->update(['email_verified_at' => now()]);
                return $this->successResponse([], 'OTP verified successfully, email verified.');
            }

            return $this->errorResponse('Invalid OTP', 400);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during OTP verification: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     * tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;

                return $this->successResponse(['token' => $token, 'user' => $user->load('wallet')], 'Login successful');
            }

            return $this->errorResponse('Invalid credentials', 401);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during login: ' . $e->getMessage(), 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful"
     *     )
     * )
     */

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse([], 'Logout successful');

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during logout: ' . $e->getMessage(), 500);
        }
    }

    function generateUniqueOtp($userId)
    {
        $otp = random_int(1000, 9999);
        if (DB::table('users')->where('otp', $otp)->exists()) {
            $otp = substr($otp . $userId, 0, 4);
        }

        return $otp;
    }

    function generateUniqueUUID()
    {
        $uuid = random_int(10000000, 99999999);
        if (DB::table('users')->where('uuid', $uuid)->exists()) {
            $otp = substr($uuid, 0, 8);
        }

        return $otp;
    }
}
