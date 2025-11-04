<?php

namespace App\Modules\Balance\Actions;

use App\Helpers\Helper;
use App\Modules\Balance\Models\Balance;

class GetBalanceAction
{
    public static function execute($userId)
    {
        $balance = Balance::where('user_id', $userId)->first();

        if (!$balance) {
            return [
                'success' => false,
                'data' => null,
                'msg' => 'User has no balance record',
                'code' => 404
            ];
        }

        return [
            'success' => true,
            'data' => [
                'user_id' => $balance->user_id,
                'balance' => $balance->balance,
            ],
            'msg' => 'Balance retrieved successfully',
            'code' => 200
        ];
    }
}
