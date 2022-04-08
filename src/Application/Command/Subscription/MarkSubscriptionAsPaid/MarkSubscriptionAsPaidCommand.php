<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\MarkSubscriptionAsPaid;


use Ramsey\Uuid\UuidInterface;

final class MarkSubscriptionAsPaidCommand
{
    private $userId;
    private $datePaid;

    /**
     * MarkSubscriptionAsPaidCommand constructor.
     * @param UuidInterface $userSubscriptionId
     * @param \DateTimeImmutable $datePaid
     */
    public function __construct(
        UuidInterface $userSubscriptionId,
        \DateTimeImmutable $datePaid)
    {
        $this->userId = $userSubscriptionId;
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