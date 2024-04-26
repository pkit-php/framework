<?php

test("status 404", function ($http) {
    expect(fn() => $http->request('GET', 'http://localhost:8080/fake'))->toThrow(Exception::class, "404 Not Found");
})->with("http");

test("status 400", function ($http) {
    expect(
        fn() => $http->request(
            'GET',
            'http://localhost:8080/',
            ["body" => "{}a", "headers" => ["Content-Type" => "application/json"]]
        )
    )->toThrow(Exception::class, "400 Bad Request");
    expect(
        fn() => $http->request(
            'GET',
            'http://localhost:8080/',
            ["body" => "<xml>a", "headers" => ["Content-Type" => "application/xml"]]
        )
    )->toThrow(Exception::class, "400 Bad Request");
})->with("http");