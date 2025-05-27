<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSimulateRequest;
use App\Services\CreditSimulatorService;

class CreditSimulatorController extends Controller
{
    private CreditSimulatorService $service;

    public function __construct()
    {
        $this->service = new CreditSimulatorService();
    }

    public function simulate(PostSimulateRequest $request)
    {
        $cpf = $request->json('cpf');
        $simulateValue = $request->json('simulateValue');

        $bestOffers = $this->service->getThreeBestOffers($cpf, $simulateValue);

        $data = array_map(function ($offer) use ($simulateValue) {
            return [
                'instituicaoFinanceira' => $offer['institutionName'],
                'modalidadeCredito' => $offer['modalityName'],
                'valorAPagar' => $offer['totalPaid'],
                'valorSolicitado' => $simulateValue,
                'taxaJuros' => $offer['modalityMonthInt'],
                'qntParcelas' => $offer['instNumber']
            ];
        }, $bestOffers);

        return response()->json($data);
    }
}
