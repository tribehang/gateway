<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Transformers\UserTransformer;
use App\Models\User;
use EllipseSynergie\ApiResponse\Contracts\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var Response
     */
    public $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
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

    public function updateUser(UserUpdateRequest $request)
    {
        $user = $request->user();

        $user->update([
            'username' => $request->username,
        ]);

        return $this->response->withItem(
            $user, new UserTransformer()
        )->setStatusCode(200);
    }
}
