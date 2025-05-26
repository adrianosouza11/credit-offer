<?php

namespace App\Services;

use App\Services\Integrations\GosatApiClient;

class CreditSimulatorService
{
    private GosatApiClient $gosatApiClient;

    public function __construct()
    {
        $this->gosatApiClient = new GosatApiClient();
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
}
