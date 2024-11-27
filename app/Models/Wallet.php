<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'private_key',
        'bnb_balance',
        'busd_balance',
        'cake_balance',
        'usdt_balance',
        'eth_balance',
        'btc_balance',

        // Additional popular tokens
        'ada_balance', 'dot_balance', 'xrp_balance', 'link_balance',
        'ltc_balance', 'uni_balance', 'doge_balance', 'shib_balance',
        'axs_balance', 'sxp_balance', 'mana_balance', 'sand_balance',
        'ftm_balance', 'atom_balance', 'sol_balance', 'avax_balance',
        'luna_balance', 'matic_balance', 'near_balance', 'bake_balance',
        'xvs_balance', 'twt_balance', 'alpaca_balance',

        // Additional DeFi tokens
        'belt_balance', 'auto_balance', 'nuls_balance', 'bnbx_balance',
        'dai_balance', 'fil_balance', 'bat_balance', 'ctsi_balance',
        'reef_balance', 'alice_balance',

        // Gaming and Metaverse tokens
        'hero_balance', 'dar_balance', 'chr_balance', 'gala_balance',
        'enj_balance', 'loka_balance', 'movr_balance',

        // Utility tokens
        'band_balance', 'perp_balance', 'coti_balance', 'ocean_balance',
        'rune_balance', 'zil_balance', 'hbar_balance', 'ont_balance',
        'one_balance', 'ctk_balance', 'theta_balance', 'vet_balance',
        'nkn_balance',

        // More tokens can be added as needed
        'total_balance' // Holds the total balance of all assets combined
    ];

    protected $casts = [
        'bnb_balance' => 'decimal:8',
        'busd_balance' => 'decimal:8',
        'cake_balance' => 'decimal:8',
        'usdt_balance' => 'decimal:8',
        'eth_balance' => 'decimal:8',
        'btc_balance' => 'decimal:8',

        // Additional token balances (decimal:8 for consistent precision)
        'ada_balance' => 'decimal:8',
        'dot_balance' => 'decimal:8',
        'xrp_balance' => 'decimal:8',
        'link_balance' => 'decimal:8',
        'ltc_balance' => 'decimal:8',
        'uni_balance' => 'decimal:8',
        'doge_balance' => 'decimal:8',
        'shib_balance' => 'decimal:8',
        'axs_balance' => 'decimal:8',
        'sxp_balance' => 'decimal:8',
        'mana_balance' => 'decimal:8',
        'sand_balance' => 'decimal:8',
        'ftm_balance' => 'decimal:8',
        'atom_balance' => 'decimal:8',
        'sol_balance' => 'decimal:8',
        'avax_balance' => 'decimal:8',
        'luna_balance' => 'decimal:8',
        'matic_balance' => 'decimal:8',
        'near_balance' => 'decimal:8',
        'bake_balance' => 'decimal:8',
        'xvs_balance' => 'decimal:8',
        'twt_balance' => 'decimal:8',
        'alpaca_balance' => 'decimal:8',

        // DeFi, gaming, metaverse, and utility tokens
        'belt_balance' => 'decimal:8',
        'auto_balance' => 'decimal:8',
        'nuls_balance' => 'decimal:8',
        'bnbx_balance' => 'decimal:8',
        'dai_balance' => 'decimal:8',
        'fil_balance' => 'decimal:8',
        'bat_balance' => 'decimal:8',
        'ctsi_balance' => 'decimal:8',
        'reef_balance' => 'decimal:8',
        'alice_balance' => 'decimal:8',
        'hero_balance' => 'decimal:8',
        'dar_balance' => 'decimal:8',
        'chr_balance' => 'decimal:8',
        'gala_balance' => 'decimal:8',
        'enj_balance' => 'decimal:8',
        'loka_balance' => 'decimal:8',
        'movr_balance' => 'decimal:8',
        'band_balance' => 'decimal:8',
        'perp_balance' => 'decimal:8',
        'coti_balance' => 'decimal:8',
        'ocean_balance' => 'decimal:8',
        'rune_balance' => 'decimal:8',
        'zil_balance' => 'decimal:8',
        'hbar_balance' => 'decimal:8',
        'ont_balance' => 'decimal:8',
        'one_balance' => 'decimal:8',
        'ctk_balance' => 'decimal:8',
        'theta_balance' => 'decimal:8',
        'vet_balance' => 'decimal:8',
        'nkn_balance' => 'decimal:8',

        'total_balance' => 'decimal:8'
    ];

    /**
     * Calculate the total balance as the sum of all individual token balances.
     */
    public function calculateTotalBalance()
    {
        $this->total_balance =
            $this->bnb_balance + $this->busd_balance + $this->cake_balance + $this->usdt_balance + $this->eth_balance + $this->btc_balance +
            $this->ada_balance + $this->dot_balance + $this->xrp_balance + $this->link_balance + $this->ltc_balance + $this->uni_balance +
            $this->doge_balance + $this->shib_balance + $this->axs_balance + $this->sxp_balance + $this->mana_balance + $this->sand_balance +
            $this->ftm_balance + $this->atom_balance + $this->sol_balance + $this->avax_balance + $this->luna_balance + $this->matic_balance +
            $this->near_balance + $this->bake_balance + $this->xvs_balance + $this->twt_balance + $this->alpaca_balance +
            $this->belt_balance + $this->auto_balance + $this->nuls_balance + $this->bnbx_balance + $this->dai_balance + $this->fil_balance +
            $this->bat_balance + $this->ctsi_balance + $this->reef_balance + $this->alice_balance + $this->hero_balance + $this->dar_balance +
            $this->chr_balance + $this->gala_balance + $this->enj_balance + $this->loka_balance + $this->movr_balance + $this->band_balance +
            $this->perp_balance + $this->coti_balance + $this->ocean_balance + $this->rune_balance + $this->zil_balance + $this->hbar_balance +
            $this->ont_balance + $this->one_balance + $this->ctk_balance + $this->theta_balance + $this->vet_balance + $this->nkn_balance;

        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
