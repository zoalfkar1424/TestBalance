<?php

namespace App\Modules\Balance\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class WithdrawDTO extends DataTransferObject
{
    public $user_id;
    public $amount;
    public $comment;

    public static function fromRequest($request)
    {
        return new self([
            'user_id' => $request['user_id'],
            'amount' => $request['amount'],
            'comment' => $request['comment'] ?? null,
        ]);
    }
}

