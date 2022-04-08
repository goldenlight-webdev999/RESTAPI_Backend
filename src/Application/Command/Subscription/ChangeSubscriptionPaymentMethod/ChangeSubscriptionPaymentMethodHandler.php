<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\ChangeSubscriptionPaymentMethod;


use App\Application\Query\Subscription\GetUserLiveSubscriptions\GetUserLiveSubscriptionsQuery;
use App\Domain\Subscription\Interfaces\PaymentGatewayServiceInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use League\Tactician\CommandBus;

final class ChangeSubscriptionPaymentMethodHandler
{
    private $paymentGatewayService;
    private $subscriptionRepository;
    private $commandBus;

    /**
     * ChangeSubscriptionPaymentMethodHandler constructor.
     * @param PaymentGatewayServiceInterface $paymentGatewayService
     * @param SubscriptionRepositoryInterface $subscriptionRepository
     * @param CommandBus $commandBus
     */
    public function __construct(
        PaymentGatewayServiceInterface $paymentGatewayService,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CommandBus $commandBus
    )
    {
        $this->paymentGatewayService = $paymentGatewayService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->commandBus = $commandBus;
    }

    public function handle(ChangeSubscriptionPaymentMethodCommand $command): void
    {
        $subscriptions = $this->commandBus->handle(
            new GetUserLiveSubscriptionsQuery($command->getUser())
        );

        if (!$subscriptions) {
            throw new \LogicException(sprintf('User %s does not have any subscription', $command->getUser()->getId()->toString()));
        }

        if ($subscriptions) {
            $this->paymentGatewayService->updateSubscriptionPaymentMethodData($subscriptions, $command->getPaymentMethodData());

            foreach ($subscriptions as $subscription) {
                /**
                 * @var Subscription $subscription
                 */
                $this->subscriptionRepository->save($subscription);
            }
        }
    }
}