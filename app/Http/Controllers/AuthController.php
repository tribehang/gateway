<?php

namespace App\Http\Controllers;

use App\Models\User;
use EllipseSynergie\ApiResponse\Contracts\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @var Client
     */
    public $guzzleClient;

    /**
     * @var Response
     */
    public $response;

    public function __construct(Client $guzzleClient, Response $response)
    {
        $this->guzzleClient = $guzzleClient;
        $this->response = $response;
    }

    /*
     * @todo - remove test, signup and signin functions - keep for testing for now
     */
    public function test(Request $request)
    {
        dd($request->gateway);
        return 'test';
    }

    public function signUp(Request $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return $user;
    }

    public function signIn(Request $request)
    {
        try {
            $clientResponse = $this->guzzleClient->post(env('APP_API_OAUTH_ENDPOINT'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $request->client_id,
                    'client_secret' => $request->client_secret,
                    'username' => $request->username,
                    'password' => $request->password,
                ],
            ]);

            return $this->response->withArray(
                json_decode((string) $clientResponse->getBody(), true)
            );
        } catch (ClientException $e) {
            return $this->response->errorUnauthorized(
                $e->getMessage()
            );
        }
    }
}
