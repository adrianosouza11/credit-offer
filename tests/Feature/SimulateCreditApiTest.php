<?php

namespace Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SimulateCreditApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_fail_when_cpf_is_missing()
    {
        $response = $this->postJson('');
    }
}
