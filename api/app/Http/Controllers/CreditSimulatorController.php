<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSimulateRequest;
use App\Repositories\ProposalRepository;
use App\Services\CreditSimulatorService;
use App\Services\ProposalService;

class CreditSimulatorController extends Controller
{
    private CreditSimulatorService $service;
    private ProposalService $proposalService;

    public function __construct()
    {
        $this->service = new CreditSimulatorService();
        $this->proposalService = new ProposalService(new ProposalRepository());
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

        $this->proposalService->storeAttached(array_map(function($offer) use ($simulateValue, $cpf) {
            return [
                ...$offer,
                'cpf' => $cpf,
                'simulateCredit' => $simulateValue
            ];
        },$bestOffers));

        return response()->json($data);
    }
}
