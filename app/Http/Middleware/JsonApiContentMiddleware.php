<?php

namespace App\Http\Middleware;

use Closure;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;

class JsonApiContentMiddleware
{
    const HTTP_METHODS = [
        'POST', 'PUT', 'PATCH'
    ];

    protected $errorReference = [
        JSON_ERROR_NONE => 'No error has occurred.',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
        JSON_ERROR_SYNTAX => 'Syntax error.',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
        JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
        JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
        JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
    ];

    const JSON_UNKNOWN_ERROR = 'Unknown error.';

    /**
     * @var Response
     */
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if (empty($request->getContent())) {
            return $next($request);
        }

        $parsedBody = $this->convertNullToString(
            json_decode($request->getContent(), true)
        );

        if (empty($parsedBody)) {
            return $next($request);
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $this->buildErrorResponse($this->getLastErrorMessage(json_last_error()));
        }

        if (in_array($request->getMethod(), self::HTTP_METHODS)) {
            $request->merge($parsedBody);
        }

        return $next($request);
    }

    protected function convertNullToString($body): array
    {
        return is_null($body) ? [] : $body;
    }

    protected function buildErrorResponse($details)
    {
        return $this->response->setStatusCode(409)->withError($details, null);
    }

    protected function getLastErrorMessage($errorCode)
    {
        if (! array_key_exists($errorCode, $this->errorReference)) {
            return self::JSON_UNKNOWN_ERROR;
        }

        return $this->errorReference[$errorCode];
    }
}
