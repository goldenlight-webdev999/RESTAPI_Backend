<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\ChangeSubscriptionPlan;


use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\User;

final class ChangeSubscriptionTypeCommand
{
    private $user;
    private $subscriptionType;

    /**
     * ChangeSubscriptionTypeCommand constructor.
     * @param User $user
     * @param SubscriptionTypeEnum $subscriptionType
     */
    public function __construct(
        User $user,
        SubscriptionTypeEnum $subscriptionType
    )
    {
        $this->user = $user;
        $this->subscriptionType = $subscriptionType;
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
}