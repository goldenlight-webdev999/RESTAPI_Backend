<?php

declare(strict_types=1);


namespace App\Application\Command\User\SynchronizeUserRoles;


use App\Application\Query\Subscription\GetUserLiveSubscriptions\GetUserLiveSubscriptionsQuery;
use App\Domain\Subscription\Subscription;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use League\Tactician\CommandBus;

final class SynchronizeUserRolesHandler
{
    private $userRepository;
    private $commandBus;

    /**
     * SynchronizeUserRolesHandler constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        CommandBus $commandBus
    )
    {
        $this->userRepository = $userRepository;
        $this->commandBus = $commandBus;
    }

    public function handle(SynchronizeUserRolesCommand $command): void
    {
        $user = $this->userRepository->get($command->getUserId());

        $userSubscriptions = $this->commandBus->handle(new GetUserLiveSubscriptionsQuery($user));

        $subscriptionType = Subscription::TYPE_FREE;

        if ($userSubscriptions) {

            foreach ($userSubscriptions as $subscription) {
                /**
                 * @var Subscription $subscription
                 */
                switch ($subscription->getSubscriptionStatus()) {
                    case Subscription::STATUS_CANCELLED:
                    case Subscription::STATUS_ACTIVE:
                        $subscriptionType = $subscription->getType();
                        break;
                }
            }
        }

        $userRoles = [
            User::ROLE_USER,
        ];

        switch ($subscriptionType) {
            case Subscription::TYPE_BASIC:
                $userRoles[] = User::ROLE_USER_BASIC;
                break;
            case Subscription::TYPE_ADVANCE:
                $userRoles[] = User::ROLE_USER_PRO;
                break;
            case Subscription::TYPE_ENTERPRISE:
                $userRoles[] = User::ROLE_USER_BUSINESS;
                break;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            $userRoles[] = User::ROLE_ADMIN;
        }

        if (array_diff($userRoles, $user->getRoles()) || array_diff($user->getRoles(), $userRoles)) {
            $user->setRoles($userRoles);
            $this->userRepository->save($user);
        }
    }
}