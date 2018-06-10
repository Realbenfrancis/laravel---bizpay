<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;

class CustomerTest extends TestCase
{
    private $api_base = '/api/v0.3';

    public function testCustomer() {

        $admin = factory(User::class)->create([
            'name' => 'Admin',
            'email'    => 'admin@bizpay.co.uk',
            'password' => md5('ahq701nAlpuqZblahxoaQblaAnx917Ajfd'),
            'merchant_id' => 1,
            'status' => 1,
            'user_id' => str_random(20),
            'user_type' => 0,
            'api_limit' => 50000,
            'api_usage' => 0,
            'api_token' => str_random(30),
        ]);

        $response = $this->withHeaders([
            'x-bizpay-key' => $admin->api_token
        ])->get($this->api_base . '/customers');
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'x-bizpay-key' => $admin->api_token
        ])->json('POST', $this->api_base . '/customers', [
            'first_name' => 'First',
            'last_name' => 'Last',
            'email' => str_random(10) . '@example.com',
            'country' => 'US'
        ]);
        $response->assertStatus(200);

        $response = $this->withHeaders([
            'x-bizpay-key' => $admin->api_token
        ])->json('POST', $this->api_base . '/customers', [
            'first_name' => 'First',
            'last_name' => 'Last',
            'email' => 'invalid email',
            'country' => 'US'
        ]);
        $response->assertStatus(400);

        $response = $this->withHeaders([
            'x-bizpay-key' => $admin->api_token
        ])->json('POST', $this->api_base . '/customers', [
            'last_name' => 'Last',
            'email' => 'invalid email',
            'country' => 'US'
        ]);
        $response->assertStatus(400);
    }
}
