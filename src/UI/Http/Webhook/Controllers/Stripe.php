<?php

declare(strict_types=1);


namespace App\UI\Http\Webhook\Controllers;


use App\Infrastructure\EventBus\EventBus;
use App\Infrastructure\Stripe\Events\WebhookReceivedEvent;
use App\Infrastructure\Stripe\StripeAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class Stripe
{
    private $stripeWebhookSigningSecret;
    private $stripeAdapter;
    private $eventBus;

    /**
     * Stripe constructor.
     * @param string $stripeWebhookSigningSecret
     * @param StripeAdapter $stripeAdapter
     * @param EventBus $eventBus
     */
    public function __construct(string $stripeWebhookSigningSecret, StripeAdapter $stripeAdapter, EventBus $eventBus)
    {
        $this->stripeWebhookSigningSecret = $stripeWebhookSigningSecret;
        $this->stripeAdapter = $stripeAdapter;
        $this->eventBus = $eventBus;
    }


    /**
     * @Route(path="/stripe", methods={"POST"})
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $isValid = $this->stripeAdapter->isValidWebhookRequest($request, $this->stripeWebhookSigningSecret);

        if (!$isValid) {
            return new JsonResponse('NOK', JsonResponse::HTTP_BAD_REQUEST);
        }

        $rawContent = $request->getContent();
        $content = json_decode($rawContent, true);

        try {
            $this->eventBus->dispatchEvent(new WebhookReceivedEvent($content));
        } catch (\Throwable $exception) {
            return new JsonResponse('NOK', JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('OK');
    }

    /**
     * @Route(path="/test", methods={"POST"})
     * @return JsonResponse
     */
    public function testPost(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        $content = json_decode($rawContent, true);

        try {
            $this->eventBus->dispatchEvent(new WebhookReceivedEvent($content));
        } catch (\Throwable $exception) {
            return new JsonResponse($exception->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('OK');

    }


}
