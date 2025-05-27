<?php

namespace Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SimulateCreditApiTest extends TestCase
{
    //use RefreshDatabase;

    public function test_should_fail_when_cpf_is_missing()
    {
        //Arrange
        $data = [ 'simulateValue' => 7500];

        //Act
        $response = $this->postJson('/api/simulate-credit',$data);

        //Assert
        $response->assertStatus(422)->assertJsonValidationErrors(['cpf']);
    }

    public function test_should_fail_when_simulateValue_is_missing()
    {
        //Arrange
        $data = ['cpf' => 7500];

        //Act
        $response = $this->postJson('/api/simulate-credit',$data);

        //Assert
        $response->assertStatus(422)->assertJsonValidationErrors(['simulateValue']);
    }

    public function test_should_fail_when_cpf_is_not_allowed_list()
    {
        //Arrange
        $data = ['cpf' => '33333333333'];

        //Act
        $this->postJson('/api/simulate-credit', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_should_fail_when_max_min_cpf_param_is_invalid()
    {
        $data = ['cpf' => '123123123', 'simulateValue' => 7500];

        $this->postJson('/api/simulate-credit', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_should_fail_when_simulateValue_param_is_not_number()
    {
        $data = ['simulateValue' => 'Name', 'cpf' => '22222222222'];

        $this->postJson('/api/simulate-credit', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['simulateValue']);
    }

    public function test_must_return_maximum_3_credit_offers_cpf_value_ordered_by_lowest_value_api()
    {
        $data = ['cpf' => '11111111111', 'simulateValue' => 7000];

        $this->postJson('/api/simulate-credit', $data)
            ->assertStatus(200)
            ->assertJson([
                [
                    'instituicaoFinanceira' => 'Financeira Assert',
                    'modalidadeCredito' => 'crédito pessoal',
                    'valorAPagar' => 8769.56,
                    'valorSolicitado' => 7000,
                    'taxaJuros' => 0.0365,
                    'qntParcelas' => 12
                ],
                [
                    'instituicaoFinanceira' => 'Banco PingApp',
                    'modalidadeCredito' => 'crédito pessoal',
                    'valorAPagar' => 9450.63,
                    'valorSolicitado' => 7000,
                    'taxaJuros' => 0.0495,
                    'qntParcelas' => 12
                ],
            ]);
    }
}
