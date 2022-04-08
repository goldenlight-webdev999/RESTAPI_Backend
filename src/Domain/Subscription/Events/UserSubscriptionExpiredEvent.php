<?php

declare(strict_types=1);


namespace App\Domain\Subscription\Events;


use App\Domain\Event\Interfaces\EventInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\Event;

final class UserSubscriptionExpiredEvent extends Event implements EventInterface
{
    private $userId;

    public static function getEventName(): string
    {
        return self::class;
    }

    public function __construct(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return UuidInterface
     */
    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}