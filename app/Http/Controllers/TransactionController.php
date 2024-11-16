<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    use ApiResponse;

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'tx_id' => 'required|string|unique:transactions',
            'amount' => 'required|numeric',
            'coin' => 'required|string',
            'status' => 'required|string',
        ]);

        $transaction = Transaction::create([
            'tx_id' => $request->tx_id,
            'amount' => $request->amount,
            'coin' => $request->coin,
            'status' => $request->status,
        ]);

        return $this->successResponse($transaction, 'Transaction stored successfully');
    }

    public function getTransactionHistory()
    {
        $transactions = Transaction::all();
        return $this->successResponse($transactions, 'Transaction history retrieved');
    }


}
