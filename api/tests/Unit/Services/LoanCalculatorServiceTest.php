<?php

use \Tests\TestCase;
use \App\Services\LoanCalculatorService;

class LoanCalculatorServiceTest extends TestCase
{
    public function test_must_return_total_paid_and_installments_valid()
    {
        //Arrange
        $simulateValue = 7000;
        $installments = 24;
        $monthlyInterest = 0.0495;

        //Act
        $loanCalculator =  new LoanCalculatorService();

        $result = $loanCalculator->calculatePriceTable($simulateValue, $monthlyInterest, $installments);

        //Assert
        $this->assertEquals(504.83, $result['pmt']);
        $this->assertEquals(12115.96, $result['total_paid']);
    }
}
