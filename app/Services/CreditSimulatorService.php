<?php

namespace App\Services;

use App\Services\Integrations\GosatApiClient;

class CreditSimulatorService
{
    private GosatApiClient $gosatApiClient;
    private LoanCalculatorService $loanCalculatorService;

    private array $summaryOffers = [];

    public function __construct()
    {
        $this->gosatApiClient = new GosatApiClient();
        $this->loanCalculatorService = new LoanCalculatorService();
    }

    /**
     * @param string $cpf
     * @return array
     */
    public function getOffersByCpf(string $cpf) : array
    {
        $instRes = $this->gosatApiClient->listInstitutions($cpf);

        $institutions = $instRes->object();

        $offers = [];

        foreach ($institutions->instituicoes as $instKey => $instValue) {
            foreach($instValue->modalidades as $keyMod => $eachMod) {
                $listOffer = $this->gosatApiClient->listOffer($cpf, $instValue->id, $eachMod->cod)->object();

                $offers[$instKey] = [
                    'institutionCode' => $instValue->id,
                    'institutionName' => $instValue->nome
                ];

                $offers[$instKey]['modalities'][$keyMod] = [
                    'code' => $eachMod->cod,
                    'name' => $eachMod->nome,
                    'conditions' => [
                        'minInstQty' => $listOffer->QntParcelaMin,
                        'maxInstQty' => $listOffer->QntParcelaMax,
                        'minValue' => $listOffer->valorMin,
                        'maxValue' => $listOffer->valorMax,
                        'interestPerMonth' => $listOffer->jurosMes
                    ]
                ];
            }
        }

        return $offers;
    }

    /**
     * @param string $cpf
     * @param int $simulateValue
     * @return array
     */
    public function filterAvailableOffers(string $cpf, int $simulateValue) : array
    {
        $offers = $this->getOffersByCpf($cpf);

        return array_filter($offers, function ($institution) use ($simulateValue) {
            return array_filter($institution['modalities'], function ($modality) use ($simulateValue) {
                return $simulateValue >= $modality['conditions']['minValue']
                    &&  $simulateValue <= $modality['conditions']['maxValue'];
            });
        });
    }

    public function calculateOffers(float $simulateValue, array $offersAvailable) : void
    {
        $this->summaryOffers = $offersAvailable;

        foreach ($offersAvailable as $keyInst => $institution) {
            foreach ($institution['modalities'] as $keyModality => $eachModality) {
                $calculated = $this->loanCalculatorService->calculatePriceTable(
                    $simulateValue,
                    $eachModality['conditions']['interestPerMonth'],
                    $eachModality['conditions']['minInstQty']
                );

                $this->summaryOffers[$keyInst]['modalities'][$keyModality]['calculated'] = [
                    'totalPaid' => $calculated['total_paid'],
                    'instNumber' => $eachModality['conditions']['minInstQty'],
                ];
            }
        }
    }

    /**
     * @return array
     */
    public function getSummaryOffers() : array
    {
        return $this->summaryOffers;
    }
}
