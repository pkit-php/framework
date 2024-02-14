<?php

use PHPUnit\Framework\TestCase;

class HomeTest extends TestCase {

    private ?GuzzleHttp\Client $http;
    
    public function setUp(): void {
        $this->http = new GuzzleHttp\Client(['http_erros' => false]);
    }

    public function tearDown(): void {
        $this->http = null;
    }

    public function testGetStatusCode() {
        $response = $this->http->request('GET','http://localhost:8080');

        $this->assertEquals(200, $response->getStatusCode());
    }
}