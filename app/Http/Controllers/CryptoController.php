<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CryptoController extends Controller
{
    use ApiResponse;

    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * @OA\Post(
     *     path="/api/create-wallet",
     *     tags={"Wallet"},
     *     summary="Create a new wallet",
     * security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Wallet created successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create wallet"
     *     )
     * )
     */
    public function createWallet()
    {
        try {
            $wallet = $this->walletService->createWallet();
            return $this->successResponse($wallet);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create wallet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/get-balance/{balance}",
     *     tags={"Wallet"},
     *     summary="Get wallet balance",
     * security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         description="Wallet address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet balance retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve balance"
     *     )
     * )
     */
    public function getBalance($address)
    {
        try {
            $balance = $this->walletService->getBalance($address);
            return $this->successResponse($balance);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get balance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transaction/internal",
     *     tags={"Transaction"},
     *     summary="Send internal transaction",
     * security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="receiver_uuid", type="string", description="Receiver's UUID"),
     *             @OA\Property(property="amount", type="number", format="float", description="Amount to send"),
     *             @OA\Property(property="currency", type="string", description="Currency (e.g., BTC, ETH)"),
     *             @OA\Property(property="note", type="string", description="Optional note", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction completed successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or insufficient balance"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Transaction failed"
     *     )
     * )
     */
    public function sendInternalTransaction(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_uuid' => 'required|exists:users,uuid',
            'amount' => 'required|numeric|min:0.0001',
            'currency' => 'required|string|max:10',
            'note' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $sender = auth()->user();
            $receiver = User::where('uuid', $validatedData['receiver_uuid'])->first();
            $currency = strtolower($validatedData['currency']) . '_balance';

            $senderWallet = Wallet::where('user_id', $sender->id)->firstOrFail();
            $receiverWallet = Wallet::where('user_id', $receiver->id)->firstOrFail();

            if (!array_key_exists($currency, $senderWallet->getAttributes())) {
                return response()->json(['error' => 'Unsupported currency'], 400);
            }

            if ($senderWallet->$currency < $validatedData['amount']) {
                return response()->json(['error' => 'Insufficient balance'], 400);
            }

            $senderWallet->decrement($currency, $validatedData['amount']);
            $receiverWallet->increment($currency, $validatedData['amount']);

            $transaction = Transaction::create([
                'transaction_hash' => Str::uuid()->toString(),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'type' => 'internal',
                'amount' => $validatedData['amount'],
                'currency' => strtoupper($validatedData['currency']),
                'note' => $validatedData['note'],
                'status' => 'completed',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transaction completed successfully',
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transaction/send",
     *     tags={"Transaction"},
     *     summary="Send crypto",
     * security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="to_address", type="string", description="Receiver's wallet address"),
     *             @OA\Property(property="amount", type="number", description="Amount to send")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Crypto sent successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to send crypto"
     *     )
     * )
     */
    public function sendCrypto(Request $request)
    {
        $request->validate([
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
        ]);
        $pk = Wallet::where('user_id', Auth::id())->where('address', $request->to_address)->first();
        try {
            $result = $this->walletService->sendCrypto(
                $request->to_address,
                $request->amount,
                $pk->private_key
            );

            return $this->successResponse($result, 'Crypto sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send crypto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transaction/swap",
     *     tags={"Transaction"},
     *     summary="Swap crypto",
     * security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tokenFrom", type="string", description="Token to swap from"),
     *             @OA\Property(property="tokenTo", type="string", description="Token to swap to"),
     *             @OA\Property(property="amount", type="number", description="Amount to swap")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Crypto swapped successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to swap crypto"
     *     )
     * )
     */
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
