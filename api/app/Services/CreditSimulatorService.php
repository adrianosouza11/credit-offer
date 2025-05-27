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
            $modalities = [];
            foreach($instValue->modalidades as $keyMod => $eachMod) {
                $listOffer = $this->gosatApiClient->listOffer($cpf, $instValue->id, $eachMod->cod)->object();

                $offers[$instKey] = [
                    'institutionCode' => $instValue->id,
                    'institutionName' => $instValue->nome
                ];

                $modalities[$keyMod] = [
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

            $offers[$instKey]['modalities'] = $modalities;
        }

        return $offers;
    }

    /**
     * @param string $cpf
     * @param int $simulateValue
     * @return array
     */
    public function filterAvailableOffers(string $cpf, float $simulateValue) : array
    {
        $offers = $this->getOffersByCpf($cpf);

        foreach ($offers as $keyInst => $institution) {
            $modalities = array_filter($institution['modalities'], function ($modality) use ($simulateValue) {
                return $simulateValue >= $modality['conditions']['minValue']
                    &&  $simulateValue <= $modality['conditions']['maxValue'];
            });

            if(!count($modalities))
                unset($offers[$keyInst]);
            else
                $offers[$keyInst]['modalities'] = $modalities;
        }

        return $offers;
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

    /**
     * @param string $cpf
     * @param float $simulateValue
     * @return array
     */
    public function getThreeBestOffers(string $cpf, float $simulateValue) : array
    {
        $availableOffers = $this->filterAvailableOffers($cpf, $simulateValue);

        if(!$availableOffers)
            return [];

        $this->calculateOffers($simulateValue, $availableOffers);

        $flattened = collect($this->summaryOffers)->flatMap(function($institution) {
            return collect($institution['modalities'])->map(function($modality) use ($institution){
                return [
                    "institutionName" => $institution['institutionName'],
                    "institutionCode" => $institution['institutionCode'],
                    "modalityName"    => $modality['name'],
                    "modalityCode"    => $modality['code'],
                    "modalityMonthInt" => $modality['conditions']['interestPerMonth'],
                    "totalPaid"       => $modality['calculated']['totalPaid'] ?? 0,
                    "instNumber"      => $modality['calculated']['instNumber'] ?? 0,
                ];
            });
        });

        return $flattened
            ->sortBy('totalPaid')
            ->take(3)
            ->values()
            ->all();
    }
}
