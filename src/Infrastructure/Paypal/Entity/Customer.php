<?php

declare(strict_types=1);


namespace App\Infrastructure\Paypal\Entity;

use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Customer
 * @package App\Infrastructure\Stripe\Entity
 * @ORM\Entity()
 * @ORM\Table(name="paypal_customer")
 */
class Customer
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Infrastructure\Database\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $customerKey;

    /**
     * @var CustomerSubscription
     * @ORM\OneToOne(targetEntity="CustomerSubscription", mappedBy="customer")
     */
    protected $subscription;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCustomerKey(): string
    {
        return $this->customerKey;
    }

    /**
     * @param string $customerKey
     */
    public function setCustomerKey(string $customerKey): void
    {
        $this->customerKey = $customerKey;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateAdded(): \DateTimeImmutable
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTimeImmutable $dateAdded
     */
    public function setDateAdded(\DateTimeImmutable $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return CustomerSubscription|null
     */
    public function getSubscription(): ?CustomerSubscription
    {
        return $this->subscription;
    }

    /**
     * @param CustomerSubscription $subscription
     */
    public function setSubscription(CustomerSubscription $subscription): void
    {
        $this->subscription = $subscription;
    }
}