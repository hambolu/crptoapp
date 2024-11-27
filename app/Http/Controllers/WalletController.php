<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/wallet",
     *     summary="Get Authenticated User Wallet",
     *     description="Fetch the wallet details of the authenticated user.",
     *     tags={"Wallet"},
     *security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Wallet details retrieved successfully.",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request.",
     *
     *     )
     * )
     */
    public function getWallet(Request $request)
    {
        try {
            $user = Auth::user();
            // Assuming Wallet model has a relation with User
            $wallet_details = Wallet::where('user_id', Auth::id())->first();

            if (!$wallet_details) {
                $wallet = Http::timeout(60) // Set a timeout of 30 seconds
                    ->post("https://cryptoserver-jqh1.onrender.com/wallet");

                $walletData = $wallet->json();
                if (isset($walletData['address']) && isset($walletData['privateKey'])) {
                    $user->wallet()->create([
                        'address' => $walletData['address'],
                        'private_key' => $walletData['privateKey'],
                    ]);

                }
            }

            return $this->successResponse($wallet_details, 'Wallet details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Could not retrieve wallet.', 400, ['error' => $e->getMessage()]);
        }
    }


}
