<?php

declare(strict_types=1);


namespace App\Infrastructure\Paypal\Factory;


use App\Infrastructure\Paypal\Entity\Customer;
use App\Infrastructure\Paypal\Entity\CustomerSubscription;

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