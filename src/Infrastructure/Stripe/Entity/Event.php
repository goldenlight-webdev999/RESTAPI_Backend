<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Event
 * @package App\Infrastructure\Stripe\Entity
 * @ORM\Entity()
 * @ORM\Table(name="log_stripe_event")
 */
class Event
{
    public const TYPE_INVOICE_PAYMENT_SUCCEEDED = 'invoice.payment_succeeded';
    public const TYPE_INVOICE_PAYMENT_FAILED = ' invoice.payment_failed';

    public const FIELD_DATA_SUBSCRIPTION = 'subscription';

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
    protected $foreignKey;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $apiVersion;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateCreated;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    protected $data;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
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
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * @param string $foreignKey
     */
    public function setForeignKey(string $foreignKey): void
    {
        $this->foreignKey = $foreignKey;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     */
    public function setApiVersion(string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateCreated(): \DateTimeImmutable
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTimeImmutable $dateCreated
     */
    public function setDateCreated(\DateTimeImmutable $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}