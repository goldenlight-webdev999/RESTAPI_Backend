<?php

declare(strict_types=1);


namespace App\Domain\ContactRequest;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Infrastructure\Normalization\ObjectNormalizer as Scope;

class ContactRequest
{
    /**
     * @var UuidInterface
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $id;
    /*
     * @var EmailAddress
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $email;
    /**
     * @var bool
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $acceptCommercialCommunications;
    /**
     * @var \DateTimeInterface
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $dateAdded;

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
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isAcceptCommercialCommunications(): bool
    {
        return $this->acceptCommercialCommunications;
    }

    /**
     * @param bool $acceptCommercialCommunications
     */
    public function setAcceptCommercialCommunications(bool $acceptCommercialCommunications): void
    {
        $this->acceptCommercialCommunications = $acceptCommercialCommunications;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateAdded(): \DateTimeInterface
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTimeInterface $dateAdded
     */
    public function setDateAdded(\DateTimeInterface $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }
}