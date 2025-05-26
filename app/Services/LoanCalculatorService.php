<?php

namespace App\Services;

class LoanCalculatorService
{
    /**
     * @param float $paymentValue
     * @param float $interest
     * @param int $installments
     * @return array|float[]
     */
    public function calculatePriceTable(float $paymentValue, float $interest, int $installments) : array
    {
        \bcscale(10);

        $pmStr = (string) $paymentValue;
        $iStr = (string) $interest;
        $nStr = (string) $installments;

        if(bccomp($iStr, '0', 10) === 0) {
            $pmt =  ($installments == 0) ? '0' : bcdiv($pmStr, $installments, 10);

            return [
                'pmt' => round($pmt, 2),
                'total_paid' => round($pmStr, 2)
            ];
        }

        if($installments <= 0)
            return [
                'pmt' => 0.0,
                'total_paid' => 0.0
            ];

        $powResult = bcpow(bcadd('1', $iStr, 10), $nStr, 10);

        $numerator = bcmul($iStr, $powResult, 10);

        $denominator = bcsub($powResult, '1', 10);

        if(bccomp($denominator, '0', 10) === 0)
            return [
                'pmt' => 0.0,
                'total_paid' => 0.0
            ];

        $pmt = bcmul($pmStr, bcdiv($numerator, $denominator, 10), 10);

        $totalPaid = bcmul($pmt, $nStr, 10);

        return [
            'pmt' => round((float)$pmt, 2),
            'total_paid' => round((float)$totalPaid, 2),
        ];
    }
}
