<?php

namespace App\Responses;

use EllipseSynergie\ApiResponse\Laravel\Response;

class ApiResponse extends Response
{
    public function withJson($content = '')
    {
        return $this->withArray(
           json_decode($content, true)
        );
    }
}
