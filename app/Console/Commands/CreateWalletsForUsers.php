<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\WalletService;
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
        // Retrieve users without a wallet
        $usersWithoutWallet = User::doesntHave('wallet')->get();

        foreach ($usersWithoutWallet as $user) {
            $wallet = $this->walletService->createWallet();

            if ($wallet['status'] === true) {
                $user->wallet()->create([
                    'address' => $wallet['data']['address'],
                    'private_key' => $wallet['data']['privateKey'],
                ]);

                $this->info("Wallet created for user ID: {$user->id}");
            } else {
                Log::error('Wallet creation failed for user', ['user_id' => $user->id, 'response' => $wallet]);
                $this->warn("Failed to create wallet for user ID: {$user->id}");
            }
        }

        $this->info('Wallet creation attempt complete for users without wallets.');
    }
}
