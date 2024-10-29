<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WalletService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://cryptoserver-jqh1.onrender.com'; // Your Node.js server URL
    }

    public function createWallet()
    {
        $response = Http::post("{$this->baseUrl}/wallet");
        return $response->json();
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
