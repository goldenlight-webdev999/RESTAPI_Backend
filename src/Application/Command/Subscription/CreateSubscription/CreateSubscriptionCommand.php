<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\CreateSubscription;


use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\User;

final class CreateSubscriptionCommand
{
    private $user;
    private $subscriptionType;
    private $paymentMethodData;

    public function __construct(
        User $user,
        SubscriptionTypeEnum $subscriptionType,
        $subscriptionData
    )
    {
        $this->user = $user;
        $this->subscriptionType = $subscriptionType;
        $this->paymentMethodData = $subscriptionData;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return SubscriptionTypeEnum
     */
    public function getSubscriptionType(): SubscriptionTypeEnum
    {
        return $this->subscriptionType;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodData()
    {
        return $this->paymentMethodData;
    }
}