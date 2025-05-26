<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\Response;

class GosatApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('GOSAT_API_BASE_URL');
    }

    /**
     * @param $cpf
     * @return Response
     */
    public function listInstitutions($cpf) : Response
    {
        return Http::post($this->baseUrl . '/api/v1/simulacao/credito', [
            'cpf' => $cpf
        ]);
    }

    /**
     * @param string $cpf
     * @param string $institutionId
     * @param string $modalityCode
     * @return Response
     */
    public function listOffer(string $cpf, string $institutionId, string $modalityCode) : Response
    {
        return Http::post($this->baseUrl . '/api/v1/simulacao/oferta', [
            'cpf' => $cpf,
            'instituicao_id' => $institutionId,
            'codModalidade' => $modalityCode
        ]);
    }
}
