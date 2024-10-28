<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Elliptic\EC;
use kornrunner\Keccak;


class CryptoController extends Controller
{
    use ApiResponse;

    public function createWallet()
    {
        // Create a new wallet for Binance Smart Chain
        $ec = new EC('secp256k1');
        $keyPair = $ec->genKeyPair();

        $privateKey = $keyPair->getPrivate()->toString(16);  // Hex private key
        $publicKey = $keyPair->getPublic()->encode('hex');   // Hex public key

        // Calculate the wallet address (BSC address is same as Ethereum address format)
        $publicKeyNoPrefix = substr($publicKey, 2); // Remove "04" prefix
        $address = '0x' . substr(Keccak::hash(hex2bin($publicKeyNoPrefix), 256), -40); // Get last 40 chars for the address

        // Return the private key and address
        return $this->successResponse([
            'address' => $address,
            'private_key' => $privateKey,
        ], 'Wallet created successfully');
    }
    public function sendCrypto(Request $request)
    {
        // Validate input
        $request->validate([
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
            'coin' => 'required|string',
        ]);

        // Use a service or API call to send crypto here
        // Example: Binance Smart Chain API or Tron API

        $result = $this->sendCryptoToBlockchain($request->to_address, $request->amount, $request->coin);

        if ($result['success']) {
            return $this->successResponse($result['data'], 'Crypto sent successfully');
        }

        return $this->errorResponse('Failed to send crypto', 500);
    }

    public function swapCrypto(Request $request)
    {
        // Implement swap logic (using external service or API)
        // Example: PancakeSwap API for BSC

        return $this->successResponse([], 'Crypto swapped successfully');
    }

    public function receiveCrypto(Request $request)
    {
        // Implement receive logic (you may not need this unless handling it on backend)
        return $this->successResponse([], 'Crypto received');
    }

    private function sendCryptoToBlockchain($toAddress, $amount, $coin)
    {
        // Implement logic to send crypto via Binance Smart Chain or Tron
        return [
            'success' => true,
            'data' => [
                'transaction_id' => 'sample_transaction_id'
            ]
        ];
    }
}
