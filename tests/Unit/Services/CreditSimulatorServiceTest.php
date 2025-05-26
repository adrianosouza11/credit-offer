<?php

use Illuminate\Support\Facades\Http;
use App\Services\CreditSimulatorService;

class CreditSimulatorServiceTest extends \Tests\TestCase
{
    public function test_should_fail_when_no_offers_simulated_value_found()
    {
        //Arrange
        $cpf = 11111111111;
        $simulateValue = 7000;

        Http::fake(['dev.gosat.org/api/v1/simulacao/oferta' =>  Http::response([
            'QntParcelaMin' => 18,
            'QntParcelaMax' => 60,
            'valorMin' => 12000,
            'valorMax' => 21250,
            'jurosMes' => 0.0118
        ], 200)]);

        $service = new CreditSimulatorService();

        //Act
        $result = $service->filterAvailableOffers($cpf, $simulateValue);

        //Assert
        $this->assertEquals([], $result);
    }
}
