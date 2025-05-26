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

    public function test_must_return_total_paid_installments_by_modality_institution_valid()
    {
        //Arrange
        $offers = [
            [
                "institutionCode" => 1,
                "institutionName" => "Banco PingApp",
                "modalities" => [
                    [
                        "code" => 3,
                        "name" => "crédito pessoal",
                        "conditions" => [
                            'minInstQty' => 12,
                            'maxInstQty' => 48,
                            'minValue' => 5000,
                            'maxValue' => 8000,
                            'interestPerMonth' => 0.0495
                        ]
                    ],
                    [
                        "code" => 13,
                        "name" => "crédito consignado",
                        "conditions" => [
                            'minInstQty' => 24,
                            'maxInstQty' => 72,
                            'minValue' => 10000,
                            'maxValue' => 19250,
                            'interestPerMonth' => 0.0118
                        ]
                    ]
                ]
            ],
            [
                "institutionCode" => 2,
                "institutionName" => "Financeira Assert",
                "modalities" => [
                    [
                        "code" => 3,
                        "name" => "crédito pessoal",
                        "conditions" => [
                            'minInstQty' => 12,
                            'maxInstQty' => 48,
                            'minValue' => 3000,
                            'maxValue' => 7000,
                            'interestPerMonth' => 0.0365
                        ]
                    ]
                ]
            ]
        ];

        //Act
        $service = new CreditSimulatorService();

        $service->calculateOffers(7000, $offers);

        $result = $service->getSummaryOffers();

        //Assert
        $this->assertEquals([
            'totalPaid' => 9450.63,
            'instNumber' => 12
        ],$result[0]['modalities'][0]['calculated']);

        $this->assertEquals([
            'totalPaid' => 8078.87,
            'instNumber' => 24
        ],$result[0]['modalities'][1]['calculated']);

        $this->assertEquals([
            'totalPaid' => 8769.56,
            'instNumber' => 12
        ],$result[1]['modalities'][0]['calculated']);
    }
}
