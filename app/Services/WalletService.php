<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Web3\Web3;
class WalletService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://cryptoserver-jqh1.onrender.com'; // Your Node.js server URL
    }

    // public function createWallet()
    // {
    //     $response = Http::post("{$this->baseUrl}/wallet");
    //     return $response->json();
    // }



    public function createWallet()
    {
        $response = Http::timeout(326) // Set a timeout of 30 seconds
            ->post("{$this->baseUrl}/wallet");

        $walletData = $response->json();

        if ($response->successful() && isset($walletData['status']) && $walletData['status'] === true) {
            return $walletData;
        }

        Log::warning('Wallet creation failed', ['response' => $walletData]);

        Log::error('Wallet creation failed after single attempt');
        return ['status' => false, 'message' => 'Wallet creation failed'];
    }



    public function getBalance($address)
    {
        $response = Http::get("{$this->baseUrl}/balance/{$address}");
        return $response->json();
    }

    public function sendCrypto($toAddress, $amount, $privateKey)
    {
        $response = Http::post("{$this->baseUrl}/send", [
            'senderPrivateKey' => $privateKey,
            'recipientAddress' => $toAddress,
            'amount' => $amount,
        ]);
        return $response->json();
    }

    public function swapCrypto($tokenFrom, $tokenTo, $amount)
    {
        $response = Http::post("{$this->baseUrl}/swap", [
            'tokenFrom' => $tokenFrom,
            'tokenTo' => $tokenTo,
            'amount' => $amount,
        ]);
        return $response->json();
    }
}
