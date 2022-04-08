<?php

declare(strict_types=1);


namespace App\Domain\Subscription\Factory;


use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\User;

final class SubscriptionFactory
{
    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    /**
     * SubscriptionFactory constructor.
     * @param SubscriptionRepositoryInterface $subscriptionRepository
     */
    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param User $user
     * @param SubscriptionTypeEnum $subscriptionTypeEnum
     * @return Subscription
     * @throws \Exception
     */
    public function createSubscription(
        User $user,
        SubscriptionTypeEnum $subscriptionTypeEnum
    ): Subscription
    {
        $subscription = $this->subscriptionRepository->newInstance();

        $subscription->setUser($user);
        $subscription->setType((string)$subscriptionTypeEnum);
        $subscription->setDateAdded(new \DateTimeImmutable());
        $subscription->setDateUpdated(new \DateTimeImmutable());
        $subscription->setSubscriptionStatus(Subscription::STATUS_PENDING);

        return $subscription;
    }
}
