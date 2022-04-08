<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\CancelAllSubscriptions;


use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\Subscription\Interfaces\PaymentGatewayServiceInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Infrastructure\Paypal\PaypalService;
use App\Infrastructure\Stripe\StripeService;
use League\Tactician\CommandBus;

final class CancelAllSubscriptionsHandler
{
    private $subscriptionRepository;
    private $paymentGateway;
    private $commandBus;
    private $paypalService;
    private $stripeService;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        PaymentGatewayServiceInterface $paypalpaymentGatewayService,
        PaypalService $paypalService,
        StripeService $stripeService,
        CommandBus $commandBus
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentGateway = $paypalpaymentGatewayService;
        $this->paypalService = $paypalService;
        $this->stripeService = $stripeService;
        $this->commandBus = $commandBus;
    }

    public function handle(CancelAllSubscriptionsCommand $command): void
    {
        $subscriptions = $this->subscriptionRepository->getLiveSubscriptionsByUser($command->getUser());

        foreach ($subscriptions as $subscription) {
            /**
             * @var Subscription $subscription
             */

            $paymentMetaData =$subscription->getPaymentMethodMetadata();
            $type = "stripe";

            if(isset($paymentMetaData['type'])){
                if($paymentMetaData['type'] == "paypal"){
                    $type = "paypal";
                }
            }

            if($command->serviceType() == "paypal" || $type == "paypal"){
                $this->paypalService->cancelSubscription($subscription);
            }else{
                $this->paymentGateway->cancelSubscription($subscription);
            }

            switch ($subscription->getSubscriptionStatus()) {
                case Subscription::STATUS_PENDING:
                    $this->subscriptionRepository->delete($subscription);
                    break;
                default:
                    $subscription->setSubscriptionStatus(Subscription::STATUS_CANCELLED);
                    // If we doing an upgrade we cancel the existing subscription immediately
                    // otherwise we leave it to expire
                    if ($command->isUpgrade()) {
                        $subscription->setDateEnd(new \DateTimeImmutable());
                    }
                    $this->subscriptionRepository->save($subscription);
                    break;
            }
        }

        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($command->getUser()->getId())
        );
    }
}
