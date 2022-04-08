<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\ChangeSubscriptionPaymentMethod;


use App\Domain\User\User;

final class ChangeSubscriptionPaymentMethodCommand
{
    private $user;
    private $paymentMethodData;

    /**
     * ChangeSubscriptionPaymentMethodCommand constructor.
     * @param User $user
     * @param array $paymentMethodMetadata
     */
    public function __construct(
        User $user,
        array $paymentMethodMetadata)
    {
        $this->user = $user;
        $this->paymentMethodData = $paymentMethodMetadata;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getPaymentMethodData(): array
    {
        return $this->paymentMethodData;
    }
}