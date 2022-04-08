<?php

declare(strict_types=1);


namespace App\Infrastructure\Exception\Handler;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class ExceptionListener
{
    private $debug;

    /**
     * ExceptionListener constructor.
     * @param bool $debug
     */
    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }


    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $response = $event->getResponse();

        if (!$response instanceof JsonResponse) {
            $exception = $event->getException();
            $exceptionCode = $exception->getCode() ?: 500;

            if ($exception instanceof \LengthException) {
                $exceptionCode = JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE;
            }

            $message = $exception->getMessage();

            if ($this->debug) {
                $message = [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                ];
            }

            if (!$this->debug && $exceptionCode >= 500) {
                $message = 'Internal error';
            }

            $newResponse = new JsonResponse(
                [
                    'error' => true,
                    'payload' => $message,
                ],
                $exceptionCode
            );

            $event->setResponse($newResponse);
        }
    }
}