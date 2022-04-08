<?php

declare(strict_types=1);


namespace App\Infrastructure\EndPointLimits\Listeners;


use App\Infrastructure\EndPointLimits\Exceptions\LimitExceededException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class LimitExceededExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof LimitExceededException) {
            return;
        }

        $responseData = [
            'error' => $exception->getMessage(),
        ];

        $event->setResponse(
            new JsonResponse(
                $responseData,
                $event->getException()->getCode()
            )
        );
    }

}