<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateWalletsForUsers extends Command
{
    protected $signature = 'wallet:create-for-users';
    protected $description = 'Create wallets for users who do not have one';
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        parent::__construct();
        $this->walletService = $walletService;
    }

    public function handle()
    {
        $usersWithoutWallet = User::doesntHave('wallet')->get();

        foreach ($usersWithoutWallet as $user) {
            try {
                $wallet = Http::timeout(60) // Set a timeout of 30 seconds
                ->post("https://cryptoserver-jqh1.onrender.com/wallet");

            $walletData = $wallet->json();
                if (isset($walletData['address']) && isset($walletData['privateKey'])) {
                    $user->wallet()->create([
                        'address' => $walletData['address'],
                        'private_key' => $walletData['privateKey'],
                    ]);

                    $this->info("Wallet created for user ID: {$user->id}");
                } else {
                    Log::error('Wallet creation failed for user', [
                        'user_id' => $user->id,
                        'error_message' => $walletData['error_message'] ?? 'Unknown error'
                    ]);
                    $this->warn("Failed to create wallet for user ID: {$user->id}");
                }
            } catch (\Exception $e) {
                Log::error('Exception during wallet creation', [
                    'user_id' => $user->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString()
                ]);
                $this->warn("Failed to create wallet for user ID: {$user->id} due to an exception.{$e->getMessage()}");
            }
        }

        $this->info('Wallet creation attempt complete for users without wallets.');
    }
}