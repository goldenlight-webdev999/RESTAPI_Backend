<?php

declare(strict_types=1);


namespace App\Domain\Subscription\Interfaces;


use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\User;
use Ramsey\Uuid\UuidInterface;

interface PaymentGatewayServiceInterface
{
    /**
     * Create a subscription
     *
     * @param User $user
     * @param Subscription $subscription
     * @param array $subscriptionData
     */
    public function createSubscription(User $user, Subscription $subscription, array $subscriptionData): void;

    /**
     * Cancel subscription
     *
     * @param Subscription $subscription
     */
    public function cancelSubscription(Subscription $subscription): void;

    /**
     * Invoke this method when an user upgrade/downgrade their subscription
     *
     * @param Subscription $oldSubscription
     * @param Subscription $newSubscription
     */
    public function changeSubscriptionType(Subscription $oldSubscription, Subscription $newSubscription): void;

    /**
     * This method must be called in order to update the payment method
     *
     * @param Subscription[] $subscriptions
     * @param array $paymentMethodData
     */
    public function updateSubscriptionPaymentMethodData(array $subscriptions, array $paymentMethodData): void;
}