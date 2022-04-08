<?php

declare(strict_types=1);


namespace App\Application\Query\Application\GetUserApplicationsLifecycleTraffic;


use App\Application\Query\QueryHandlerInterface;
use App\Application\Query\Subscription\GetUserLiveSubscriptions\GetUserLiveSubscriptionsQuery;
use App\Domain\Log\Repository\LogCleanTaskRepositoryInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\User\User;
use League\Tactician\CommandBus;

final class GetUserApplicationsLifecycleTrafficHandler implements QueryHandlerInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    /**
     * @var LogCleanTaskRepositoryInterface
     */
    private $logCleanTaskRepository;

    /**
     * @param CommandBus $commandBus
     * @param SubscriptionRepositoryInterface $subscriptionRepository
     * @param LogCleanTaskRepositoryInterface $logCleanTaskRepository
     */
    public function __construct(
        CommandBus $commandBus,
        SubscriptionRepositoryInterface $subscriptionRepository,
        LogCleanTaskRepositoryInterface $logCleanTaskRepository
    )
    {
        $this->commandBus = $commandBus;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logCleanTaskRepository = $logCleanTaskRepository;
    }

    /**
     * @param GetUserApplicationsLifecycleTrafficQuery $query
     * @return int
     * @throws \Exception
     */
    public function handle(GetUserApplicationsLifecycleTrafficQuery $query): int
    {
        /**
         * We need to find the day of the cycle start
         */
        $cycleAnchor = $this->getCycleAnchor($query->getUser());

        $currentCycleStart = $this->getCurrentCycleStartDate($cycleAnchor);
        $currentCycleEnd = $currentCycleStart->add(new \DateInterval('P1M'));

        return $this->logCleanTaskRepository->getConsumedUploadBandwidthByApplicationInBytes(
            $query->getUser(),
            $query->getApplication(),
            $currentCycleStart,
            $currentCycleEnd
        );
    }

    /**
     * @param \DateTimeInterface $cycleAnchor
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    private function getCurrentCycleStartDate(\DateTimeInterface $cycleAnchor): \DateTimeImmutable
    {
        $day = (int)(new \DateTime())->format('j');
        $month = (int)(new \DateTime())->format('n');
        $year = (int)(new \DateTime())->format('Y');

        $cycleAnchorDay = (int)$cycleAnchor->format('j');

        if ($cycleAnchorDay > $day) {
            $month--;
        }

        $day = $cycleAnchorDay;

        if ($month < 1) {
            $month = 12;
            $year--;
        }

        /**
         * check if the day is greater than the last day of the month
         */
        $lastDayMonthCycle = (int)(new \DateTime(sprintf('%u-%u-1', $year, $month)))->format('t');
        if ($day > $lastDayMonthCycle) {
            $day = $lastDayMonthCycle;
        }

        return new \DateTimeImmutable(
            sprintf(
                '%u-%u-%u %u:%u:%u',
                $year,
                $month,
                $day,
                (int)$cycleAnchor->format('H'),
                (int)$cycleAnchor->format('i'),
                (int)$cycleAnchor->format('s')
            )
        );
    }

    /**
     * @param User $user
     * @return \DateTimeInterface
     *
     * It tries to get the last active subscription start date, if not then it will look for the last valid subscription
     * finally if everything fails it returns the sign up date
     */
    private function getCycleAnchor(User $user): \DateTimeInterface
    {
        $cycleAnchor = null;

        $liveSubscriptions = $this->commandBus->handle(new GetUserLiveSubscriptionsQuery($user));

        if ($liveSubscriptions) {
            /**
             * @var Subscription $subscription
             */
            $subscription = reset($liveSubscriptions);
            $cycleAnchor = $subscription->getDateStart();
        }

        if (!$cycleAnchor) {
            $userSubscriptions = $this->subscriptionRepository->getSubscriptionsByUser($user);

            if ($userSubscriptions->count()) {
                foreach ($userSubscriptions as $userSubscription) {
                    /**
                     * @var Subscription $userSubscription
                     */
                    if ($userSubscription->getSubscriptionStatus() !== Subscription::STATUS_PENDING) {
                        $cycleAnchor = $userSubscription->getDateStart();
                    }
                }
            }
        }

        if (!$cycleAnchor) {
            $cycleAnchor = $user->getDateAdded();
        }

        return $cycleAnchor;
    }
}