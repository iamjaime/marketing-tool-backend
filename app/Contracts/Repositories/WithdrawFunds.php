<?php

namespace App\Contracts\Repositories;

interface WithdrawFunds
{
    /**
     * Handles withdrawing funds
     *
     * @param $amount
     * @param $currency
     * @param $recipient
     * @return mixed
     */
    public function withdraw($amount, $currency, $recipient);

    /**
     * Handles checking if the recipient exists
     * @param $userId
     * @return boolean
     */
    public function recipientExists($userId);

    /**
     * Handles updating the payout record from the webhook
     *
     * @param $payoutId
     * @param $data
     * @return mixed
     */
    public function updatePayoutRecord($payoutId, $data);

}
