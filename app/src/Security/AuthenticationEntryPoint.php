<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if (str_starts_with($request->getPathInfo(), '/api')) {
            return new JsonResponse([
                'error' => 'Authentication required',
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            return new Response('Authentication required', Response::HTTP_UNAUTHORIZED);
        }
    }
}
