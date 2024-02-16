<?php


test("route middleware of maintenance", function ($http) {
    expect(fn() => $http->request('GET', 'http://localhost:8080/maintenance'))
        ->toThrow(Exception::class, "503 Service Unavailable");
})->with("http");

test("route expired cache", function ($http, $uri) {
    $response = $http->request('GET', 'http://localhost:8080' . $uri);
    $initialText = $response->getBody()->getContents();
    sleep(1);
    $response = $http->request('GET', 'http://localhost:8080' . $uri);
    expect($initialText)->toEqual($response->getBody()->getContents());
    sleep(2);
    $response = $http->request('GET', 'http://localhost:8080' . $uri);
    expect($initialText)->not()->toEqual($response->getBody()->getContents());
    expect($response->getStatusCode())->toEqual(200);
})->with("http")
->with(["/cache"]);

test("route cache", function ($http, $uri) {
    $response = $http->request('GET', 'http://localhost:8080' . $uri);
    $initialText = $response->getBody()->getContents();
    $response = $http->request('GET', 'http://localhost:8080' . $uri);
    expect($initialText)->toEqual($response->getBody()->getContents());
})->with("http")
->with(["/cache", "/cache/file", "/cache/file?key=1"]);


test("route cache clean", function ($http) {
    $response = $http->request('GET', 'http://localhost:8080/cache/clear');
    expect(glob(".pkit/cache/cache?.cache"))->not()->toBeEmpty();
    expect(glob(".pkit/cache/cache%2Ffile?*.cache"))->not()->toBeEmpty();
    expect(glob(".pkit/cache/cache%2Ffile?key=1.cache"))->toBeEmpty();
    expect($response->getStatusCode())->toEqual(200);
})->with("http")
->depends("route cache");
