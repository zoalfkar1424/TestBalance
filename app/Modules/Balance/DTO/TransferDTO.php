<?php

namespace App\Modules\Balance\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class TransferDTO extends DataTransferObject
{
    public $from_user_id;
    public $to_user_id;
    public $amount;
    public $comment;

    public static function fromRequest($request)
    {
        return new self([
            'from_user_id' => $request['from_user_id'],
            'to_user_id' => $request['to_user_id'],
            'amount' => $request['amount'],
            'comment' => $request['comment'] ?? null,
        ]);
    }
}

