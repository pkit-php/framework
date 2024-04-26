<?php

test("route match params", function ($http, $route, $expect) {
    $response = $http->request('GET', 'http://localhost:8080/params' . $route);
    expect($response->getStatusCode())->toEqual(200);
    expect(json_decode($response->getBody(), true))->toEqual($expect);
})->with("http")
    ->with([
        ["/int/2", ["int" => 2]],
        ["/float/2.1", ["float" => 2.1]],
        ["/float/3", ["float" => 3]],
        [
            "/file/a.b",
            [
                "file" => [
                    'filename' => 'a',
                    'dirname' => '.',
                    'basename' => 'a.b',
                    'extension' => 'b'
                ]
            ]
        ],
        ["/word/a_b", ["word" => "a_b"]],
        ["/word/a_b.ext", ["word" => "a_b"]],
        ["/rest/a/b/c", ["rest" => "a/b", "filename" => "c"]],
        ["/rest/generic/a/b", ["rest" => "a/b"]],
        ["/generic/@a2.3", ["generic" => "@a2.3"]],
    ]);

test("route not match params", function ($http, $route) {
    expect(fn() => $http->request('GET', 'http://localhost:8080/params' . $route))
    ->toThrow(Exception::class,"404 Not Found");
})->with("http")
    ->with([
        ["/int/2.1"],
        ["/float/a.1"],
        ["/word/a.b"],
        ["/word/a.b.ext"],
    ]);
