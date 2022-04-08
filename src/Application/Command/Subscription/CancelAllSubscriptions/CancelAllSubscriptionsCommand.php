<?php

declare(strict_types=1);


namespace App\Application\Command\Subscription\CancelAllSubscriptions;


use App\Domain\User\User;

final class CancelAllSubscriptionsCommand
{
    private $user;
    private $upgrade;
    private $serviceType;

    public function __construct(User $user, bool $upgrade, string $serviceType = "stripe")
    {
        $this->user = $user;
        $this->upgrade = $upgrade;
        $this->serviceType = $serviceType;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function isUpgrade(): bool
    {
        return $this->upgrade;
    }

    public function serviceType(): string
    {
        return $this->serviceType;
    }

}
