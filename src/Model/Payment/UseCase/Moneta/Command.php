<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Moneta;

use Symfony\Component\HttpFoundation\Request;

class Command
{
    public $MNT_ID;
    public $MNT_TRANSACTION_ID;
    public $MNT_OPERATION_ID;
    public $MNT_AMOUNT;
    public $MNT_SUBSCRIBER_ID;
    public $MNT_TEST_MODE;
    public $MNT_SIGNATURE;
    public $MNT_CURRENCY_CODE;

    public static function fromRequest(Request $request): self
    {
        $command = new self();
        $command->MNT_ID = $request->request->get('MNT_ID');
        $command->MNT_TRANSACTION_ID = $request->request->get('MNT_TRANSACTION_ID');
        $command->MNT_OPERATION_ID = $request->request->get('MNT_OPERATION_ID');
        $command->MNT_AMOUNT = $request->request->get('MNT_AMOUNT');
        $command->MNT_CURRENCY_CODE = $request->request->get('MNT_CURRENCY_CODE');
        $command->MNT_SUBSCRIBER_ID = $request->request->get('MNT_SUBSCRIBER_ID');
        $command->MNT_TEST_MODE = $request->request->get('MNT_TEST_MODE');
        $command->MNT_SIGNATURE = $request->request->get('MNT_SIGNATURE');
        return $command;
    }
}
