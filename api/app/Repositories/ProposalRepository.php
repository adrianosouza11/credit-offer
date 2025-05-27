<?php

namespace App\Repositories;

use App\Models\Proposal;

class ProposalRepository
{
    /***
     * @param array $params
     * @return Proposal
     */
    public function create(array $params) : Proposal
    {
        return Proposal::create($params);
    }
}
