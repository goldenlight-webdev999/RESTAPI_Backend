<?php

declare(strict_types=1);


namespace App\Infrastructure\Exception\Handler;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

final class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $responseContent = [
            'error' => true,
            'payload' => $accessDeniedException->getMessage(),
        ];

        return new JsonResponse(
            $responseContent,
            $accessDeniedException->getCode()
        );
    }
}