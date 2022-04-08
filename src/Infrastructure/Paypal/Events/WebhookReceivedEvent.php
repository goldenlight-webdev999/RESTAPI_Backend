<?php

declare(strict_types=1);

namespace App\Infrastructure\Paypal\Events;

use App\Domain\Event\Interfaces\EventInterface;
use Symfony\Component\EventDispatcher\Event;

final class WebhookReceivedEvent extends Event implements EventInterface
{
    private $webhookData;

    public static function getEventName(): string
    {
        return self::class;
    }

    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
    }

    /**
     * @return array
     */
    public function getWebhookData(): array
    {
        return $this->webhookData;
    }
}