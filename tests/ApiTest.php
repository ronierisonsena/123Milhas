<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{
    /**
     * Testa a rota de voos.
     *
     * @test
     */
    public function getFlightsTest()
    {
        $this->json('GET', '/api/v1/flights')
            ->seeJson([
                'status' => 200
            ]);
    }

    /**
     * Testa a rota de voos agrupados.
     *
     * @test
     */
    public function getFlightsGroupsTest()
    {
        $this->json('GET', '/api/v1/flights/groups')
            ->seeJson([
                'status' => 200
            ]);
    }

    /**
     * Testa a rota completa.
     *
     * @test
     */
    public function getFlightsDataTest()
    {
        $this->json('GET', '/api/v1/flights/all')
            ->seeJson([
                'outbound' => 1
            ]);
    }
}
