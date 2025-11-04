<?php

namespace App\Modules\Balance\Actions;

use App\Helpers\Helper;
use App\Modules\Balance\DTO\TransferDTO;
use App\Modules\Balance\Models\Balance;
use App\Modules\Balance\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransferAction
{
    public static function execute(TransferDTO $transferDTO)
    {
        try {
            DB::beginTransaction();

            // Get sender balance
            $senderBalance = Balance::where('user_id', $transferDTO->from_user_id)->first();

            if (!$senderBalance) {
                DB::rollBack();
                return Helper::createErrorResponse(null, 'Sender has no balance record', 404);
            }

            // Check if sufficient balance
            if ($senderBalance->balance < $transferDTO->amount) {
                DB::rollBack();
                return [
                    'success' => false,
                    'data' => ['current_balance' => $senderBalance->balance, 'requested_amount' => $transferDTO->amount],
                    'msg' => 'Insufficient balance',
                    'code' => 409
                ];
            }

            // Get or create receiver balance
            $receiverBalance = Balance::firstOrCreate(
                ['user_id' => $transferDTO->to_user_id],
                ['balance' => 0]
            );

            // Update sender balance
            $senderBalance->balance -= $transferDTO->amount;
            $senderBalance->save();

            // Update receiver balance
            $receiverBalance->balance += $transferDTO->amount;
            $receiverBalance->save();

            // Create transfer_out transaction for sender
            $transferOutTransaction = Transaction::create([
                'user_id' => $transferDTO->from_user_id,
                'type' => 'transfer_out',
                'amount' => $transferDTO->amount,
                'comment' => $transferDTO->comment,
            ]);

            // Create transfer_in transaction for receiver
            $transferInTransaction = Transaction::create([
                'user_id' => $transferDTO->to_user_id,
                'type' => 'transfer_in',
                'amount' => $transferDTO->amount,
                'comment' => $transferDTO->comment,
                'related_transaction_id' => $transferOutTransaction->id,
            ]);

            // Link the transactions
            $transferOutTransaction->related_transaction_id = $transferInTransaction->id;
            $transferOutTransaction->save();

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'from_user_id' => $transferDTO->from_user_id,
                    'to_user_id' => $transferDTO->to_user_id,
                    'amount' => $transferDTO->amount,
                    'sender_new_balance' => $senderBalance->balance,
                    'receiver_new_balance' => $receiverBalance->balance,
                ],
                'msg' => 'Transfer successful',
                'code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::createErrorResponse(null, 'Transfer failed: ' . $e->getMessage(), 500);
        }
    }
}
