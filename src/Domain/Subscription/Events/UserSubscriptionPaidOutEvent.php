<?php

declare(strict_types=1);


namespace App\Domain\Subscription\Events;


use App\Domain\Event\Interfaces\EventInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\Event;

final class UserSubscriptionPaidOutEvent extends Event implements EventInterface
{
    private $userId;
    private $datePaid;

    public static function getEventName(): string
    {
        return self::class;
    }

    public function __construct(UuidInterface $userId, \DateTimeImmutable $datePaid)
    {
        $this->userId = $userId;
        $this->datePaid = $datePaid;
    }

    /**
     * @return UuidInterface
     */
    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDatePaid(): \DateTimeImmutable
    {
        return $this->datePaid;
    }
}