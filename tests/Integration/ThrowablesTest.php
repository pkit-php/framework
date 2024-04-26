<?php

test("route redirect", function ($http) {
    $response = $http->request('GET', 'http://localhost:8080/redirect');
    expect($response->getStatusCode())->toEqual(200);
})->with("http");