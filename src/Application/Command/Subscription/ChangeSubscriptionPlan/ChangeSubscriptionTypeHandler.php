<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\ChangeSubscriptionPlan;


use App\Application\Command\CommandHandlerInterface;
use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\Subscription\Interfaces\PaymentGatewayServiceInterface;
use App\Domain\Subscription\Factory\SubscriptionFactory;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use League\Tactician\CommandBus;

final class ChangeSubscriptionTypeHandler implements CommandHandlerInterface
{
    private $paymentGatewayService;
    private $subscriptionRepository;
    private $subscriptionFactory;
    private $commandBus;

    public function __construct(
        PaymentGatewayServiceInterface $paymentGatewayService,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionFactory $subscriptionFactory,
        CommandBus $commandBus
    )
    {
        $this->paymentGatewayService = $paymentGatewayService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->commandBus = $commandBus;
    }

    /**
     * @param ChangeSubscriptionTypeCommand $command
     * @throws \Throwable
     */
    public function handle(ChangeSubscriptionTypeCommand $command): void
    {
        $userSubscriptions = $this->subscriptionRepository->getLiveSubscriptionsByUser($command->getUser());

        if ($userSubscriptions->isEmpty()) {
            throw new \LogicException(sprintf('User %s doesn\'t have any active subscription', $command->getUser()->getId()->toString()));
        }

        if ($userSubscriptions->count() > 1) {
            throw new \LogicException(sprintf('User %s has a pending subscription change', $command->getUser()->getId()->toString()));
        }

        /**
         * @var Subscription $currentSubscription
         */
        $currentSubscription = $userSubscriptions->first();
        $currentSubscriptionType = SubscriptionTypeEnum::build($currentSubscription->getType());

        if ($currentSubscriptionType->isEquals($command->getSubscriptionType())) {
            throw new \LogicException(sprintf('User "%s" is already subscribed to the "%s" plan', $command->getUser()->getId()->toString(), (string)$command->getSubscriptionType()));
        }

        if ($currentSubscription->getSubscriptionStatus() === Subscription::STATUS_PENDING) {
            throw new \LogicException(sprintf('User "%s" has a pending payment for the "%s" plan', $command->getUser()->getId()->toString(), (string)$command->getSubscriptionType()));
        }

        $subscription = $this->subscriptionFactory->createSubscription(
            $command->getUser(),
            $command->getSubscriptionType()
        );

        $dateStart = $currentSubscription->getDateEnd()->add(new \DateInterval('PT1S'));
        $dateEnd = $dateStart->add(new \DateInterval('P1M'));

        $subscription->setDateStart($dateStart);
        $subscription->setDateEnd($dateEnd);

        try {
            $this->paymentGatewayService->changeSubscriptionType($currentSubscription, $subscription);
            $this->subscriptionRepository->save($subscription);
        } catch (\Throwable $exception) {
            throw $exception;
        }

        // It is a upgrade, the old one must be cancelled right now
        if ($command->getSubscriptionType()->isGreaterThan($currentSubscriptionType)) {
            $currentSubscription->setSubscriptionStatus(Subscription::STATUS_CANCELLED);
            $this->subscriptionRepository->save($currentSubscription);
        }

        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($command->getUser()->getId())
        );
    }


}