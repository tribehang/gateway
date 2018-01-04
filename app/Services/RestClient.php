<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class RestClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $guzzleParams = [
        'headers' => [],
    ];

    /*
     * @var string
     */
    protected $url;

    /*
     * @var string
     */
    protected $method;

    /**
     * @var int
     */
    const USER_ID_ANONYMOUS = -1;

    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
        $this->injectHeaders($request);
    }

    private function injectHeaders(Request $request)
    {
        $this->setHeaders(
            [
                'X-User' => $request->user()->id ?? self::USER_ID_ANONYMOUS,
                'X-Token-Scopes' => $request->user() && ! empty($request->user()->token()) ? implode(',', $request->user()->token()->scopes) : '',
                'X-Client-Ip' => $request->getClientIp(),
                'User-Agent' => $request->header('User-Agent'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        );
    }

    public function getHeaders()
    {
        return $this->guzzleParams['headers'];
    }

    public function setTimeout(int $timeout)
    {
        $this->guzzleParams['timeout'] = $timeout;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->guzzleParams['headers'] = $headers;
    }

    public function setBody(string $body)
    {
        $this->guzzleParams['body'] = $body;

        return $this;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = mb_strtolower($method);

        return $this;
    }

    public function getRequest(): Response
    {
        return $this->client->{$this->method}($this->url, $this->guzzleParams);
    }
}
