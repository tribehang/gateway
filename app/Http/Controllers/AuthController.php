<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Http\Transformers\UserTransformer;
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

    public function test(Request $request)
    {
        dd($request->gateway);
        return 'test';
    }

    public function getUser(Request $request)
    {
        $user = $request->user();

        if (is_null($user)) {
            return $this->response->errorNotFound('User Not Found!');
        }

        return $this->response->withItem(
            $user,
            new UserTransformer()
        )->setStatusCode(200);
    }

    public function signUp(SignUpRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return $this->response->withItem(
            $user,
            new UserTransformer()
        )->setStatusCode(201);
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
