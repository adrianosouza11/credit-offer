<?php

namespace App\Services;

use App\Repositories\ProposalRepository;

class ProposalService
{
    private ProposalRepository $repository;

    public function __construct(ProposalRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $offers
     * @return void
     */
    public function storeAttached(array $offers) : void
    {
        foreach ($offers as $each) {
            $this->repository->create($each);
        }
    }
}
