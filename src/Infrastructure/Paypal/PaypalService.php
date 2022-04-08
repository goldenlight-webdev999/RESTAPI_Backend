<?php

declare(strict_types=1);


namespace App\Infrastructure\Paypal;


use App\Domain\Subscription\Events\UserSubscriptionMetadataChangedEvent;
use App\Domain\Subscription\Interfaces\PaymentGatewayServiceInterface;
use App\Domain\Subscription\Interfaces\PaymentGatewaySubscriptionTypeAdapterInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\User;
use App\Infrastructure\EventBus\EventBus;
use App\Infrastructure\Paypal\Factory\CustomerSubscriptionFactory;
use App\Infrastructure\Paypal\Repository\CustomerRepository;
use App\Infrastructure\Paypal\Repository\CustomerSubscriptionRepository;

final class PaypalService implements PaymentGatewayServiceInterface
{
    private $customerRepository;
    private $paypalAdapter;
    private $paymentGatewaySubscriptionTypeAdapter;
    private $customerSubscriptionFactory;
    private $customerSubscriptionRepository;
    private $eventBus;

    public function __construct(
        CustomerRepository $customerRepository,
        PaypalAdapter $paypalAdapter,
        PaymentGatewaySubscriptionTypeAdapterInterface $paymentGatewaySubscriptionTypeAdapter,
        CustomerSubscriptionFactory $customerSubscriptionFactory,
        CustomerSubscriptionRepository $customerSubscriptionRepository,
        EventBus $eventBus
    )
    {
        $this->customerRepository = $customerRepository;
        $this->paypalAdapter = $paypalAdapter;
        $this->paymentGatewaySubscriptionTypeAdapter = $paymentGatewaySubscriptionTypeAdapter;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->eventBus = $eventBus;
    }

    public function testsend()
    {
    }

    /**
     * @param User $user
     * @param Subscription $subscription
     * @param array $subscriptionData
     * @throws \Exception
     */
    public function createSubscription(User $user, Subscription $subscription, array $subscriptionData): void
    {
        $customer = $this->customerRepository->getByUser($user);

        if (!$customer) {
            $rawCustomer = $this->paypalAdapter->createCustomer($user, $subscriptionData);

            $customer = $this->customerRepository->newInstance();
            $customer->setUser($user);
            $customer->setCustomerKey($rawCustomer->id);
            $customer->setDateAdded(new \DateTimeImmutable());

            $this->customerRepository->save($customer);

            //Refresh it
            $customer = $this->customerRepository->getByUser($user);
        } else {
            $this->paypalAdapter->updateCustomerPaymentMethod($customer, $subscriptionData);
        }

        if ($customer->getSubscription()) {
            $customerSubscription = $this->customerSubscriptionRepository->get($customer->getSubscription()->getId());
            $this->customerSubscriptionRepository->delete($customerSubscription);
            //throw new \LogicException(sprintf('User %s already has a subscription', $user->getId()->toString()));
        }

        $stripePlan = $this->paymentGatewaySubscriptionTypeAdapter->getPaymentGatewaySubscriptionType(
            SubscriptionTypeEnum::build($subscription->getType())
        );

        $rawSubscription = $this->paypalAdapter->createSubscription($customer, $stripePlan);

        $newSubscription = $this->customerSubscriptionFactory->createSubscription(
            $customer,
            $rawSubscription->id
        );

        $this->customerSubscriptionRepository->save($newSubscription);

        $subscription->setPaymentMethodMetadata(
            $this->paypalAdapter->getPaymentMethodDataFromTokenData($subscriptionData)
        );
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        $user = $subscription->getUser();
        $customer = $this->customerRepository->getByUser($user);

        $customerSubscription = $customer->getSubscription();

        if ($customerSubscription && $customerSubscription->getId()) {
            $customerSubscription = $this->customerSubscriptionRepository->get($customerSubscription->getId());

            $this->paypalAdapter->cancelSubscription($customerSubscription);
            $this->customerSubscriptionRepository->delete($customerSubscription);
        }
    }

    public function changeSubscriptionType(Subscription $oldSubscription, Subscription $newSubscription): void
    {
        $oldSubscriptionType = SubscriptionTypeEnum::build($oldSubscription->getType());
        $newSubscriptionType = SubscriptionTypeEnum::build($newSubscription->getType());

        $newPlan = $this->paymentGatewaySubscriptionTypeAdapter->getPaymentGatewaySubscriptionType($newSubscriptionType);

        $user = $oldSubscription->getUser();
        $customer = $this->customerRepository->getByUser($user);

        $this->paypalAdapter->changeSubscriptionPlan($customer->getSubscription(), $newPlan);

        //It is a subscription upgrade, we need to ask to stripe to generate an new invoice for the difference
        if ($newSubscriptionType->isGreaterThan($oldSubscriptionType)) {
            $this->paypalAdapter->emitInvoiceForPendingPaymentSubscription($customer->getSubscription());
        }

        $newSubscription->setPaymentMethodMetadata($oldSubscription->getPaymentMethodMetadata());
    }

    public function updateSubscriptionPaymentMethodData(array $subscriptions, array $paymentMethodData): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = reset($subscriptions);
        $customer = $this->customerRepository->getByUser($subscription->getUser());

        $this->paypalAdapter->updateCustomerPaymentMethod($customer, $paymentMethodData);

        $paymentMetadata = $this->paypalAdapter->getPaymentMethodDataFromTokenData($paymentMethodData);
        foreach ($subscriptions as $subscriptionItem) {
            /**
             * @var Subscription $subscriptionItem
             */
            $subscriptionItem->setPaymentMethodMetadata($paymentMetadata);
        }
    }
}