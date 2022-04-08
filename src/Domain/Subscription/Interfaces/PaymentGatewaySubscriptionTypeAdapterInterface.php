<?php

declare(strict_types=1);


namespace App\Domain\Subscription\Interfaces;


use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;

interface PaymentGatewaySubscriptionTypeAdapterInterface
{
    public function getPaymentGatewaySubscriptionType(SubscriptionTypeEnum $subscriptionTypeEnum): string;
    public function getSubscriptionType($paymentGatewaySubscription): SubscriptionTypeEnum;
}