<?php

namespace App\Modules\Balance\Actions;

use App\Helpers\Helper;
use App\Modules\Balance\DTO\WithdrawDTO;
use App\Modules\Balance\Models\Balance;
use App\Modules\Balance\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WithdrawAction
{
    public static function execute(WithdrawDTO $withdrawDTO)
    {
        try {
            DB::beginTransaction();

            // Get balance record
            $balance = Balance::where('user_id', $withdrawDTO->user_id)->first();

            if (!$balance) {
                DB::rollBack();
                return Helper::createErrorResponse(null, 'User has no balance record', 404);
            }

            // Check if sufficient balance
            if ($balance->balance < $withdrawDTO->amount) {
                DB::rollBack();
                return [
                    'success' => false,
                    'data' => ['current_balance' => $balance->balance, 'requested_amount' => $withdrawDTO->amount],
                    'msg' => 'Insufficient balance',
                    'code' => 409
                ];
            }

            // Update balance
            $balance->balance -= $withdrawDTO->amount;
            $balance->save();

            // Create transaction record
            Transaction::create([
                'user_id' => $withdrawDTO->user_id,
                'type' => 'withdraw',
                'amount' => $withdrawDTO->amount,
                'comment' => $withdrawDTO->comment,
            ]);

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'user_id' => $balance->user_id,
                    'balance' => $balance->balance,
                    'withdrawn_amount' => $withdrawDTO->amount,
                ],
                'msg' => 'Withdrawal successful',
                'code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::createErrorResponse(null, 'Withdrawal failed: ' . $e->getMessage(), 500);
        }
    }
}
