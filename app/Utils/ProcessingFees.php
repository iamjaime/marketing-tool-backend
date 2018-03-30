<?php

namespace App\Utils;

/**
 * Processing Fees
 *
 */
trait ProcessingFees {

    public $percentProcessingFee = 0.029;
    public $fixedProcessingFee = 0.30;

    /**
     * Handles getting the Processing Fees
     *
     * @param $value  The amount of views needed
     * @return array  The processing fees.
     */
    public function getProcessingFees($value) {
        $percentRate = (1 - $this->percentProcessingFee);
        $views = $value * 2; //multiply by 2 because each view is 0.02

        $netAmount = ($views / 100);

        $total = $netAmount + $this->fixedProcessingFee;
        $total = $total / $percentRate;

        $totalCost = round($total, 2);
        $processingFee = $totalCost - $netAmount;

        $fees = array(
            'net_amount' => $netAmount,
            'processing_fees' => number_format($processingFee, 2)
        );

        return $fees;
    }

}
