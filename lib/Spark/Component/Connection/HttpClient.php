<?php

namespace Spark\Component\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class HttpClient
{
    private ?Client $client = null;

    public function __construct()
    {
    }

    public function create( bool $asynchronous = false)
    {
            $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function request()
    {
        $response = $this->client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
        // 200
        // 'application/json; charset=utf8'
        // '{"id": 1420053, "name": "guzzle", ...}'
        return [$response->getStatusCode(), $response->getHeaderLine('content-type'), $response->getBody()];

        $responseBody = [];
        $request = new Request('GET', 'http://httpbin.org');
        $promise = $this->client->sendAsync($request)->then(function ($response) use ($responseBody) {
            $responseBody.array_push($response->getStatusCode(), $response->getHeaderLine('content-type'), $response->getBody());
        });

        $promise->wait();
        $promise->then();
        return $responseBody;
    }

    public function createRequestData()
    {

    }
    public function createData()
    {

    }
}