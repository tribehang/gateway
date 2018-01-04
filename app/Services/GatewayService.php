<?php

namespace App\Services;

use App\Responses\ApiResponse;
use ErrorException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use InvalidArgumentException;

class GatewayService
{
    const DEFAULT_METHOD = 'GET';

    const DEFAULT_TIMEOUT = 10;

    /*
     * @var Request
     */
    protected $request;

    /*
     * @var RestClient
     */
    protected $client;

    /*
     * @var ApiResponse
     */
    protected $response;

    public function __construct(Request $request, ApiResponse $response, RestClient $client)
    {
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function handle(array $params)
    {
        $config = config('gateway');

        try {
            $url = $config['services'][$params['service']]['url'] . str_replace($params['service'], '', $params['target']);
        } catch (ErrorException $e) {
            return $this->response->errorNotFound('Service not found!');
        }
        
        $this->client
            ->setTimeout(env('APP_REQUEST_TIMEOUT', self::DEFAULT_TIMEOUT))
            ->setBody($params['content'])
            ->setUrl($url)
            ->setMethod($params['method'] ?? self::DEFAULT_METHOD);

        try {
            $request = $this->client->getRequest();
            $response = $this->response->setStatusCode($request->getStatusCode())
                ->withJson($request->getBody()->getContents());
        } catch (ClientException | ConnectException | InvalidArgumentException $e) {
            if ($this->isNotFoundRequest($e->getCode())) {
                return $this->response->errorNotFound('Service not found!');
            }

            $response = $this->response->setStatusCode($e->getCode())
                ->withJson($e->getResponse()->getBody()->getContents());
        }

        return $response;
    }

    protected function isNotFoundRequest(int $statusCode): bool
    {
        return ($statusCode === 404 || $statusCode === 0 || $statusCode >= 500);
    }
}
