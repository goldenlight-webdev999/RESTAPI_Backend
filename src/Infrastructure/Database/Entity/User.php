<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Entity;

use App\Domain\User\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Database\Repository\UserRepository")
 */
class User extends \App\Domain\User\User implements UserInterface
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
     * @ORM\Column(unique=true, nullable=false)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $userName;

    /**
     * @var string;
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var bool;
     * @ORM\Column(type="boolean", options={"default": false}, nullable=false)
     */
    protected $acceptsCommercialCommunications;

    /**
     * @var array
     * @ORM\Column(type="json_array", options={"default": "[""ROLE_USER""]"}, nullable=false)
     */
    protected $roles;

    /**
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="user")
     */
    protected $subscriptions;

    /**
     * @var \DateTimeImmutable;
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;

    /**
     * @var \DateTimeImmutable;
     * @ORM\Column(type="datetimetz_immutable", options={"default": NULL}, nullable=true)
     */
    protected $dateVerified;


    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }


    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }
}
