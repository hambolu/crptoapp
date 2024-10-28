<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\WalletService;

class CryptoController extends Controller
{
    use ApiResponse;

    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createWallet()
    {
        try {
            $wallet = $this->walletService->createWallet();
            return $this->successResponse($wallet);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create wallet: ' . $e->getMessage(), 500);
        }
    }

    public function getBalance(Request $request)
    {
        $address = $request->input('address');

        try {
            $balance = $this->walletService->getBalance($address);
            return $this->successResponse($balance);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get balance: ' . $e->getMessage(), 500);
        }
    }

    public function sendCrypto(Request $request)
    {
        $request->validate([
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        try {
            $result = $this->walletService->sendCrypto(
                $request->to_address,
                $request->amount,
                env('YOUR_PRIVATE_KEY') // Use the private key from your .env file
            );

            return $this->successResponse($result, 'Crypto sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send crypto: ' . $e->getMessage(), 500);
        }
    }

    public function swapCrypto(Request $request)
    {
        $request->validate([
            'tokenFrom' => 'required|string',
            'tokenTo' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        try {
            $result = $this->walletService->swapCrypto(
                $request->tokenFrom,
                $request->tokenTo,
                $request->amount
            );

            return $this->successResponse($result, 'Crypto swapped successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to swap crypto: ' . $e->getMessage(), 500);
        }
    }
}
