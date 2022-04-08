<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use DDD\Embeddable\EmailAddress;

/**
 * @ORM\Entity(repositoryClass="\App\Infrastructure\Database\Repository\ContactRequestRepository")
 */
class ContactRequest extends \App\Domain\ContactRequest\ContactRequest
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $id;
    /**
     * @var EmailAddress
     * @ORM\Embedded(class="DDD\Embeddable\EmailAddress")
     * @ORM\Column(unique=false, nullable=false)
     */
    protected $email;
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $acceptCommercialCommunications;
    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;
}