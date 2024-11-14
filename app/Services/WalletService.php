<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function createWallet($attempts = 3, $delay = 500)
    {
        $attempt = 0;

        while ($attempt < $attempts) {
            $response = Http::post("{$this->baseUrl}/wallet");
            $walletData = $response->json();

            if ($response->successful() && isset($walletData['status']) && $walletData['status'] === true) {
                return $walletData;
            }

            Log::warning('Wallet creation attempt failed', ['attempt' => $attempt + 1, 'response' => $walletData]);
            usleep($delay * 1000);
            $attempt++;
        }

        Log::error('Wallet creation failed after maximum retries');
        return ['status' => false, 'message' => 'Wallet creation failed after maximum retries'];
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
