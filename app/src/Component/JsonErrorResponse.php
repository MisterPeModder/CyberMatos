<?php

namespace App\Component;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    public function __construct(string|\Exception $error, int $status = 400, array $headers = [], bool $json = false)
    {
        if ($error instanceof \Exception) {
            $error = $error->getMessage();
        }
        parent::__construct(['error' => $error], $status, $headers, $json);
    }
}
