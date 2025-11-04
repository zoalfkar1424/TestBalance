<?php

namespace App\Modules\Balance\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Balance\Actions\DepositAction;
use App\Modules\Balance\Actions\GetBalanceAction;
use App\Modules\Balance\Actions\TransferAction;
use App\Modules\Balance\Actions\WithdrawAction;
use App\Modules\Balance\DTO\DepositDTO;
use App\Modules\Balance\DTO\TransferDTO;
use App\Modules\Balance\DTO\WithdrawDTO;
use App\Modules\Balance\Requests\DepositRequest;
use App\Modules\Balance\Requests\TransferRequest;
use App\Modules\Balance\Requests\WithdrawRequest;

class BalanceController extends Controller
{
    /**
     * Deposit funds to user balance
     *
     * @param DepositRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(DepositRequest $request)
    {
        $depositDTO = DepositDTO::fromRequest($request);
        $response = Helper::createResponse(DepositAction::execute($depositDTO));
        return response()->json($response, $response['code']);
    }

    /**
     * Withdraw funds from user balance
     *
     * @param WithdrawRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw(WithdrawRequest $request)
    {
        $withdrawDTO = WithdrawDTO::fromRequest($request);
        $response = Helper::createResponse(WithdrawAction::execute($withdrawDTO));
        return response()->json($response, $response['code']);
    }

    /**
     * Transfer funds between users
     *
     * @param TransferRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(TransferRequest $request)
    {
        $transferDTO = TransferDTO::fromRequest($request);
        $response = Helper::createResponse(TransferAction::execute($transferDTO));
        return response()->json($response, $response['code']);
    }

    /**
     * Get user balance
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance($userId)
    {
        $response = Helper::createResponse(GetBalanceAction::execute($userId));
        return response()->json($response, $response['code']);
    }
}

