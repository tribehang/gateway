<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Transformers\UserTransformer;
use App\Repositories\UserRepository;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var Response
     */
    public $response;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(Response $response, UserRepository $usersRepository)
    {
        $this->response = $response;
        $this->userRepository = $usersRepository;
    }

    public function get(Request $request)
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

    public function update(UserUpdateRequest $request)
    {
        $user = $request->user();

        $user->update([
            'username' => $request->username,
        ]);

        return $this->response->withItem(
            $user, new UserTransformer()
        )->setStatusCode(200);
    }

    public function create(UserCreateRequest $request)
    {
        $user = $this->userRepository->create(
            $request->all()
        );

        return $this->response->withItem(
            $user,
            new UserTransformer()
        )->setStatusCode(201);
    }
}
