<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSimulateRequest;

class CreditSimulatorController extends Controller
{
    public function simulate(PostSimulateRequest $request)
    {
        dd($request->json('simulateValue'));
        return response()->json();
    }
}
