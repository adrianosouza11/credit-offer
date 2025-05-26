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

//    public function test_should_return_empty_when_external_api_does_not_find_results()
//    {
//        Http::fake([ 'dev.gosat.org/api/v1/simulacao/oferta' =>  Http::response([
//            'QntParcelaMin' => 18,
//            'QntParcelaMax' => 60,
//            'valorMin' => 12000,
//            'valorMax' => 21250,
//            'juroMes' => 0.0118
//        ], 200)]);
//
//        $this->postJson('/api/simulate');
//    }
}
