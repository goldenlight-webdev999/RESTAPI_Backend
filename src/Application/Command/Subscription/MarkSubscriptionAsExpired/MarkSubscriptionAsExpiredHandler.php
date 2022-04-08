<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\MarkSubscriptionAsExpired;

use App\Application\Command\CommandHandlerInterface;
use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\User\Repository\UserRepositoryInterface;
use League\Tactician\CommandBus;

final class MarkSubscriptionAsExpiredHandler implements CommandHandlerInterface
{
    private $userRepository;
    private $subscriptionRepository;
    private $commandBus;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CommandBus $commandBus
    )
    {
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->commandBus = $commandBus;
    }


    public function handle(MarkSubscriptionAsExpiredCommand $command): void
    {
        $user = $this->userRepository->get($command->getUserSubscriptionId());
        $subscriptions = $this->subscriptionRepository->getSubscriptionsByUser($user);

        $lastSubscriptions = array_slice($subscriptions->toArray(), -2, 2);

        if (count($lastSubscriptions)) {
            /**
             * @var Subscription $newSubscription
             * @var Subscription $oldSubscription
             */
            $newSubscription = end($lastSubscriptions);
            $oldSubscription = reset($lastSubscriptions);

            /**
             * An user can only have 2 live subscriptions, we find the last one comparing the start date
             */
            if ($oldSubscription->getDateStart() > $newSubscription->getDateStart()) {
                list($oldSubscription, $newSubscription) = [$newSubscription, $oldSubscription];
            }

            $newSubscription->setSubscriptionStatus(Subscription::STATUS_EXPIRED);
            $this->subscriptionRepository->save($newSubscription);
        }

        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($command->getUserSubscriptionId())
        );
    }
}