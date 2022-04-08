<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Customer
 * @package App\Infrastructure\Stripe\Entity
 * @ORM\Entity()
 * @ORM\Table(name="stripe_customer_subscription")
 */
class CustomerSubscription
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
     * @var string
     * @ORM\Column(type="string")
     */
    protected $subscriptionId;

    /**
     * @var Customer
     * @ORM\OneToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $customer;

    /**
     * @return UuidInterface
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @param mixed $subscriptionId
     */
    public function setSubscriptionId($subscriptionId): void
    {
        $this->subscriptionId = $subscriptionId;
    }
}