<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
/**
 * @OA\Info(
 *     title="Chain Finance",
 *     version="1.0.0",
 *     description="This is the API documentation for my application.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 */
class AuthController extends Controller
{
    use ApiResponse;

    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
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
     *                 required={"name", "email", "username", "password"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="username", type="string", example="johndoe123"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="password_confirmation", type="string", example="password123"),
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
            // Validate the incoming request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|alpha_dash|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Register the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            // Create wallet for the user
            // $wallet = $this->walletService->createWallet();
            // $wallet_service = $this->successResponse($wallet);

            // if ($wallet_service['status'] === true) {
            //     Wallet::create([
            //         'user_id' => $user->id,
            //         'address' => $wallet_service['data']['address'],
            //         'private_key' => $wallet_service['data']['privateKey']
            //     ]);
            // }

            // Generate OTP and update the user
            $otp = Str::random(6);
            $user->update(['otp' => $otp]);

            // Send OTP email
            Mail::to($user->email)->send(new OtpMail($otp));

            // Return success response
            return $this->successResponse($user->load('wallet'), 'User registered successfully. Please check your email for OTP verification.');

        } catch (\Exception $e) {
            // Catch any exception and return an error response
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
     *                 required={"otp"},
     *                 @OA\Property(property="otp", type="string", example="123456"),
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
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = Auth::user(); // Get the authenticated user

        if ($user->otp === $request->otp) {
            // Verify email upon successful OTP verification
            $user->update(['email_verified_at' => now()]);
            return $this->successResponse([], 'OTP verified successfully, email verified.');
        }

        return $this->errorResponse('Invalid OTP', 400);
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
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     * tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'Logout successful');
    }
}
