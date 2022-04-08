<?php

declare(strict_types=1);


namespace App\Application\Query\Subscription\GetUserLiveSubscriptions;


use App\Application\Query\QueryHandlerInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;

final class GetUserLiveSubscriptionsHandler implements QueryHandlerInterface
{
    private $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param GetUserLiveSubscriptionsQuery $query
     * @return Subscription[] Subscription list ordered by ending date
     */
    public function handle(GetUserLiveSubscriptionsQuery $query): array
    {
        $subscriptions = $this->subscriptionRepository->getLiveSubscriptionsByUser($query->getUser());

        $result = [];

        switch ($subscriptions->count()) {
            case 0:
                // Current user does not have any valid subscription or the last one is expired
                $allSubscriptions =  $this->subscriptionRepository->getSubscriptionsByUser($query->getUser());

                if ($allSubscriptions->count()) {
                    /**
                     * @var Subscription $lastSubscription
                     */
                    $lastSubscription = $allSubscriptions->last();

                    if ($lastSubscription->getSubscriptionStatus() === Subscription::STATUS_CANCELLED) {
                        if ($lastSubscription->getDateEnd() > (new \DateTimeImmutable())) {
                            $result[] = $allSubscriptions->last();
                        }
                    }
                }
                break;
            case 1:
                /**
                 * @var Subscription $subscription
                 */
                $subscription = $subscriptions->first();
                $result[] = $subscription;
                break;
            case 2:
                /**
                 * @var Subscription $firstSubscription
                 * @var Subscription $lastSubscription
                 */
                $firstSubscription = $subscriptions->first();
                $lastSubscription = $subscriptions->last();

                if ($firstSubscription->getDateEnd() > $lastSubscription->getDateEnd()) {
                    list($firstSubscription, $lastSubscription) = [$lastSubscription, $firstSubscription];
                }

                $result[] = $firstSubscription;
                $result[] = $lastSubscription;

                break;
        }

        return $result;
    }
}