<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->string('address')->unique();
            $table->string('private_key')->unique();
            $table->decimal('bnb_balance', 20, 8)->default(0);
            $table->decimal('busd_balance', 20, 8)->default(0);
            $table->decimal('cake_balance', 20, 8)->default(0);
            $table->decimal('usdt_balance', 20, 8)->default(0);
            $table->decimal('eth_balance', 20, 8)->default(0);
            $table->decimal('btc_balance', 20, 8)->default(0);

            // Popular tokens
            $table->decimal('ada_balance', 20, 8)->default(0);    // Cardano
            $table->decimal('dot_balance', 20, 8)->default(0);    // Polkadot
            $table->decimal('xrp_balance', 20, 8)->default(0);    // XRP
            $table->decimal('link_balance', 20, 8)->default(0);   // Chainlink
            $table->decimal('ltc_balance', 20, 8)->default(0);    // Litecoin
            $table->decimal('uni_balance', 20, 8)->default(0);    // Uniswap
            $table->decimal('doge_balance', 20, 8)->default(0);   // Dogecoin
            $table->decimal('shib_balance', 20, 8)->default(0);   // Shiba Inu
            $table->decimal('axs_balance', 20, 8)->default(0);    // Axie Infinity
            $table->decimal('sxp_balance', 20, 8)->default(0);    // Swipe
            $table->decimal('mana_balance', 20, 8)->default(0);   // Decentraland
            $table->decimal('sand_balance', 20, 8)->default(0);   // The Sandbox
            $table->decimal('ftm_balance', 20, 8)->default(0);    // Fantom
            $table->decimal('atom_balance', 20, 8)->default(0);   // Cosmos
            $table->decimal('sol_balance', 20, 8)->default(0);    // Solana
            $table->decimal('avax_balance', 20, 8)->default(0);   // Avalanche
            $table->decimal('luna_balance', 20, 8)->default(0);   // Terra
            $table->decimal('matic_balance', 20, 8)->default(0);  // Polygon
            $table->decimal('near_balance', 20, 8)->default(0);   // NEAR Protocol
            $table->decimal('bake_balance', 20, 8)->default(0);   // BakeryToken
            $table->decimal('xvs_balance', 20, 8)->default(0);    // Venus
            $table->decimal('twt_balance', 20, 8)->default(0);    // Trust Wallet Token
            $table->decimal('alpaca_balance', 20, 8)->default(0); // Alpaca Finance

            // Additional DeFi tokens
            $table->decimal('belt_balance', 20, 8)->default(0);    // Belt Finance
            $table->decimal('auto_balance', 20, 8)->default(0);    // AutoFarm
            $table->decimal('nuls_balance', 20, 8)->default(0);    // NULS
            $table->decimal('bnbx_balance', 20, 8)->default(0);    // Staked BNB
            $table->decimal('dai_balance', 20, 8)->default(0);     // Dai
            $table->decimal('fil_balance', 20, 8)->default(0);     // Filecoin
            $table->decimal('bat_balance', 20, 8)->default(0);     // Basic Attention Token
            $table->decimal('ctsi_balance', 20, 8)->default(0);    // Cartesi
            $table->decimal('reef_balance', 20, 8)->default(0);    // Reef Finance
            $table->decimal('alice_balance', 20, 8)->default(0);   // My Neighbor Alice

            // Gaming and Metaverse tokens
            $table->decimal('hero_balance', 20, 8)->default(0);    // Metahero
            $table->decimal('dar_balance', 20, 8)->default(0);     // Mines of Dalarnia
            $table->decimal('chr_balance', 20, 8)->default(0);     // Chromia
            $table->decimal('gala_balance', 20, 8)->default(0);    // Gala
            $table->decimal('enj_balance', 20, 8)->default(0);     // Enjin Coin
            $table->decimal('loka_balance', 20, 8)->default(0);    // League of Kingdoms
            $table->decimal('movr_balance', 20, 8)->default(0);    // Moonriver

            // Utility tokens
            $table->decimal('band_balance', 20, 8)->default(0);    // Band Protocol
            $table->decimal('perp_balance', 20, 8)->default(0);    // Perpetual Protocol
            $table->decimal('coti_balance', 20, 8)->default(0);    // COTI
            $table->decimal('ocean_balance', 20, 8)->default(0);   // Ocean Protocol
            $table->decimal('rune_balance', 20, 8)->default(0);    // THORChain
            $table->decimal('zil_balance', 20, 8)->default(0);     // Zilliqa
            $table->decimal('hbar_balance', 20, 8)->default(0);    // Hedera
            $table->decimal('ont_balance', 20, 8)->default(0);     // Ontology
            $table->decimal('one_balance', 20, 8)->default(0);     // Harmony
            $table->decimal('ctk_balance', 20, 8)->default(0);     // CertiK
            $table->decimal('theta_balance', 20, 8)->default(0);   // Theta Token
            $table->decimal('vet_balance', 20, 8)->default(0);     // VeChain
            $table->decimal('nkn_balance', 20, 8)->default(0);     // NKN

            // Additional tokens as required...

            // Total balance column to store the sum of all balances
            $table->decimal('total_balance', 20, 8)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
