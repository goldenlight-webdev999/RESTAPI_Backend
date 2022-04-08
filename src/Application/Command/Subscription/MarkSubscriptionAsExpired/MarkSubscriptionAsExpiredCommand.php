<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\MarkSubscriptionAsExpired;


use Ramsey\Uuid\UuidInterface;

final class MarkSubscriptionAsExpiredCommand
{
    private $userSubscriptionId;

    public function __construct(UuidInterface $userSubscriptionId)
    {
        $this->userSubscriptionId = $userSubscriptionId;
    }

    /**
     * @return UuidInterface
     */
    public function getUserSubscriptionId(): UuidInterface
    {
        return $this->userSubscriptionId;
    }
}