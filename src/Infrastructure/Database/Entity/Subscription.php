<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Infrastructure\Database\Repository\LogCleanTaskRepository")
 */
class Subscription extends \App\Domain\Subscription\Subscription
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $paymentMethodMetadata;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $subscriptionStatus;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateStart;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateEnd;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateUpdated;
}