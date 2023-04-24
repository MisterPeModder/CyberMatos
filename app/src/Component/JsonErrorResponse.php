<?php

namespace App\Component;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonErrorResponse extends JsonResponse
{
    public function __construct(string|\Exception $error, int $status = Response::HTTP_BAD_REQUEST, array $headers = [], bool $json = false)
    {
        if ($error instanceof \Exception) {
            $error = $error->getMessage();
        }
        parent::__construct(['error' => $error], $status, $headers, $json);
    }
}
