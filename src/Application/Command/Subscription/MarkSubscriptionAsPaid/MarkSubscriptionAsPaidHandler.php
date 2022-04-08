<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\MarkSubscriptionAsPaid;


use App\Application\Command\CommandHandlerInterface;
use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\Repository\UserRepositoryInterface;
use League\Tactician\CommandBus;

final class MarkSubscriptionAsPaidHandler implements CommandHandlerInterface
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

    /**
     * @param MarkSubscriptionAsPaidCommand $command
     * @throws \Exception
     */
    public function handle(MarkSubscriptionAsPaidCommand $command): void
    {
        $user = $this->userRepository->get($command->getUserId());
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

            /**
             * If there are two live subscriptions and the last one is pending means that we are dealing with a subscription upgrade/downgrade
             */
            $isSubscriptionChange = (count($lastSubscriptions) > 1) && ($newSubscription->getSubscriptionStatus() === Subscription::STATUS_PENDING);

            if ($isSubscriptionChange) {
                $oldSubscriptionType = SubscriptionTypeEnum::build($oldSubscription->getType());
                $newSubscriptionType = SubscriptionTypeEnum::build($newSubscription->getType());

                /**
                 * It is a subscription upgrade, the user paid for the difference, the old subscription ends here
                 */
                if ($newSubscriptionType->isGreaterThan($oldSubscriptionType)) {
                    $oldSubscription->setDateEnd($command->getDatePaid()->sub(new \DateInterval('PT1S')));
                    $this->subscriptionRepository->save($oldSubscription);
                }
            }

            $newSubscription->setDateStart($command->getDatePaid());
            $newSubscription->setDateEnd($command->getDatePaid()->add(new \DateInterval('P1M')));
            $newSubscription->setSubscriptionStatus(Subscription::STATUS_ACTIVE);

            $this->subscriptionRepository->save($newSubscription);
        }

        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($command->getUserId())
        );
    }
}
