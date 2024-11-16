<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_hash')->unique(); // Unique identifier for each transaction
            $table->integer('sender_id'); // User who initiates the transfer
            $table->integer('receiver_id'); // Receiver on the same platform
            $table->string('external_wallet_address')->nullable(); // For wallet-to-wallet transfers
            $table->enum('type', ['internal', 'wallet-to-wallet']); // Type of transaction
            $table->decimal('amount', 20, 8); // Amount of cryptocurrency
            $table->string('currency')->default('BTC'); // Type of cryptocurrency, e.g., BTC, ETH
            $table->text('note')->nullable(); // Optional note for the transaction
            $table->string('status')->default('pending'); // Transaction status: pending, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
