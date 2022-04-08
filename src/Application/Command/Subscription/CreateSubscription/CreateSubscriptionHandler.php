<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\CreateSubscription;


use App\Application\Command\CommandHandlerInterface;
use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\Subscription\Interfaces\PaymentGatewayServiceInterface;
use App\Domain\Subscription\Factory\SubscriptionFactory;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\User\User;
use League\Tactician\CommandBus;

final class CreateSubscriptionHandler implements CommandHandlerInterface
{
    private $paymentGatewayService;
    private $subscriptionRepository;
    private $subscriptionFactory;
    private $commandBus;

    /**
     * SetUpSubscriptionHandler constructor.
     */
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
     * @param CreateSubscriptionCommand $command
     * @throws \Throwable
     */
    public function handle(CreateSubscriptionCommand $command): void
    {
        $userSubscriptions = $this->subscriptionRepository->getLiveSubscriptionsByUser($command->getUser());

        $subscription = $this->subscriptionFactory->createSubscription(
            $command->getUser(),
            $command->getSubscriptionType()
        );

        /**
         * If there is any problem during the payment gateway subscription set up then we do a rollback
         */
        switch ($userSubscriptions->count()) {
            case 0:
                    $this->setUpFirstSubscription(
                        $command->getUser(),
                        $subscription,
                        $command->getPaymentMethodData()
                    );
                break;
            default:
                /**
                 * @var Subscription $lastSubscription
                 */
                $allSubscriptions = $userSubscriptions->toArray();
                $lastSubscription = end($allSubscriptions);

                if ($lastSubscription->getSubscriptionStatus() === Subscription::STATUS_CANCELLED) {
                    $this->setUpFirstSubscription(
                        $command->getUser(),
                        $subscription,
                        $command->getPaymentMethodData()
                    );
                } else {
                    throw new \LogicException(sprintf('User %s already has an active subscription', $command->getUser()->getId()->toString()));
                }
                break;
        }


        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($command->getUser()->getId())
        );
    }

    /**
     * @param Subscription $subscription
     * @throws \Exception
     */
    private function setUpFirstSubscription(User $user, Subscription $subscription, $subscrptionData): void
    {
        $subscription->setDateStart(new \DateTimeImmutable());
        $subscription->setDateEnd(new \DateTimeImmutable('now + 1 month'));

        $this->paymentGatewayService->createSubscription(
            $user,
            $subscription,
            $subscrptionData
        );

        $this->subscriptionRepository->save($subscription);
    }
}