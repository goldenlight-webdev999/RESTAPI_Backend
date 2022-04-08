<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Factory;


use App\Infrastructure\Stripe\Entity\Customer;
use App\Infrastructure\Stripe\Entity\CustomerSubscription;

final class CustomerSubscriptionFactory
{
    public function createSubscription(Customer $customer, string $subscriptionId): CustomerSubscription
    {
        $subscription = new CustomerSubscription();

        $subscription->setCustomer($customer);
        $subscription->setSubscriptionId($subscriptionId);

        return $subscription;
    }
}