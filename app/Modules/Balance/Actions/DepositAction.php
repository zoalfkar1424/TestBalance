<?php

namespace App\Modules\Balance\Actions;

use App\Helpers\Helper;
use App\Modules\Balance\DTO\DepositDTO;
use App\Modules\Balance\Models\Balance;
use App\Modules\Balance\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DepositAction
{
    public static function execute(DepositDTO $depositDTO)
    {
        try {
            DB::beginTransaction();

            // Get or create balance record
            $balance = Balance::firstOrCreate(
                ['user_id' => $depositDTO->user_id],
                ['balance' => 0]
            );

            // Update balance
            $balance->balance += $depositDTO->amount;
            $balance->save();

            // Create transaction record
            Transaction::create([
                'user_id' => $depositDTO->user_id,
                'type' => 'deposit',
                'amount' => $depositDTO->amount,
                'comment' => $depositDTO->comment,
            ]);

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'user_id' => $balance->user_id,
                    'balance' => $balance->balance,
                    'deposited_amount' => $depositDTO->amount,
                ],
                'msg' => 'Deposit successful',
                'code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::createErrorResponse(null, 'Deposit failed: ' . $e->getMessage(), 500);
        }
    }
}

