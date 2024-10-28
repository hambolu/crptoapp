<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3p\EthereumUtil\Util;

use Uma\Phpec\EC;
use kornrunner\Keccak;

class CryptoController extends Controller
{
    use ApiResponse;
    private $web3;



public function createWallet()
{
    try {
        $ec = new EC('secp256k1');
        $keyPair = $ec->genKeyPair();

        $privateKey = $keyPair->getPrivate('hex');
        $publicKey = $keyPair->getPublic(false, 'hex');

        $publicKeyNoPrefix = substr($publicKey, 2);
        $keccakHash = Keccak::hash(hex2bin($publicKeyNoPrefix), 256);
        $address = '0x' . substr($keccakHash, -40);

        return $this->successResponse([
            'address' => $address,
            'private_key' => $privateKey,
        ]);
    } catch (\Exception $e) {
        return $this->errorResponse('Failed to create wallet: ' . $e->getMessage(), 500);
    }
}

    public function getBalance(Request $request)
    {
        $web3 = new Web3(new HttpProvider('https://mainnet.infura.io/v3/226b7d90c9ea429aa64b907b426c2519'));

        $address = $request->input('address');

        $balanceWei = $web3->eth()->getBalance($address);
        $balanceEther = $web3->utils()->fromWei($balanceWei, 'ether');

        return $this->successResponse([
            'balance' => $balanceEther . ' ETH'
        ]);
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

    public function sendCryptoToBlockchain($toAddress, $amount, $coin)
    {
        // Prepare transaction data
        $fromAddress = env('YOUR_WALLET_ADDRESS');
        $privateKey = env('YOUR_PRIVATE_KEY');

        // Build transaction
        $transaction = [
            'from' => $fromAddress,
            'to' => $toAddress,
            'value' => $this->toWei($amount), // Convert amount to Wei
            'gas' => '21000',                 // Standard gas limit for ETH transfers
            'gasPrice' => '20000000000',       // Gas price in Wei
        ];

        // Sign transaction using private key
        $signedTransaction = $this->web3->eth->signTransaction($transaction, $privateKey);

        // Send transaction
        $result = $this->web3->eth->sendRawTransaction($signedTransaction);

        return $result;
    }

    private function toWei($amount)
    {
        return bcmul($amount, bcpow('10', '18')); // Convert to Wei
    }
}
