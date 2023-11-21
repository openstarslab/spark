<?php

namespace Spark\Component\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class HttpClient
{
    private ?Client $client = null;

    public function __construct()
    {
    }

    public function create(string $base_uri, float $timeout)
    {
        $this->client = new Client([
            'base_uri' => $base_uri,
            $timeout => 2.0
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function request(HttpMethod $method, string $url)
    {
        //TODO Najepierw to ułożyć trzeba i potem po reszcie zagadnień przejdź
        try {
            $response = $this->client->request($method->value, $url);
        } catch (ClientException $e) {
            echo Psr7\Message::toString($e->getRequest());
            echo Psr7\Message::toString($e->getResponse());
        }
        // 200
        // 'application/json; charset=utf8'
        // '{"id": 1420053, "name": "guzzle", ...}'
        return [$response->getStatusCode(), $response->getHeaderLine('content-type'), $response->getBody()];

        $responseBody = [];
        $request = new Request('GET', 'http://httpbin.org');
//        $client->request('GET', 'http://httpbin.org', ['query' => 'foo=bar']); < PARAMETRY OBSŁUŻ
        //TODO Decyzja co z cookie
        $promise = $this->client->sendAsync($request)->then(function ($response) use ($responseBody) {
            $responseBody . array_push($response->getStatusCode(), $response->getHeaderLine('content-type'), $response->getBody());
        });
// Or, if you don't need to pass in a request instance:
        $promise = $client->requestAsync('GET', 'http://httpbin.org/get');

        $promise->wait();
        $promise->then();
        return $responseBody;
    }

    /**
     * @throws GuzzleException
     */
    public function multipleRequests(HttpMethod $method, string $url)
    {

        $client = new Client();

        $requests = function ($total) {
            $uri = 'http://127.0.0.1:8126/guzzle-server/perf';
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri);
            }
        };

        $pool = new Pool($client, $requests(100), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) {
                // this is delivered each successful response
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
            },
        ]);

// Initiate the transfers and create a promise
        $promise = $pool->promise();

// Force the pool of requests to complete.
        $promise->wait();

        // TODO ROZDZIEL Or using a closure that will return a promise once the pool calls the closure.

        $client = new Client();

        $requests = function ($total) use ($client) {
            $uri = 'http://127.0.0.1:8126/guzzle-server/perf';
            for ($i = 0; $i < $total; $i++) {
                yield function () use ($client, $uri) {
                    return $client->getAsync($uri);
                };
            }
        };

        $pool = new Pool($client, $requests(100));


    }

    public function createRequestData()
    {

    }

    public function createData()
    {

    }
}