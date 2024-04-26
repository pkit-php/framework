<?php

test("route parse post params", function (GuzzleHttp\Client $http, $content, $contentType) {
	$response = $http->request('POST', 'http://localhost:8080/post-params', [
		"body" => $content,
		"headers" => [
			"Content-Type" => $contentType
		]
	]);
	expect($response->getStatusCode())->toEqual(200);
	expect($response->getBody()->read(1024))->toEqual($content);
})->with("http")
	->with([
		['{"a":"b"}', "application/json"],
		[
			"<?xml version=\"1.0\"?>\n<employees max=\"2\"><employee><id>1</id><firstName>Tom</firstName><lastName>Cruise</lastName><photo>https://jsonformatter.org/img/tom-cruise.jpg</photo></employee><employee><id>2</id><firstName>Maria</firstName><lastName>Sharapova</lastName><photo>https://jsonformatter.org/img/Maria-Sharapova.jpg</photo></employee></employees>\n",
			"application/xml"
		],

	]);


test("route parse literal post params", function (GuzzleHttp\Client $http, $content, $contentType, $json_content) {
	$response = $http->request('GET', 'http://localhost:8080/post-params', [
		"body" => $content,
		"headers" => [
			"Content-Type" => $contentType
		]
	]);
	expect($response->getStatusCode())->toEqual(200);
	expect(json_decode($response->getBody(), true))->toEqual($json_content);
})->with("http")
	->with([
		['{"a":"b"}', "application/json", ["a" => "b"]],
		[
			"<?xml version=\"1.0\"?>\n<employees max=\"2\"><employee><id>1</id><firstName>Tom</firstName><lastName>Cruise</lastName><photo>https://jsonformatter.org/img/tom-cruise.jpg</photo></employee><employee><id>2</id><firstName>Maria</firstName><lastName>Sharapova</lastName><photo>https://jsonformatter.org/img/Maria-Sharapova.jpg</photo></employee></employees>\n",
			"application/xml",
			[
				"@root" => "employees",
				"@attributes" => [
					"max" => "2"
				],
				"employee" => [
					[
						'id' => '1',
						'firstName' => 'Tom',
						'lastName' => 'Cruise',
						'photo' => 'https://jsonformatter.org/img/tom-cruise.jpg'
					],
					[
						'id' => '2',
						'firstName' => 'Maria',
						'lastName' => 'Sharapova',
						'photo' => 'https://jsonformatter.org/img/Maria-Sharapova.jpg'
					],
				]
			]
		],

	]);
