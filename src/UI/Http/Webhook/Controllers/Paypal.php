<?php


namespace App\UI\Http\Webhook\Controllers;

use App\Infrastructure\EventBus\EventBus;
use App\Infrastructure\Paypal\Events\WebhookReceivedEvent;
use App\Infrastructure\Paypal\PaypalAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class Paypal
{
    private $paypalAdapter;
    private $eventBus;

    /**
     * Stripe constructor.
     * @param PaypalAdapter $paypalAdapter
     * @param EventBus $eventBus
     */
    public function __construct(PaypalAdapter $paypalAdapter, EventBus $eventBus)
    {
        $this->paypalAdapter = $paypalAdapter;
        $this->eventBus = $eventBus;
    }

    /**
     * @Route(path="/paypal", methods={"POST"})
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $isValid = $this->paypalAdapter->isValidWebhookRequest($request);

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
}