<?php
declare(strict_types=1);


namespace App\Domain\Subscription\Repository;


use App\Domain\Subscription\Subscription;
use App\Domain\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

interface SubscriptionRepositoryInterface
{
    public function get(UuidInterface $uuid): Subscription;
    public function save(Subscription $subscription): void;
    public function delete(Subscription $subscription): void;
    public function newInstance(): Subscription;

    /**
     * "Live" means those subscriptions with the date_end greater than the current timestamp
     * @param User $user
     * @return ArrayCollection
     */
    public function getLiveSubscriptionsByUser(User $user): ArrayCollection;

    public function getSubscriptionsByUser(User $user): ArrayCollection;
}